<?php

namespace App\Repositories\Message;


use App\Enums\ReadMessageEnum;
use App\Models\Message;
use App\Repositories\BaseRepository;

class MessageRepository extends BaseRepository
{
    protected $modeler = Message::class;

    public function getDataTableQuery()
    {
        return $this->modeler->select('*')->where('is_read', ReadMessageEnum::no->value);
    }
}
