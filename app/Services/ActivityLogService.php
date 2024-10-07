<?php
namespace App\Services;

use App\Filters\CreatedAtFrom;
use App\Filters\CreatedAtTo;
use App\Filters\Event;
use App\Filters\Id;
use App\Filters\SubjectId;
use App\Filters\SubjectType;
use App\Repositories\ActivityLog\ActivityLogRepository;
use App\Repositories\AuthSession\AuthSessionRepository;
use Datatables;
use Illuminate\Pipeline\Pipeline;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\Models\Activity;

class ActivityLogService extends BaseService
{
    protected $activity_log_repository;

    public function __construct(ActivityLogRepository $activity_log_repository)
    {
        parent::__construct();
        $this->activity_log_repository = $activity_log_repository;
    }

    public function loadViewData(): array
    {
        $this->pageTitle('Activity Log');
        $this->tableColumns([
            __('ID'),
            __('Event'),
            __('User'),
            __('Model ID'),
            __('Created At'),
            __('Action')
        ]);

        $this->jsColumns([
            'id'=>'activity_log.id',
            'event'=>'activity_log.event',
            'causer'=>'activity_log.causer_id',
            'subject'=>'user.subject_type',
            'created_at'=>'activity_log.created_at',
            'action'=>'action'
        ]);

        $this->breadcrumb('Setting');
        $this->filterIgnoreColumns(['id', 'event', 'causer', 'subject', 'created_at', 'action']);
        $this->otherData(['models' => $this->models()]);

        return $this->retunData;
    }


    public function models()
    {
        $model_path = app_path() . "/Models";
        $out = [];
        $results = scandir($model_path);
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
            $filename = $model_path . '/' . $result;
            if (is_dir($filename)) {
                $out = array_merge($out, getModels($filename));
            } else {
                $out[] = str_replace("/", "\\\\", ucfirst(strstr(substr($filename, 0, -4), 'app/Models')));
            }
        }
        return $out;
    }


    /**
     * @return mixed
     */
    public function loadDataTableData($id = null)
    {
        $query = $this->activity_log_repository->getDataTableQuery($id);

        $eloquentData = app(Pipeline::class)
            ->send($query)
            ->through([
                Id::class,
                Event::class,
//                SubjectId::class,
//                SubjectType::class,
                CreatedAtFrom::class,
                CreatedAtTo::class
            ])->thenReturn();
        return datatables()->eloquent($eloquentData)
            ->addColumn('id','{{$id}}')
            ->addColumn('event',function($data){
                return __($data->event);
            })
            ->addColumn('causer',function($data){
                if($data->causer) {
                    return links('system.user.show', route('system.user.show',  $data->causer->id), $data->causer->name );
                }
                return '--';
            })
            ->addColumn('subject',function($data){
                $subject = last(explode('\\',$data->subject_type));
                return $subject. '( '. $data->subject_id .' )';
            })
            ->addColumn('created_at',function($data){
                if ($data->created_at)
                return $data->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('action',function($data){
                return show_links('system.activity-log.show', route('system.activity-log.show', $data->id), __("View"));
            })
            ->escapeColumns([])
            ->make(true);
    }


    /**
     * @param $id
     */
    public function findById($id)
    {

        $result =  $this->activity_log_repository->find($id);

        $agent = new Agent();
        $agent->setUserAgent($result->user_agent);
        $result->agent = $agent;

        $location = @json_decode(file_get_contents('http://ip-api.com/json/'.$result->ip));

        if(isset($location->status) && $location->status!='fail')
            $result->location = $location;

        $this->pageTitle('View Activity Log');
        $this->breadcrumb('Setting');
        $this->breadcrumb('Activity Log', 'system.activity-log.index');
        $this->otherData(['result'=> $result] );
        return $this->retunData;
    }


}
