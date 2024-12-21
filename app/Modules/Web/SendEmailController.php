<?php

namespace App\Modules\Web;


use App\Modules\System\SystemController;
use App\Services\BaseService;
use App\Services\MessageService;
use Illuminate\Http\Request;


class SendEmailController extends SystemController
{

    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        parent::__construct();
        $this->messageService = $messageService;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->messageService->loadDataTableData();
        }
        return $this->view('slider.index', $this->messageService->loadViewData());
    }

    public function store(Request $request)
    {
        $store = $this->messageService->store($request);
        if ($store) {
            flash_msg('success',__('Data Added successfully'));
            return $this->success( __( 'Data added successfully' ),
                [ 'url' => route( 'contact' )] );
        } else {
            return $this->fail(__( 'Sorry, we could not add the data' ) );
        }
    }

}
