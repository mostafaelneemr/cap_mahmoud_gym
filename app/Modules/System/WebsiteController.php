<?php

namespace App\Modules\System;

use App\Models\Post;
use App\Models\PostItem;
use App\Models\Setting;
use App\Services\WebsiteService;
use Illuminate\Http\Request;

class WebsiteController extends SystemController
{
    protected $websiteService;

    public function __construct(WebsiteService $websiteService)
    {
        parent::__construct();
        $this->websiteService = $websiteService;
    }

    /**
     * Display the CMS landing page management interface with all sections, items, and settings.
     */
    public function index(Request $request)
    {
        $sections = Post::with('items')->get()->keyBy('type');
        $settings = Setting::pluck('value', 'name')->toArray();

        $this->viewData['sections']           = $sections;
        $this->viewData['settings']           = $settings;

        // Named shortcuts for each section in the Blade view
        $this->viewData['hero']               = $sections->get('hero');
        $this->viewData['transformationHero'] = $sections->get('transformation_hero');
        $this->viewData['joinHero']           = $sections->get('join_hero');
        $this->viewData['contactHero']        = $sections->get('contact_hero');
        $this->viewData['about']              = $sections->get('about');
        $this->viewData['service']            = $sections->get('service');
        $this->viewData['whyUs']              = $sections->get('why_us');
        $this->viewData['quote']              = $sections->get('quote');
        $this->viewData['transformation']     = $sections->get('transformation');
        $this->viewData['review']             = $sections->get('review');

        $this->viewData['pageTitle']          = __('Landing Page Management');

        return $this->view('website.index', $this->viewData);
    }

    /**
     * Update or create a parent section in the posts table by type.
     */
    public function updateSection(Request $request, string $type)
    {
        $allowedTypes = [
            'hero',
            'about',
            'service',
            'why_us',
            'quote',
            'transformation_hero',
            'transformation',
            'review',
            'join_hero',
            'contact_hero',
        ];

        if (!in_array($type, $allowedTypes, true)) {
            return $this->fail(__('Invalid section type specified.'));
        }

        $data = $request->except(['_token', 'image']);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $uploadDir = public_path('upload/posts');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $imageFile = $request->file('image');
            $nameGen = hexdec(uniqid()) . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move($uploadDir, $nameGen);
            $data['image'] = 'upload/posts/' . $nameGen;
        }

        Post::updateOrCreate(['type' => $type], $data
        );

        return $this->success(__('Section updated successfully'), ['url' => route('system.website.index')]);
    }

    /**
     * Store a new child post_item tied to a parent post.
     */
    public function storePostItem(Request $request)
    {
        $postId = $request->input('post_id');

        if (!$postId) {
            $postType = $request->input('post_type', $request->input('type'));
            if ($postType) {
                $parent = Post::firstOrCreate(
                    ['type' => $postType],
                    ['status' => 'active']
                );
                $postId = $parent->id;
            }
        }

        if (!$postId) {
            return $this->fail(__('Parent section could not be resolved.'));
        }

        $data = $request->except(['_token', 'image', 'extra_image', 'post_type', 'type']);
        $data['post_id'] = $postId;

        if ($request->filled('rate') && !$request->filled('rating')) {
            $data['rating'] = $request->input('rate');
        }

        $uploadDir = public_path('upload/posts');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imageFile = $request->file('image');
            $nameGen = hexdec(uniqid()) . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move($uploadDir, $nameGen);
            $data['image'] = 'upload/posts/' . $nameGen;
        }

        if ($request->hasFile('extra_image') && $request->file('extra_image')->isValid()) {
            $extraFile = $request->file('extra_image');
            $nameGen = hexdec(uniqid()) . '.' . $extraFile->getClientOriginalExtension();
            $extraFile->move($uploadDir, $nameGen);
            $data['extra_image'] = 'upload/posts/' . $nameGen;
        }

        PostItem::create($data);

        return $this->success(__('Item added successfully'), ['url' => route('system.website.index')]);
    }

    /**
     * Update an existing child post_item by ID.
     */
    public function updatePostItem(Request $request, int|string $id)
    {
        $item = PostItem::findOrFail($id);

        $data = $request->except(['_token', 'image', 'extra_image', 'post_type', 'type']);

        if ($request->filled('rate') && !$request->filled('rating')) {
            $data['rating'] = $request->input('rate');
        }

        $uploadDir = public_path('upload/posts');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imageFile = $request->file('image');
            $nameGen = hexdec(uniqid()) . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move($uploadDir, $nameGen);
            $data['image'] = 'upload/posts/' . $nameGen;
        }

        if ($request->hasFile('extra_image') && $request->file('extra_image')->isValid()) {
            $extraFile = $request->file('extra_image');
            $nameGen = hexdec(uniqid()) . '.' . $extraFile->getClientOriginalExtension();
            $extraFile->move($uploadDir, $nameGen);
            $data['extra_image'] = 'upload/posts/' . $nameGen;
        }

        $item->update($data);

        return $this->success(__('Item updated successfully'), ['url' => route('system.website.index')]);
    }

    /**
     * Delete a child post_item by ID via AJAX.
     */
    public function destroyPostItem(int|string $id)
    {
        $item = PostItem::findOrFail($id);
        $item->delete();

        return $this->success(__('Item deleted successfully'));
    }

    /**
     * Update global website contact info, logo, and social links.
     */
    public function updateSettings(Request $request)
    {
        $data = $request->except(['_token']);
        $uploadDir = public_path('upload/setting');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($request->files as $key => $file) {
            if ($request->hasFile($key) && $request->file($key)->isValid()) {
                $uploadedFile = $request->file($key);
                $nameGen = hexdec(uniqid()) . '.' . $uploadedFile->getClientOriginalExtension();
                $uploadedFile->move($uploadDir, $nameGen);
                $data[$key] = 'upload/setting/' . $nameGen;
            }
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['name' => $key],
                [
                    'value'         => is_array($value) ? serialize($value) : ($value ?? ''),
                    'group_name'    => 'landing_page_home',
                    'shown_name_ar' => $key,
                    'shown_name_en' => $key,
                    'input_type'    => 'text',
                    'sort'          => 0,
                ]
            );
        }

        return $this->success(__('Settings saved successfully'), ['url' => route('system.website.index')]);
    }
}
