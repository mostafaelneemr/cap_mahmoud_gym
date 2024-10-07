<?php

namespace App\Modules\System\Address;

use App\Modules\System\SystemController;
use App\Services\Address\CountryService;
use Illuminate\Http\Request;

class CountryController extends SystemController
{

    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        parent::__construct();
        $this->countryService = $countryService;
    }

    public function index(Request $request)
    {
        if ($request->isDataTable) {
            return $this->countryService->loadDataTableData();
        }
        return $this->view('country.index', $this->countryService->loadViewData());
    }






}
