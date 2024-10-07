<?php


namespace App\Repositories\QueueLog;


use App\Models\QueueLog;
use App\Repositories\BaseRepository;

class QueueLogRepository extends BaseRepository
{
    protected $modeler = QueueLog::class;
}
