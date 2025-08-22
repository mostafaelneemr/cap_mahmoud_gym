<?php

namespace App\Services;


use App\Repositories\Site\SiteRepository;
use Illuminate\Support\Facades\DB;
use Datatables;


class SiteService extends BaseService
{
    protected $siteRepository;

    public function __construct(SiteRepository $siteRepository)
    {
        parent::__construct();
        $this->siteRepository = $siteRepository;
    }

    public function loadViewData(): array
    {
        $this->pageTitle(__('Site Data'));
        $this->tableColumns([
            __('ID'),
            __('icon'),
            __('Title'),
            __('Link'),
            __('Status'),
            __('Action'),
        ]);

        $this->jsColumns([
            'id' => 'site.id',
            'icon' => 'site.icon',
            'title' => 'site.title',
            'link_url' => 'site.link_url',
            'status' => 'site.status',
            'action' => '',
        ]);

        $this->filterIgnoreColumns(['action']);
        $this->addButton('system.site.create','Add link');
        return $this->retunData;
    }

    public function loadDataTableData()
    {
        return Datatables::eloquent($this->siteRepository->getDataTableQuery())
            ->addColumn('id', '{{$id}}')
            ->addColumn('icon', function ($data) {
                return $data->icon;
            })
            ->addColumn('title', '{{$title}}')
            ->addColumn('link_url', '{{$link_url}}')
            ->addColumn('status', function($data) {
                return status_icon($data->status);
            })
            ->addColumn('action', function($data) {
                $this->actionButtons(datatable_menu_edit(route('system.site.edit', $data->id), 'system.site.edit'));
                return $this->actionButtonsRender($this->siteRepository->modelPath(), $data->id);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function create(): array
    {
        $this->pageTitle('Create Link');
        $this->breadcrumb('Home');
        $this->breadcrumb('Links', 'system.site.index');

        return $this->retunData;
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();


            $data = [
                'title' => $request->title,
                'link_url' => $request->link_url,
                'icon' => $request->icon,
                'status' => $request->status,
            ];

            $store = $this->siteRepository->store($data);
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
        $this->pageTitle('Update Link');
        $this->breadcrumb('Links', 'system.site.index');

        $this->otherData(['result' => $this->siteRepository->find($id)]);
        return $this->retunData;
    }

    public function update($request,$id)
    {
        try {
            DB::beginTransaction();

            $data = [
                'title' => $request->title,
                'link_url' => $request->link_url,
                'icon' => $request->icon,
                'status' => $request->status,
            ];

            $update = $this->siteRepository->update($data,$id);
            DB::commit();
            return $update;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }
    }

}
