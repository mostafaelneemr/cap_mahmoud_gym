<?php

namespace App\Models;

class PostItem extends GlobalModel
{
    protected $table = 'post_items';
    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return asset($this->image);
    }

    public function getExtraImageUrlAttribute(): ?string
    {
        if (empty($this->extra_image)) {
            return null;
        }

        if (filter_var($this->extra_image, FILTER_VALIDATE_URL)) {
            return $this->extra_image;
        }

        return asset($this->extra_image);
    }
}
