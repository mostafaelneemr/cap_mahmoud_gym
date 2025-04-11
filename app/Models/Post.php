<?php

namespace App\Models;


class Post extends GlobalModel
{
    public $table = 'post';
    public $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id', 'image_url', 'status', 'thumb', 'added_at',];

    public function description()
    {
        return $this->hasMany(PostDescription::class, 'post_id');
    }
}
