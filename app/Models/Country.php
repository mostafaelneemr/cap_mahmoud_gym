<?php

namespace App\Models;



class Country extends GlobalModel
{
    public $table = 'address_country';
    public $timestamps = false;
    public $primaryKey = 'id';

    public $fillable = ['id', 'name_en','name_ar','iso_code_2','iso_code_3','status','sort_order','currency_id','thumb'];

    public function regions()
    {
        return $this->hasMany(Region::class);
    }
}
