<?php

namespace App\Modules\System;

use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends SystemController
{

    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        parent::__construct();
        $this->settingService = $settingService;
    }

    public function index()
    {
        return $this->view('setting.index', $this->settingService->loadViewData());
    }

    public function update(Request $request)
    {
        $update = $this->settingService->update($request);
        if ($update['status']) {
            flash_msg('success',__( 'Data Updated successfully' ));
            return redirect(route('system.setting.index'));
        } else {
            return $this->fail(__( 'Sorry, we could not Update the data' ) );
        }

    }

    public function getActivateSection()
    {
        return $this->view('activate-section.index');
    }

    public function updateActivateSection($id, Request $request)
    {
        // التحقق من الصلاحيات الإدارية
        if (!userCan('system.activate.update')) {
            return ($request->expectsJson() || $request->ajax())
                ? $this->fail(__('Unauthorized access.'), 403)
                : abort(403, __('Unauthorized access.'));
        }

        // التحقق من صحة المدخلات ومعرف القسم
        $request->validate([
            'name'  => 'nullable|string|max:100|regex:/^[a-zA-Z0-9_\-]+$/',
            'value' => 'nullable|string|max:20',
        ]);

        $sectionKey = $request->input('name', $id);
        if (empty($sectionKey) || !is_string($sectionKey) || !preg_match('/^[a-zA-Z0-9_\-]+$/', $sectionKey)) {
            return ($request->expectsJson() || $request->ajax())
                ? $this->response(false, 422, __('Invalid section identifier.'))
                : redirect()->back()->withErrors(__('Invalid section identifier.'));
        }

        $rawVal = $request->input('value', 'active');
        if (in_array($rawVal, ['active', '1', 1, true, 'true'], true)) {
            $normalizedValue = 'active';
        } elseif (in_array($rawVal, ['inactive', '0', 0, false, 'false'], true)) {
            $normalizedValue = 'inactive';
        } else {
            return ($request->expectsJson() || $request->ajax())
                ? $this->response(false, 422, __('Invalid status value.'))
                : redirect()->back()->withErrors(__('Invalid status value.'));
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $section = null;
            if (is_numeric($id)) {
                $section = \App\Models\Active_section::where('id', $id)->first();
            }
            if (!$section) {
                $section = \App\Models\Active_section::where('name', $sectionKey)->first();
            }

            if ($section) {
                $section->value = $normalizedValue;
                $section->save();
            } else {
                $maxId = (int) \App\Models\Active_section::max('id');
                $section = \App\Models\Active_section::create([
                    'id'    => $maxId + 1,
                    'name'  => $sectionKey,
                    'value' => $normalizedValue,
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return $this->success(__('Section status updated successfully.'), [
                    'section' => $section->name,
                    'value'   => $section->value,
                ]);
            }

            flash_msg('success', __('Section status updated successfully.'));
            return redirect()->route('system.activate.index');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            errorLog($e->getMessage());
            return ($request->expectsJson() || $request->ajax())
                ? $this->fail(__('Failed to update section.'), 500)
                : redirect()->back()->withErrors(__('Failed to update section.'));
        }
    }
}
