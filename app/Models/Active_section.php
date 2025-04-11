<?php

namespace App\Models;


class Active_section extends GlobalModel
{
    public $table = 'activate_section';
    public $timestamps = false;
    public $primaryKey = 'id';

    public $fillable = ['name','value','created_at','updated_at'];

}
