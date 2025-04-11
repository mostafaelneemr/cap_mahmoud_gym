<?php

namespace App\Models;


class ChooseItem extends GlobalModel
{
    public $table = 'choose_item';
    public $timestamps = false;
    public $primaryKey = 'id';

    public $fillable = ['id','image','title_en','title_ar', 'text_en','text_ar','status'];

}
