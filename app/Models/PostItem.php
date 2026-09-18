<?php

namespace App\Models;

class PostItem extends GlobalModel
{
    protected $table = 'post_items';

    /**
     * All columns are mass-assignable (guarded = []).
     * Full column list (including manually added `email`):
     *   id, post_id, email, title_ar, title_en, subtitle_ar, subtitle_en,
     *   description_ar, description_en, image, extra_image, link,
     *   btn_text_ar, btn_text_en, rating, price, sort, status,
     *   created_at, updated_at
     *
     * NOTE: The `email` column requires the following manual SQL before use:
     *   ALTER TABLE `post_items` ADD COLUMN `email` VARCHAR(255) NULL DEFAULT NULL AFTER `post_id`;
     */
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
