<?php

namespace App\Models;

use App\Models\GlobalModel;

class Setting extends GlobalModel
{
    protected $table = "settings";
    public $timestamps = true;

    protected $fillable = [
        'name',
        'value'
    ];

    public function getOptionListAttribute($value){
        $value = @unserialize($value);
        if(!is_array($value)){
            return [];
        }
        return $value;
    }
}
