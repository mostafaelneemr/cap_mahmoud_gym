<?php
namespace App\Services;

use App\Repositories\AuthSession\AuthSessionRepository;
use Datatables;
use Illuminate\Pipeline\Pipeline;
use Jenssegers\Agent\Agent;

class AuthSessionService extends BaseService
{
    protected $auth_session_repository;

    public function __construct(AuthSessionRepository $auth_session_repository)
    {
        parent::__construct();
        $this->auth_session_repository = $auth_session_repository;
    }

    public function loadViewData(): array
    {
        $this->pageTitle('Auth Sessions');
        $this->tableColumns([
            __('ID'),
            __('User'),
            __('Ip'),
            __('Created At'),
            __('Updated At'),
            __('Action')
        ]);

        $this->jsColumns([
            'id' => 'auth_session.id',
            'user_id' => 'auth_session.user_id',
            'ip' => 'auth_session.ip',
            'created_at' => 'auth_session.created_at',
            'updated_at' => 'auth_session.updated_at',
            'action' => 'action'
        ]);

        $this->breadcrumb('Setting');
        $this->filterIgnoreColumns(['id', 'user_id', 'ip', 'user_agent', 'action', 'status', 'created_at', 'updated_at']);
        return $this->retunData;
    }


    /**
     * @return mixed
     */
    public function loadDataTableData($user_id=null)
    {
        $query = $this->auth_session_repository->getDataTableQuery($user_id);

        return Datatables::eloquent($query)
            ->addColumn('id', '{{$id}}')
            ->addColumn('user_id', function ($data) {
                return datatable_links('system.user.show', route('system.user.show', $data->user_id), $data->user?->name);
            })
            ->addColumn('ip', '{{$ip}}')
            ->addColumn('created_at', function ($data) {
                return !empty($data->created_at) || !is_null($data->created_at)?$data->created_at->diffForHumans():'';
            })
            ->addColumn('updated_at', function ($data) {
                return !empty($data->updated_at) || !is_null($data->updated_at)?$data->updated_at->diffForHumans():'';
            })
            ->addColumn('action', function ($data) {

                $this->actionButtons(datatable_menu_show_onclick( route('system.auth-sessions.show', $data->id),'system.user.show' , $data->id));
                $this->actionButtons(datatable_menu_delete(route('system.auth-sessions.destroy', $data->id) ,'system.auth-sessions.destroy','tr_'.$data->id));
                return $this->actionButtonsRender();
            })
            ->escapeColumns([])
            ->setRowId(function ($data){
                return 'tr_'.$data->id;
            })
            ->make(true);
    }

    public function delete($id)
    {
      return  $this->auth_session_repository->destroy($id);
    }


    /**
     * @param $id
     */
    public function findById($id)
    {
        $result =  $this->auth_session_repository->find($id);

        $agent = new Agent();
        $agent->setUserAgent($result->user_agent);
        $result->agent = $agent;

        $location = @json_decode(file_get_contents('http://ip-api.com/json/'.$result->ip));
        if($location->status!='fail')
            $result->location = $location;


        $htmlData  = view( 'system.auth-session.show', compact('result'))->render();

        return response()->json($htmlData,200);

    }
}
