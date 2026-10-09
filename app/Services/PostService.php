<?php

namespace App\Services;

 use App\Repositories\Post\PostRepository;
 use App\Repositories\Setting\SettingRepository;

class PostService extends BaseService
{
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        parent::__construct();
        $this->postRepository = $postRepository;
    }

    /**
     * Get active posts by type.
     */
    public function getPostWithItemsByType()
    {
        return $this->postRepository->getPostWithItemsByType();
    }

    public function getPostsActive($type)
    {
        return $this->postRepository->getPostsActive($type);
    }

    public function getPostsWithItemsActive($type)
    {
        return $this->postRepository->getPostsWithItemsActive($type);
    }

}
