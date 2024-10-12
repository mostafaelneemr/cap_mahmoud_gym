<?php

namespace App\Models;


class Blog extends GlobalModel
{
    public $table = 'blog';
    public $primaryKey = 'id';

    public $fillable = ['id','image','thumb','name','title', 'slug','desc','status','author_id'];


    public function author()
    {
        return $this->belongsTo(User::class,'author_id','id');
    }
}
