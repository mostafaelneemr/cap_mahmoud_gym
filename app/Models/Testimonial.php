<?php

namespace App\Models;


class Testimonial extends GlobalModel
{
    public $table = 'testimonial';
    public $timestamps = false;
    public $primaryKey = 'id';

    public $fillable = ['id','image','name_en','name_ar','title_en','title_ar', 'text_en','text_ar','rating','status'];

}
