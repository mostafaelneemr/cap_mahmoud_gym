<?php

namespace App\Models;


class Site extends GlobalModel
{
    protected $table = "site";
    public $timestamps = true;

    protected $fillable = ['title', 'icon', 'link_url', 'status'];

}
