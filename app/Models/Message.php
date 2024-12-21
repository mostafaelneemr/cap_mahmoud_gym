<?php

namespace App\Models;

class Message extends GlobalModel
{
    public $table = 'message';
    public $timestamps = false;
    public $primaryKey = 'id';

    public $fillable = ['id', 'name','telephone','email','message','is_read'];

}
