<?php

namespace App\Modules\System;

use App\Services\BlogService;
use App\Services\TestimonialService;
use Illuminate\Http\Request;

class BlogController extends SystemController
{

    protected $blogService;

    public function __construct(BlogService $blogService)
    {
        parent::__construct();
        $this->blogService = $blogService;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->blogService->loadDataTableData();
        }
        return $this->view('blog.index', $this->blogService->loadViewData());
    }

    public function create()
    {
        return $this->view('blog.create', $this->blogService->create());
    }


    public function store(Request $request)
    {
        $user = $this->blogService->store($request);
        if ($user) {
            flash_msg('success',__('Data Added successfully'));
            return $this->success( __( 'Data added successfully' ),
                [ 'url' => route( 'system.blog.index' )] );
        } else {
            return $this->fail(__( 'Sorry, we could not add the data' ) );
        }
    }

    public function edit($id)
    {
        return $this->view('blog.create', $this->blogService->edit($id));
    }

    public function update(Request $request, $id)
    {
        $update = $this->blogService->update($id, $request);
        if ($update) {
            flash_msg('success',__( 'Data Updated successfully' ));
            return $this->success( __( 'Data Updated successfully' ),
                ['url'=> route('system.blog.index') ]);
        } else {
            return $this->fail(__( 'Sorry, we could not Update the data' ) );
        }

    }

}
