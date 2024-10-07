<?php

namespace App\Modules\System\Address;

use App\Modules\System\SystemController;
use App\Services\ZoneService;
use Illuminate\Http\Request;

class ZoneController extends SystemController
{

    protected $zoneService;

    public function __construct(ZoneService $zoneService)
    {
        parent::__construct();
        $this->zoneService = $zoneService;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->zoneService->loadDataTableData();
        }
        return $this->view('zone.index', $this->zoneService->loadViewData());
    }






}
