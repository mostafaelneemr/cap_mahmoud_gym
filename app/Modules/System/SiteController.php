<?php

namespace App\Modules\System;

use App\Http\Requests\ItemFormRequest;
use App\Http\Requests\SiteFormRequest;
use App\Services\SiteService;
use Illuminate\Http\Request;

class SiteController extends SystemController
{

    protected $siteService;

    public function __construct(SiteService $siteService)
    {
        parent::__construct();
        $this->siteService = $siteService;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->siteService->loadDataTableData();
        }
        return $this->view('site.index', $this->siteService->loadViewData());
    }

    public function create()
    {
        return $this->view('site.create', $this->siteService->create());
    }


    public function store(SiteFormRequest $request)
    {
        $store = $this->siteService->store($request);
        if ($store) {
            flash_msg('success',__('Data Added successfully'));
            return $this->success( __( 'Data added successfully' ),
                [ 'url' => route( 'system.site.index' )] );
        } else {
            return $this->fail(__( 'Sorry, we could not add the data' ) );
        }
    }

    public function edit($id)
    {
        return $this->view('site.create', $this->siteService->edit($id));
    }

    public function update(SiteFormRequest $request, $id)
    {
        $update = $this->siteService->update($request, $id);
        if ($update) {
            flash_msg('success',__( 'Data Updated successfully' ));
            return $this->success( __( 'Data Updated successfully' ),
                ['url'=> route('system.site.index') ]);
        } else {
            return $this->fail(__( 'Sorry, we could not Update the data' ) );
        }

    }

}
