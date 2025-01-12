<?php

namespace App\Services;


use App\Filters\Id;
use App\Filters\MessageRead;
use App\Filters\Name;
use App\Filters\Status;
use App\Repositories\Message\MessageRepository;
use Illuminate\Pipeline\Pipeline;
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
        $query = $this->messageRepository->getDataTableQuery();
        $eloquentData = app(Pipeline::class)
            ->send($query)
            ->through([
                MessageRead::class,
            ])->thenReturn();

        return Datatables::eloquent($eloquentData)
            ->addColumn('id', '{{$id}}')
            ->addColumn('name', '{{$name}}')
            ->addColumn('email', '{{$email}}')
            ->addColumn('message', '{{$message}}')
            ->addColumn('is_read', function($data) {
                if (in_array($data->is_read,[message_read(('Not_Read'))])) {
                    $this->actionButtons(datatable_menu_button(route('system.message.update-status'), 'system.message.update-status', 'fa-check', message_read('Read'), $data->id));
                }
                return $this->actionButtonsRender();
            })
            ->escapeColumns([])
            ->setRowId(function ($data) {
                return 'tr_' . $data->id;
            })
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

    public function updateStatus($request)
    {
        DB::beginTransaction();
        try {
            if (isset($request->data)) {
                $update = $this->messageRepository->update(['is_read' => $request->status], $request->data[0]);
            }
            DB::commit();
            return $update;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            return false;
        }
    }

}
