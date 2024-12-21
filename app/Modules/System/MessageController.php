<?php

namespace App\Modules\System;


use App\Services\MessageService;
use Illuminate\Http\Request;


class MessageController extends SystemController
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
        return $this->view('message.index', $this->messageService->loadViewData());
    }

}
