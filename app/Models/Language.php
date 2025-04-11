<?php

namespace App\Models;



class Language extends GlobalModel
{
    public $table = 'language';
    public $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable =['name','code','locale','image','directory','sort_order','status', 'image',];
    public function getImageAttribute()
    {
          $image = !empty($this->attributes['image']) ? env('front_image_url') . $this->attributes['image'] : null;
          return  $image;
    }
}
