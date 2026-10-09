<?php

namespace App\Repositories\Post;

use App\Enums\DefaultStatus;
use App\Enums\PostTypeEnum;
use App\Enums\StatusEnum;
use App\Models\Post;
use App\Repositories\BaseRepository;

class PostRepository extends BaseRepository
{
    protected $modeler = Post::class;

    /**
     * Query for datatables / CMS dashboard listing.
     */
    public function getDataTableQuery(?string $type = null)
    {
        $query = $this->modeler->newQuery();

        if ($type) {
            $query->where('type', $type);
        }

        return $query->select([
            'id',
            'type',
            'title',
            'subtitle',
            'image',
            'price',
            'sort_order',
            'status',
            'created_at',
        ]);
    }

    /**
     * Get active posts by type, ordered by sort_order.
     */
    public function getActiveByType(string|PostTypeEnum $type)
    {
        $typeValue = $type instanceof PostTypeEnum ? $type->value : $type;

        return $this->modeler
            ->where('type', $typeValue)
            ->where('status', DefaultStatus::Active->value)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get all active posts grouped by type.
     */
    public function getActiveGroupedByType(): array
    {
        $posts = $this->modeler
            ->where('status', DefaultStatus::Active->value)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        $grouped = [];
        foreach (PostTypeEnum::cases() as $case) {
            $grouped[$case->value] = [];
        }

        foreach ($posts as $post) {
            $type = $post->type instanceof PostTypeEnum ? $post->type->value : (string) $post->type;
            $grouped[$type][] = $post;
        }

        return $grouped;
    }

    public function getPostWithItemsByType()
    {
        return $this->modeler->with('items')->get()->keyBy('type');
    }

    public function getPostsActive($type)
    {
        return $this->modeler->where('type', $type)
            ->where(function ($q) {
                $q->where('status', DefaultStatus::Active->value)->orWhereNull('status');
            })->first();
    }

    public function getPostsWithItemsActive($type)
    {
        return $this->modeler->with(['items' => function ($query) {
            $query->where(function ($q) {
                $q->where('status', DefaultStatus::Active->value)->orWhereNull('status');
            })->orderBy('sort', 'asc');
        }])
            ->where('type', $type)
            ->where(function ($q) {
                $q->where('status', DefaultStatus::Active->value)->orWhereNull('status');
            })->first();
    }
}
