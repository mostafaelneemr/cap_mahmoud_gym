<?php

namespace App\Modules\System;

use App\Services\TestimonialService;
use Illuminate\Http\Request;

class TestimonialController extends SystemController
{

    protected $testimonialService;

    public function __construct(TestimonialService $testimonialService)
    {
        parent::__construct();
        $this->testimonialService = $testimonialService;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->testimonialService->loadDataTableData();
        }
        return $this->view('testimonial.index', $this->testimonialService->loadViewData());
    }

    public function create()
    {
        return $this->view('testimonial.create', $this->testimonialService->create());
    }


    public function store(Request $request)
    {
        $user = $this->testimonialService->store($request);
        if ($user) {
            flash_msg('success',__('Data Added successfully'));
            return $this->success( __( 'Data added successfully' ),
                [ 'url' => route( 'system.testimonial.index' )] );
        } else {
            return $this->fail(__( 'Sorry, we could not add the data' ) );
        }
    }

    public function edit($id)
    {
        return $this->view('testimonial.create', $this->testimonialService->edit($id));
    }

    public function update(Request $request, $id)
    {
        $update = $this->testimonialService->update($id, $request);
        if ($update) {
            flash_msg('success',__( 'Data Updated successfully' ));
            return $this->success( __( 'Data Updated successfully' ),
                ['url'=> route('system.testimonial.index') ]);
        } else {
            return $this->fail(__( 'Sorry, we could not Update the data' ) );
        }

    }

}
