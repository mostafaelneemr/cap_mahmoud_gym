<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends GlobalModel
{
    use SoftDeletes;

    protected $table = 'posts';
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(PostItem::class, 'post_id')->orderBy('sort', 'asc');
    }

    public function activeItems()
    {
        return $this->hasMany(PostItem::class, 'post_id')
            ->where('status', 'active')
            ->orderBy('sort', 'asc');
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
}
