<?php

namespace App\Services;


use App\Imports\DataImport;
use App\Repositories\ActivityLog\ActivityLogRepository;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Excel;
use Illuminate\Support\Facades\DB;

abstract class BaseService extends Service
{
    protected $retunData = [];
    protected $datatableID = 'datatable-main';
    protected $dataTableSort = ['column'=>0,'order'=>'desc'];
    protected $dataTableReorder = false;
    protected $pageLength = 100;


    public function __construct()
    {
      $this->initReturnData();

    }

    public function initReturnData(){
        $this->clearRetunData();
        $this->retunData['datatableID'] = $this->datatableID;
        $this->retunData['datatableURL'] = url()->full();
        $this->retunData['datatableVar'] = md5($this->datatableID);
        $this->retunData['dataTableSort'] = $this->dataTableSort;
        $this->retunData['dataTableReorder'] = $this->dataTableReorder;
        $this->retunData['pageLength'] = $this->pageLength;
    }

    public function clearRetunData()
    {
        $this->retunData = [];
    }

    public function datatableReorder(bool $dataTableReorder)
    {
        $this->retunData['dataTableReorder'] = $dataTableReorder;
    }
    public function pageLength($pageLength)
    {
        $this->retunData['pageLength'] = $pageLength;
    }

    public function datatableURL(string $datatableURL)
    {
        $this->retunData['datatableURL'] = $datatableURL;
    }

    public function datatableID(string $datatableID)
    {
        $this->retunData['datatableID'] = $datatableID;
        $this->retunData['datatableVar'] = md5($datatableID);

    }

    public function pageTitle(string $title)
    {
        $this->retunData['pageTitle'] = __($title);
    }

    public function jsColumns(array $columns)
    {
        $this->retunData['jsColumns'] = $columns;
    }

    public function customClass($custom_class = '')
    {
        if (isset($custom_class)) {
            $this->retunData['customClass'] = $custom_class;
        }
    }

    public function dataTableSort($column = null, $order = null)
    {
        if (isset($column))
            $this->retunData['dataTableSort']['column'] = $column;
        if (isset($order))
            $this->retunData['dataTableSort']['order'] = $order;
    }

    public function tableColumns(array $columns)
    {
        $this->retunData['tableColumns'] = $columns;
    }

    public function addButton(string $route, string $title = '')
    {
        $this->retunData['addButton'] = ['route' => $route, 'title' => $title];
    }

    public function addModalButton(string $route, string $title = '', string $modalId = '', $icon = 'ki-outline ki-folder-up fs-2')
    {
        $this->retunData['addModalButton'][] = ['route' => $route, 'title' => $title, 'modalId' => $modalId, 'icon' => $icon];
    }

    public function addModalIcon(string $route, string $modalId = '')
    {
        $this->retunData['addModalIcon'] = ['route' => $route, 'modalId' => $modalId];
    }

    public function downloadExcel(bool $download)
    {
        // TODO: Implement downloadExcel() method.
    }

    public function showAdvancedFilter(bool $boolean)
    {
        $this->retunData['showAdvancedFilter'] = $boolean;
    }

    public function showDownloadExcel(bool $boolean)
    {
        $this->retunData['showDownloadExcel'] = $boolean;
    }

    public function filterIgnoreColumns(array $columns)
    {
        $this->retunData['filterIgnoreColumns'] = $columns;
    }

    public function otherData(array $data)
    {
        $this->retunData = array_merge($this->retunData, $data);
    }

    public function actionButtons($button)
    {
        $this->retunData['actionButtons'][] = $button;
    }

    public function editModalButton($button)
    {
        $this->retunData['editModalButton'] = $button;
    }

    public function actionButtonsRender($model = null, $model_id = null)
    {
        $buttons = $this->retunData['actionButtons'] ?? [];
        $this->retunData['actionButtons'] = [];
        return view('system.actionButtons',
            [
                'actionButtons' => $buttons,
                'model' => str_replace('\\', '\\\\', $model),
                'model_id' => $model_id
            ]);
    }

    /**
     * Optimizes PNG file with pngquant 1.8 or later (reduces file size of 24-bit/32-bit PNG images).
     *
     * You need to install pngquant 1.8 on the server (ancient version 1.0 won't work).
     * There's package for Debian/Ubuntu and RPM for other distributions on http://pngquant.org
     *
     * @param $path_to_png_file
     * @param int $max_quality
     * @return string|null
     * @throws Exception
     */
    function compress_png($path_to_png_file, $max_quality = 90, $_isImg360 = false)
    {
        if (!file_exists($path_to_png_file)) {
            throw new Exception("File does not exist: $path_to_png_file");
        }

        $max_quality = $_isImg360 ? 100 : $max_quality;
        $min_quality = $_isImg360 ? 99 : 60;

        // '-' makes it use stdout, required to save to $compressed_png_content variable
        // '<' makes it read from the given file path
        // escapeshellarg() makes this safe to use with any path
        $compressed_png_content = shell_exec("/usr/local/bin/pngquant --quality=$min_quality-$max_quality - < " . escapeshellarg($path_to_png_file));

        unlink($path_to_png_file);

        if (!$compressed_png_content) {
            throw new Exception("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
        }

        return $compressed_png_content;
    }

    function compress_jpg($path_to_jpg_file, $max_quality = 90, $_isImg360 = false)
    {
        if (!file_exists($path_to_jpg_file)) {
            throw new Exception("File does not exist: $path_to_jpg_file");
        }

        // guarantee that quality won't be worse than that.
        $max_quality = $_isImg360 ? 100 : $max_quality;
        $min_quality = $_isImg360 ? 99 : 60;

        // '-' makes it use stdout, required to save to $compressed_png_content variable
        // '<' makes it read from the given file path
        // escapeshellarg() makes this safe to use with any path
        shell_exec("/usr/local/bin/jpegoptim -m60 " . escapeshellarg($path_to_jpg_file));


//        unlink($path_to_jpg_file);


//        if (!$compressed_jpg_content) {
//            throw new Exception("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
//        }
//
//        return $compressed_jpg_content;
    }

    public function uploadFileS3($file, $folderName, $driver = 's3', $content = null)
    {
        if(!$content && $file){
            $file_name = '';
            $name_only = time() . "_" . md5(time());
            $name = $name_only . '.' . $file->getClientOriginalExtension();
            $success_upload = Storage::disk($driver)->put($folderName . '/' . $name, fopen($file, 'r+'));
            $file_name = $success_upload ? $folderName . '/' . $name_only . '.' . $file->getClientOriginalExtension() : $file_name;
            return $file_name;
        }elseif($content){
            return Storage::disk('s3')->put($folderName, $content);
        }
    }


    public function uploadImage($image, $folderName)
    {
        return $image->store($folderName . '/' . date('y') . '/' . date('m'));
    }

    public function breadcrumb($text, $url = '')
    {
        $data['text'] = __($text);
        if (!empty($url)) {
            $data['url'] = route($url);
        }

        $this->retunData['breadcrumb'][] = $data;

//        $this->retunData['breadcrumb'][] = [
//            'text' => __($this->retunData['pageTitle']),
//        ];

    }

    function token($length = 32): string
    {
        return Str::random($length);
    }

    function getCode($telephone)
    {

        switch (substr($telephone, 0, 3)) {
            case '966':
                return "sa";
                break;
            case "965":
                return "kw";
                break;
            case "971":
                return "ae";
                break;
            case "968":
                return "om";
                break;
            case "973":
                return "bh";
                break;
            case "974":
                return "qa";
                break;
            case "+20":
                return "eg";
                break;
            default:
                return "eg";
        }
    }


    public function handleActivityLogData($newData, $oldData)
    {

        if (empty($newData) && empty($oldData) && (is_array($oldData) && !count($oldData)) && (is_array($newData) && !count($newData)))
            return;
        if (!empty($oldData)) {
            $changed_new_data = [];
            $changed_old_data = [];

            foreach ($newData as $key => $row) {
                if ($newData[$key] != $oldData[$key]) {
                    $changed_old_data[$key] = $oldData[$key];
                    $changed_new_data[$key] = $newData[$key];
                }
            }
            if (empty($changed_new_data) && empty($changed_old_data))
                return;
            $DATA = ['attributes' => $changed_new_data, 'old' => $changed_old_data];
        } else {
            $DATA = ['attributes' => $newData];
        }
        return $DATA;

    }


    public function ActivityLogManually($event, $subject_type, $subject_id, $newData, $oldData = [])
    {

        $DATA = $this->handleActivityLogData($newData, $oldData);

        $activityLogRepo = new ActivityLogRepository();
        // Resolve causer: check user guard first, then trainee guard
        $causer = auth('user')->user() ?? auth('trainee')->user();
        $activityLogRepo->store([
            'log_name'     => config('activitylog.default_log_name'),
            'description'  => $event,
            'subject_id'   => $subject_id,
            'subject_type' => $subject_type,
            'causer_id'    => $causer?->id,
            'causer_type'  => $causer?->modelPath ?? 'unknown',
            'ip'           => getRealIP(),
            'user_agent'   => getUserAgent(),
            'url'          => request()->url(),
            'properties'   => $DATA,
            'event'        => $event
        ]);
    }

    public function ActivityLog($event, $elquentModel, $newData, $oldData = [], $description = null)
    {

        $DATA = $this->handleActivityLogData($newData, $oldData);
        if (!empty($DATA)) {
            // Resolve causer: check user guard first, then trainee guard
            $causer = auth('user')->user() ?? auth('trainee')->user();
            activity()
                ->withProperties($DATA)
                ->performedOn($elquentModel)
                ->causedBy($causer)
                ->event($event)
                ->log($description ?? $event);
        }


    }

    public function upload_s3_with_file_content($path,$content){
       return Storage::disk('s3')->put($path, $content);
    }

//    protected function sendEmailTemplate($to, $subject, $text, $data,$template='mail.plain-text')
//    {
//        $mailUrl = "";
//        $messageBody = view('system.'.$template, $data)->render();
//        if(empty($messageBody)){
//            $messageBody = $text;
//        }
//
//        $mailParams = array(
//            'to' => $to,
//            'from' => '',
//            'subject' => $subject,
//            'messageBody' => $messageBody,
//            'validate' => false
//        );
//
//
//        return $this->curlJsonRequestWithOutResponse($mailUrl, json_encode($mailParams));
//    }
//
//    public function curlJsonRequestWithOutResponse($url, $params)
//    {
//        $curl = curl_init($url);
//        curl_setopt( $curl, CURLOPT_POSTFIELDS, $params);
//        curl_setopt( $curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'platform:curl'));
//        curl_setopt($curl, CURLOPT_USERAGENT, 'curl');
//        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
//        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
//
//
//        $result = curl_exec($curl);
//        curl_close($curl);
//
//        return $result;
//    }
}



