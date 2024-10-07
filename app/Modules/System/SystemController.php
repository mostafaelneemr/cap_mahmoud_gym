<?php

namespace App\Modules\System;

use App\Http\Controllers\Controller;

use App\Models\Driver;
use App\Models\Order;
use Datatables;
use App\Notifications\General;

class SystemController extends Controller{

    protected $viewData = [
        'breadcrumb'=> []
    ];

    public function __construct(){

        $this->middleware(['auth:user','check_password_reset']);
    }

    protected function view($file,array $data = [], $mergeData = []){
        return view('system.'.$file,$data, $mergeData);
    }

    protected function response($status,$code = '200',$message = 'Done',$data = []) {

        return response([
            'status'=> $status,
            'code'=> $code,
            'message'=> $message,
            'data'=> $data
        ], $code);

    }

    protected function success($message = 'Done',$data = []) {
        return $this->response(true,200,$message,(Array)$data);
    }

    protected function fail($message = 'Done',$data = []) {
        return $this->response(false,400,$message,(Array)$data);
    }


    public function dashboard(\Illuminate\Http\Request $request){

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Dashboard')
        ];
        $this->viewData['pageTitle'] = __('Dashboard');
         return $this->view('dashboard',$this->viewData);
    }

    public function ux(\Illuminate\Http\Request $request){

        $this->viewData['breadcrumb'][] = [
            'text'=> __('UX')
        ];
        $this->viewData['pageTitle'] = __('UX');
        return $this->view('ux',$this->viewData);
    }


}
