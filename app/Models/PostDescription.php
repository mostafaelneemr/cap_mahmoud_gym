<?php

namespace App\Models;


class PostDescription extends GlobalModel
{
    public $table = 'post_description';
//    protected $primaryKey = null;
//    public $incrementing = false;
    public $timestamps = false;
    protected $fillable =[
        'post_id',
        'language_id',
        'title',
        'description',
        'meta_title',
        'meta_description',
        'meta_keyword',
        'custom_title_1',
        'custom_title_2',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
