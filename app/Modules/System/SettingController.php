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
        return $this->view('setting.index', $this->settingService->loadViewData());
    }

    public function update(Request $request)
    {
        $update = $this->settingService->update($request);
        if ($update) {
            flash_msg('success',__( 'Data Updated successfully' ));
            return $this->success( __( 'Data Updated successfully' ),
                ['url'=>route('system.setting.index')]);
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
        dd($id,$request);
    }

}
