<?php

namespace App\Services;



use App\Repositories\Post\PostRepository;


class WebsiteService extends BaseService
{
    protected $postRepository;

    public function __construct(PostRepository  $postRepository)
    {
        parent::__construct();
        $this->postRepository = $postRepository;
    }






}
