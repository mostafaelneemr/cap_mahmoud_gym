<?php

namespace App\Modules\System;

use App\Services\SocialLinkService;
use Illuminate\Http\Request;

class SocialLinkController extends SystemController
{
    protected $socialLinkService;

    public function __construct(SocialLinkService $socialLinkService)
    {
        parent::__construct();
        $this->socialLinkService = $socialLinkService;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->socialLinkService->loadDataTableData();
        }
        return $this->view('social-links.index', $this->socialLinkService->loadViewData());
    }

    public function create()
    {
        return $this->view('social-links.create', $this->socialLinkService->create());
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'order' => 'integer'
        ]);

        $stored = $this->socialLinkService->store($data);

        if ($stored) {
            flash_msg('success', __('Data Added successfully'));
            return $this->success(__('Data added successfully'), ['url' => route('system.social-links.index')]);
        } else {
            return $this->fail(__('Sorry, we could not add the data'));
        }
    }

    public function edit($id)
    {
        return $this->view('social-links.create', $this->socialLinkService->edit($id));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'order' => 'integer'
        ]);

        $updated = $this->socialLinkService->update($id, $data);

        if ($updated) {
            flash_msg('success', __('Data Updated successfully'));
            return $this->success(__('Data Updated successfully'), ['url' => route('system.social-links.index')]);
        } else {
            return $this->fail(__('Sorry, we could not Update the data'));
        }
    }
}
