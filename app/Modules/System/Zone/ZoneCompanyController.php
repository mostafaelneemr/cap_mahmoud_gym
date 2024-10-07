<?php

namespace App\Modules\System\Zone;

use App\Modules\System\SystemController;
use App\Services\ZoneCompanyService;
use Illuminate\Http\Request;

class ZoneCompanyController extends SystemController
{
    protected $zoneCompanyService;

    public function __construct(ZoneCompanyService $zoneCompanyService)
    {
        parent::__construct();
        $this->zoneCompanyService = $zoneCompanyService;
    }

    public function index(Request $request)
    {

        if ($request->isDataTable) {
            return $this->zoneCompanyService->loadDataTableData();
        }

        return $this->view('zone-company.index', $this->zoneCompanyService->loadViewData());
    }

    public function manageZoneCompany(Request $request){
        return $this->view('manage-zone-company.index', $this->zoneCompanyService->loadViewData());
    }
    public function getZoneCompany(Request $request)
    {
        $zones = $this->zoneCompanyService->loadZoneCompany($request->zoneId);
        $view = $this->view("manage-zone-company.table-data", compact('zones'))->render();
        return response()->json(['html' => $view]);
    }


    public function sortZoneCompany(Request $request)
    {
        $updated = $this->zoneCompanyService->updateSort($request);
        if ($updated) {
            return $this->success(__('Data Updated successfully'));
        }
        return ['status' => false, 'msg' => __('Sorry, we could not add the data')];
    }

    public function statusZoneCompany(Request $request)
    {
        $updated = $this->zoneCompanyService->updateStatus($request->zoneCompanyId);
        if ($updated) {
            return $this->success(__('Data Updated successfully'));
        }
        return ['status' => false, 'msg' => __('Sorry, we could not add the data')];
    }

}
