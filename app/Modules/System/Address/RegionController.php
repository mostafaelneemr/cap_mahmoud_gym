<?php

namespace App\Modules\System\Address;

use App\Modules\System\SystemController;
use App\Services\Address\RegionService;
use Illuminate\Http\Request;

class RegionController extends SystemController
{

    protected $regionService;

    public function __construct(RegionService $regionService)
    {
        parent::__construct();
        $this->regionService = $regionService;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->regionService->loadDataTableData();
        }
        return $this->view('region.index', $this->regionService->loadViewData());
    }






}
