<?php

namespace App\Models;


class Setting extends GlobalModel
{
    protected $table = "settings";

    protected $primaryKey = 'name';
    public $incrementing = false;
    protected $keyType = 'string';

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
