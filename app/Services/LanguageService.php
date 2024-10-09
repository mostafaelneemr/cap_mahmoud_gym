<?php

namespace App\Services;

use App\Models\Language;
use App\Repositories\Language\LanguageRepository;
use Datatables;
use Illuminate\Support\Facades\DB;

class LanguageService extends BaseService
{
    protected $language_repository;

    public function __construct(LanguageRepository $language_repository)
    {
        parent::__construct();
        $this->language_repository = $language_repository;
    }
    public function loadViewData(): array
    {
        $this->pageTitle('Languages');
        $this->tableColumns([
            __('ID'),
            __('Name'),
            __('Code'),
            __('Image'),
            __('Status'),
            __('Sort Order'),
            __('Action'),
        ]);

        $this->jsColumns([
            'id' => 'language.id',
            'name' => 'language.name',
            'code' => 'language.code',
            'image' => 'language.image',
            'status' => 'language.status',
            'sort_order' => 'language.sort_order',
            'action' => ''
        ]);

        $this->breadcrumb('Language', 'system.language.index');
        $this->addButton('system.language.create');
        $this->filterIgnoreColumns(['action','status','image']);
        return $this->retunData;
    }
    /**
     * @return mixed
     */
    public function loadDataTableData()
    {
        $eloquentData = $this->language_repository->getDataTableQuery();

        return Datatables::eloquent($eloquentData)
            ->addColumn('id', '{{$id}}')
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('code', function ($data) {
                return $data->code;
            })
            ->addColumn('image', function ($data) {
                return $data->image;
//                if ($data->image) {
//                    return datatableImageFullPath($data->image);
//                }
//                return '--';
            })

            ->addColumn('status', function ($data) {
                return status_icon($data->status);
            })
            ->addColumn('sort_order', function ($data) {
                return $data->sort_order;
            })
            ->editColumn('action', function ($data) {
                if (userCan('system.language.edit')) {
                    $this->actionButtons(datatable_menu_edit(route('system.language.edit', $data->id), 'system.language.edit'));
                }
                return $this->actionButtonsRender($this->language_repository->modelPath(), $data->id);
            })
            ->escapeColumns([])
            ->setRowId(function ($data) {
                return 'tr_' . $data->id;
            })
            ->make(true);
    }
    public function create(): array
    {
        $this->pageTitle('Create Language');
        $this->breadcrumb('Language', 'system.language.index');
        return $this->retunData;
    }
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $flagIcon = null;
            if (isset($request->image) && !empty($request->image)) {
                $flagIcon = $this->uploadFileS3($request->image, 'image/language');
            }
            $language = $this->language_repository->store([
                'name' => $request->name,
                'code' => strtolower(str_replace(" ","-",$request->code)),
                'status' => $request->status,
                'sort_order' => $request->sort_order,
                'image' => $flagIcon,
            ]);

            DB::commit();

            return $language;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }
    }
    public function edit($id): array
    {
        $this->pageTitle('Edit Language');

        $this->breadcrumb('Languages', 'system.language.index');

        $this->otherData([
            'result' => $this->language_repository->find($id)
        ]);

        return $this->retunData;
    }
    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $flagIcon = null;
            if (isset($request->image) && !empty($request->image)) {
                $flagIcon = $this->uploadFileS3($request->image, 'image/language');
            }

            $updateData = [
                'name' => $request->name,
                'code' => $request->code,
                'status' => $request->status,
                'sort_order' => $request->sort_order
            ];
            if($flagIcon != null ){
                $updateData['image'] = $flagIcon;
            }
            $update = $this->language_repository->update($updateData, $id);
            DB::commit();
            return $update;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }
    }
    public function languageArray()
    {
        return array_column($this->language_repository->get()->toArray(), 'name', 'id');
    }
}
