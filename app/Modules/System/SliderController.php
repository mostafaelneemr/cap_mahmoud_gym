<?php

namespace App\Modules\System;

use App\Services\SliderService;
use Illuminate\Http\Request;

class SliderController extends SystemController
{

    protected $sliderService;

    public function __construct(SliderService $sliderService)
    {
        parent::__construct();
        $this->sliderService = $sliderService;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->sliderService->loadDataTableData();
        }
        return $this->view('slider.index', $this->sliderService->loadViewData());
    }

    public function create()
    {
        return $this->view('slider.create', $this->sliderService->create());
    }


    public function store(Request $request)
    {
        $user = $this->sliderService->store($request);
        if ($user) {
            flash_msg('success',__('Data Added successfully'));
            return $this->success( __( 'Data added successfully' ),
                [ 'url' => route( 'system.slider.index' )] );
        } else {
            return $this->fail(__( 'Sorry, we could not add the data' ) );
        }
    }

    public function edit($id)
    {
        return $this->view('slider.create', $this->sliderService->edit($id));
    }

    public function update(Request $request, $id)
    {
        $update = $this->sliderService->update($request, $id);
        if ($update) {
            flash_msg('success',__( 'Data Updated successfully' ));
            return $this->success( __( 'Data Updated successfully' ),
                ['url'=> route('system.slider.index') ]);
        } else {
            return $this->fail(__( 'Sorry, we could not Update the data' ) );
        }

    }

}
