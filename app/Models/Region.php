<?php

namespace App\Models;



class Region extends GlobalModel
{
    public $table = 'address_region';
    public $timestamps = false;
    public $primaryKey = 'id';

    public $fillable = ['id', 'county_id','name_en','name_ar','status'];


    public function country()
    {
        return $this->belongsTo(Country::class,'county_id','id');
    }
    public function cities() {
        return $this->hasMany(City::class,'region_id','id');
    }
}
