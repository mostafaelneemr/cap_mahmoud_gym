<?php

namespace App\Models;



class Post extends GlobalModel
{
    protected $table = 'posts';
    public $timestamps = true;
    public $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'image_url',
        'extra_image_url',
    ];

    /**
     * Get full image URL accessor.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'storage/')) {
            return asset($this->image);
        }

        return asset($this->image);
    }

    /**
     * Get full extra_image URL accessor (e.g., Before image for transformations).
     */
    public function getExtraImageUrlAttribute(): ?string
    {
        if (empty($this->extra_image)) {
            return null;
        }

        if (filter_var($this->extra_image, FILTER_VALIDATE_URL)) {
            return $this->extra_image;
        }

        if (str_starts_with($this->extra_image, 'storage/')) {
            return asset($this->extra_image);
        }

        return asset($this->extra_image);
    }

    /**
     * Scope: Active items only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Filter by post type.
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Ordered by sort_order ascending then id descending.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
    }
}
