<?php

namespace App\Models;

class SocialLink extends GlobalModel
{
    protected $table = 'social_links';
    
    protected $fillable = [
        'title',
        'url',
        'icon',
        'is_active',
        'order',
    ];
}
