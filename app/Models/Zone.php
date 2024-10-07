<?php

namespace App\Models;



class Zone extends GlobalModel
{
    public $table = 'address_zone';
    public $timestamps = false;
    public $primaryKey = 'id';

    public $fillable = [
        'id', 'country_id','region_id',
        'name_en','name_ar','code','status','city_id',
        'lat','lng'];


    public function country()
    {
        return $this->belongsTo(Country::class,'country_id','id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class,'region_id','id');
    }

    public function city()
    {
        return $this->belongsTo(City::class,'city_id', 'id');
    }

}
