<?php

namespace App\Repositories\Message;


use App\Models\Message;
use App\Repositories\BaseRepository;

class MessageRepository extends BaseRepository
{
    protected $modeler = Message::class;

    public function getDataTableQuery()
    {
        return $this->modeler->select('*');
    }
}
