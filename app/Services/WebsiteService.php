<?php

namespace App\Services;


use App\Filters\MessageRead;
use App\Repositories\Message\MessageRepository;
use App\Repositories\Post\PostRepository;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Datatables;


class WebsiteService extends BaseService
{
    protected $postRepository;

    public function __construct(PostRepository  $postRepository)
    {
        parent::__construct();
        $this->postRepository = $postRepository;
    }






}
