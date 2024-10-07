<?php

namespace App\Modules\System\Address;

use App\Modules\System\SystemController;
use App\Services\Address\CityService;
use Illuminate\Http\Request;

class CityController extends SystemController
{

    protected $cityService;

    public function __construct(CityService $cityService)
    {
        parent::__construct();
        $this->cityService = $cityService;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->cityService->loadDataTableData();
        }
        return $this->view('city.index', $this->cityService->loadViewData());
    }






}
