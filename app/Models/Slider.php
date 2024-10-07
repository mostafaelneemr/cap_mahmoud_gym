<?php

namespace App\Models;


class Slider extends GlobalModel
{
    public $table = 'slider';
    public $timestamps = false;
    public $primaryKey = 'id';

    public $fillable = [
        'id','image','title_en','title_ar',
        'sub_title_en','sub_title_ar','button_en','button_ar','type','status'
    ];

}
