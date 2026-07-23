<?php

namespace App\Services;

use App\Repositories\SocialLink\SocialLinkRepository;
use Illuminate\Support\Facades\DB;
use Datatables;

class SocialLinkService extends BaseService
{
    protected $socialLinkRepository;

    public function __construct(SocialLinkRepository $socialLinkRepository)
    {
        parent::__construct();
        $this->socialLinkRepository = $socialLinkRepository;
    }

    public function loadViewData(): array
    {
        $this->pageTitle(__('Social Links'));
        $this->tableColumns([
            __('ID'),
            __('Title'),
            __('URL'),
            __('Icon'),
            __('Status'),
            __('Order'),
            __('Action'),
        ]);

        $this->jsColumns([
            'id' => 'social_links.id',
            'title' => 'social_links.title',
            'url' => 'social_links.url',
            'icon' => 'social_links.icon',
            'status' => 'social_links.is_active',
            'order' => 'social_links.order',
            'action' => '',
        ]);

        $this->filterIgnoreColumns(['action']);
        $this->addButton('system.social-links.create', 'Add Link');
        return $this->retunData;
    }

    public function loadDataTableData()
    {
        return Datatables::eloquent($this->socialLinkRepository->getDataTableQuery())
            ->addColumn('id', '{{$id}}')
            ->addColumn('title', function ($data) {
                return $data->title;
            })
            ->addColumn('url', function ($data) {
                return $data->url;
            })
            ->addColumn('icon', function ($data) {
                return '<i class="fa-solid ' . ($data->icon ?? 'fa-link') . '"></i>';
            })
            ->addColumn('status', function($data) {
                return status_icon($data->is_active);
            })
            ->addColumn('order', function($data) {
                return $data->order;
            })
            ->addColumn('action', function($data) {
                $this->actionButtons(datatable_menu_edit(route('system.social-links.edit', $data->id), 'system.social-links.edit'));
                return $this->actionButtonsRender($this->socialLinkRepository->modelPath(), $data->id);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function create(): array
    {
        $this->pageTitle('Create Social Link');
        $this->breadcrumb('Home');
        $this->breadcrumb('Social Links', 'system.social-links.index');
        return $this->retunData;
    }

    public function store(array $data)
    {
        try {
            DB::beginTransaction();
            $store = $this->socialLinkRepository->store($data);
            DB::commit();
            return $store;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }
    }

    public function edit($id): array
    {
        $link = $this->socialLinkRepository->find($id);

        $this->pageTitle('Update Social Link');
        $this->breadcrumb('Social Links', 'system.social-links.index');

        $this->otherData([
            'socialLink' => $link
        ]);
        return $this->retunData;
    }

    public function update($id, array $data)
    {
        try {
            DB::beginTransaction();
            $update = $this->socialLinkRepository->update( $data,$id);
            DB::commit();
            return $update;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }
    }
}
