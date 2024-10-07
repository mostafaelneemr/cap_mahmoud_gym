<?php

namespace App\Models;



class City extends GlobalModel
{
    public $table = 'address_city';
    public $timestamps = false;
    public $primaryKey = 'id';

    public $fillable = ['id', 'region_id','name_en','name_ar','lat','lng'];


    public function region()
    {
        return $this->belongsTo(Region::class,'region_id', 'id');
    }

    public function zones()
    {
        return $this->hasMany(Zone::class,'city_id', 'id');
    }
}
