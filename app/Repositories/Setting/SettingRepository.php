<?php

namespace App\Repositories\Setting;

use App\Models\Setting;
use App\Repositories\BaseRepository;

class SettingRepository extends BaseRepository
{
    protected $modeler = Setting::class;

    public function getDataTableQuery()
    {
        return  $this->modeler::select('group_name')
            ->groupBy('group_name')
            ->where('is_visible','yes')
            ->where('group_name','!=','staff_app')
            ->get();
    }

    public function getSetting($value)
    {
        return $this->modeler::where('group_name',$value->group_name)->where('is_visible','yes')->orderBy('sort','ASC')->get();
    }

    public function getNameSetting($name)
    {
        return $this->modeler::whereName($name)->first();
    }
}
