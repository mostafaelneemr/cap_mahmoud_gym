<?php

namespace App\Services;


use App\Repositories\Message\MessageRepository;
use Illuminate\Support\Facades\DB;
use Datatables;


class MessageService extends BaseService
{
    protected $messageRepository;

    public function __construct(MessageRepository  $messageRepository,)
    {
        parent::__construct();
        $this->messageRepository = $messageRepository;
    }

    public function loadViewData(): array
    {
        $this->pageTitle(__('Messages'));
        $this->tableColumns([
            __('ID'),
            __('Name'),
            __('Email'),
            __('Telephone'),
            __('Message'),
            __('Read'),
        ]);

        $this->jsColumns([
            'id' => 'message.id',
            'name' => '',
            'email' => '',
            'telephone' => '',
            'message' => '',
            'is_read' => '',
        ]);

        $this->filterIgnoreColumns(['action']);
        return $this->retunData;
    }

    public function loadDataTableData()
    {
        return Datatables::eloquent($this->messageRepository->getDataTableQuery())
            ->addColumn('id', '{{$id}}')
            ->addColumn('name', '{{$name}}')
            ->addColumn('email', '{{$email}}')
            ->addColumn('message', '{{$message}}')
            ->escapeColumns([])
            ->make(true);
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'message' => $request->message,
                'is_read' => 'no',
            ];

            $store = $this->messageRepository->store($data);

            DB::commit();
            return $store;
        } catch (\Exception $e) {
            DB::rollback();
            errorLog($e->getMessage());
            return false;
        }
    }


}
