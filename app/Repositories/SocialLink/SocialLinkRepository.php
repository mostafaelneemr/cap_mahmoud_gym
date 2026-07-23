<?php

namespace App\Repositories\SocialLink;

use App\Enums\StatusEnum;
use App\Models\SocialLink;
use App\Repositories\BaseRepository;

class SocialLinkRepository extends BaseRepository
{
    protected $modeler = SocialLink::class;

    public function getDataTableQuery()
    {
        return $this->modeler->select(['id', 'title', 'url', 'icon', 'is_active', 'order', 'created_at']);
    }

    public function getActiveLinksOrdered()
    {
        return $this->modeler->where('is_active', StatusEnum::Enable->value)->orderByAsc('order')->get();
    }
}
