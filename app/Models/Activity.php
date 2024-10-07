<?php

namespace App\Models;
use  Spatie\Activitylog\Models\Activity as ActivityModel;
class Activity extends ActivityModel
{
    public $timestamps = false;

    protected static function booted()
     {
         parent::booted();
         static::creating(function ($item) {
            unset($item['batch_uuid']);
            unset($item['method']);
             $item->url = url()->current();
             $item->ip = getRealIP();
             $item->user_agent = getUserAgent();
         });
     }
}
