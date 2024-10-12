<?php

namespace App\Modules\System;

use App\Models\Setting;
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
        $this->viewData['pageTitle'] = __('Setting');

        $settingGroups = Setting::select('group_name')
            ->groupBy('group_name')
            ->where('is_visible','yes')
            ->where('group_name','!=','staff_app')
            ->get();
        $setting = [];
        foreach ($settingGroups as $key => $value) {
            $setting[] = Setting::where('group_name',$value->group_name)->where('is_visible','yes')->orderBy('sort','ASC')->get();
        }


        $this->viewData['settingGroups'] = $settingGroups;
        $this->viewData['setting'] = $setting;

        return $this->view('setting.index',$this->viewData);
//        return $this->view('setting.index', $this->settingService->loadViewData());
    }

    public function update(Request $request)
    {
        $update = $this->settingService->update($request);
        if ($update) {
            flash_msg('success',__( 'Data Updated successfully' ));
            return $this->success( __( 'Data Updated successfully' ),
                ['url'=> route('system.setting.index') ]);
        } else {
            return $this->fail(__( 'Sorry, we could not Update the data' ) );
        }

    }

}
