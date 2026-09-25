<?php

namespace App\Modules\Web;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\JoinUsService;
use App\Services\LandingPageService;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebController extends Controller
{
    protected LandingPageService $landingPageService;
    protected MessageService $messageService;
    protected JoinUsService $joinUsService;

    public function __construct(
        LandingPageService $landingPageService,
        MessageService $messageService,
        JoinUsService $joinUsService
    ) {
        $this->landingPageService = $landingPageService;
        $this->messageService = $messageService;
        $this->joinUsService = $joinUsService;
    }

    /**
     * Resolve current interface language ('en' or 'ar').
     */
    protected function getLanguage(): string
    {
        $locale = strtolower(app()->getLocale());
        return in_array($locale, ['ar', 'en'], true) ? $locale : 'en';
    }

    /**
     * Main Home Landing Page
     * Route: GET / (web.home)
     */
    public function home(Request $request): View
    {
        $lang = $this->getLanguage();

        $hero    = $this->landingPageService->getSectionData('hero', $lang);
        $about   = $this->landingPageService->getSectionData('about', $lang);
        $service = $this->landingPageService->getSectionData('services', $lang);
        $whyUs   = $this->landingPageService->getSectionData('why_us', $lang);
        $quote   = $this->landingPageService->getSectionData('quote', $lang);
        $global  = $this->landingPageService->getGlobalData($lang);

        $pageTitle = ($global['site_title'] ?? 'Mahmoud Shaltout') . ' - ' . __('Home');

        return view('web.index', compact('hero', 'about', 'service', 'whyUs', 'quote', 'global', 'pageTitle'));
    }

    /**
     * Transformations & Customer Reviews Page
     * Route: GET /transformation (web.transformations)
     */
    public function transformations(Request $request): View
    {
        $lang = $this->getLanguage();

        $transHero      = $this->landingPageService->getTransformationPageData('hero', $lang);
        $transformation = $this->landingPageService->getTransformationPageData('list', $lang);
        $reviews        = $this->landingPageService->getTransformationPageData('reviews', $lang);
        $global         = $this->landingPageService->getGlobalData($lang);

        $pageTitle = ($global['site_title'] ?? 'Mahmoud Shaltout') . ' - ' . __('Transformations & Reviews');

        return view('web.transformations', compact('transHero', 'transformation', 'reviews', 'global', 'pageTitle'));
    }

    /**
     * Join Us Application Page
     * Route: GET /join-us (web.join-us)
     */
    public function joinUs(Request $request): View
    {
        $lang = $this->getLanguage();

        $joinHero = $this->landingPageService->getJoinUsPageData('hero', $lang);
        $joinForm = $this->landingPageService->getJoinUsPageData('form_info', $lang);
        $global   = $this->landingPageService->getGlobalData($lang);

        $pageTitle = ($global['site_title'] ?? 'Mahmoud Shaltout') . ' - ' . __('Join Us');

        return view('web.join_us', compact('joinHero', 'joinForm', 'global', 'pageTitle'));
    }

    /**
     * Contact Us Page
     * Route: GET /contact (web.contact)
     */
    public function contact(Request $request): View
    {
        $lang = $this->getLanguage();

        $contactHero = $this->landingPageService->getContactPageData('hero', $lang);
        $contactData = $this->landingPageService->getContactPageData('info', $lang);
        $global      = $this->landingPageService->getGlobalData($lang);

        $pageTitle = ($global['site_title'] ?? 'Mahmoud Shaltout') . ' - ' . __('Contact Us');

        return view('web.contact_us', compact('contactHero', 'contactData', 'global', 'pageTitle'));
    }

    /**
     * Handle Join Us form submission
     * Route: POST /join-us (web.join-us.store)
     */
    public function submitJoinUs(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:50',
            'age'            => 'nullable|numeric',
            'country'        => 'nullable|string|max:100',
            'governorate'    => 'nullable|string|max:100',
            'training_level' => 'nullable|string|max:100',
            'goal'           => 'nullable|string|max:1000',
            'injuries'       => 'nullable|string|max:255',
            'injury_details' => 'nullable|string|max:1000',
            'reason'         => 'nullable|string|max:1000',
            'routine'        => 'nullable|string|max:2000',
        ]);

        $stored = $this->joinUsService->store($validated);

        if ($request->ajax() || $request->wantsJson()) {
            if (!$stored) {
                return response()->json([
                    'status'  => false,
                    'message' => __('Something went wrong. Please try again later.'),
                ], 500);
            }

            return response()->json([
                'status'  => true,
                'message' => __('Your application has been received! We will contact you shortly.'),
            ], 200);
        }

        if ($stored) {
            return back()->with('success', __('Your application has been received! We will contact you shortly.'));
        }

        return back()->withInput()->with('error', __('Something went wrong. Please try again later.'));
    }

    /**
     * Handle Contact form submission
     * Route: POST /contact (web.contact.store)
     */
    public function submitContact(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone'     => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:50',
            'message'   => 'required|string|max:3000',
        ]);

        // Normalize telephone if phone was passed in the request
        if (!$request->filled('telephone') && $request->filled('phone')) {
            $request->merge(['telephone' => $request->input('phone')]);
        }

        $stored = $this->messageService->store($request);

        if ($request->ajax() || $request->wantsJson()) {
            if (!$stored) {
                return response()->json([
                    'status'  => false,
                    'message' => __('Something went wrong. Please try again later.'),
                ], 500);
            }

            return response()->json([
                'status'  => true,
                'message' => __('Your message has been sent successfully! We will get back to you soon.'),
            ], 200);
        }

        if ($stored) {
            return back()->with('success', __('Your message has been sent successfully! We will get back to you soon.'));
        }

        return back()->withInput()->with('error', __('Something went wrong. Please try again later.'));
    }

    /**
     * Handle customer review submission
     * Route: POST /reviews (web.reviews.store)
     */
    public function submitReview(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'rating'  => 'required|numeric|min:1|max:5',
            'message' => 'required|string|max:2000',
        ]);

        $post = Post::firstOrCreate(
            ['type' => 'review'],
            [
                'status'   => 'active',
                'title_en' => 'Our Customer Reviews',
                'title_ar' => 'آراء عملائنا',
            ]
        );

        try {
            $post->items()->create([
                'email'          => $validated['email'],
                'title_en'       => $validated['name'],
                'title_ar'       => $validated['name'],
                'description_en' => $validated['message'],
                'description_ar' => $validated['message'],
                'rating'         => $validated['rating'],
                'status'         => 'inactive', // Moderated
                'sort'           => 0,
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'  => true,
                    'message' => __('Thank you for your review! It will appear after moderation.'),
                ], 200);
            }

            return back()->with('success', __('Thank you for your review! It will appear after moderation.'));
        } catch (\Exception $e) {
            errorLog($e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => __('Something went wrong. Please try again later.'),
                ], 500);
            }

            return back()->withInput()->with('error', __('Something went wrong. Please try again later.'));
        }
    }
}
