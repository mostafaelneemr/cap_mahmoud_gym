<?php

namespace App\Modules\System;

use App\Http\Requests\AddressFormRequest;
use App\Http\Requests\LanguageFormRequest;
use App\Services\AddressService;
use App\Services\CustomerService;
use App\Services\LanguageService;
use Illuminate\Http\Request;

class LanguageController extends SystemController
{

    protected $language_service;

    public function __construct(LanguageService $language_service)
    {
        parent::__construct();
        $this->language_service = $language_service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->language_service->loadDataTableData();
        }
        return $this->view('language.index', $this->language_service->loadViewData());
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->view('language.create', $this->language_service->create());
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LanguageFormRequest $request)
    {
        $row = $this->language_service->store($request);
        if ($row) {
            flash_msg('success',__('Data added successfully'));
            return $this->success( __( 'Data added successfully' ),
                [  'url' => route( 'system.language.index' )] );
        }
        return $this->fail(__( 'Sorry, we could not add the data' ) );
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->view('language.create', $this->language_service->edit($id));

    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LanguageFormRequest $request, $id)
    {
        $update = $this->language_service->update($request, $id);
        if ($update) {
            flash_msg('success',__( 'Data Updated successfully' ));
            return $this->success( __( 'Data Updated successfully' ),
                [  'url' => route( 'system.language.index' )]);
        }
        return $this->fail(__( 'Sorry, we could not Update the data' ) );
    }


}
