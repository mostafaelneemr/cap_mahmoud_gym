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
            notify('success', 'Your message has been sent successfully!');
            return redirect()->back();
        } else {
            notify('error', 'An error occurred while sending your message.');
            return redirect()->back();
        }
    }

}
