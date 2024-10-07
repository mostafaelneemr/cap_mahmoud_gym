<?php

namespace App\Services\Address;

use App\Filters\AddressCountryRegion;
use App\Filters\AddressRegionId;
use App\Filters\Id;
use App\Filters\NameWithLang;
use App\Services\BaseService;
use Datatables;
use Illuminate\Pipeline\Pipeline;

class CityService extends BaseService
{
    protected $cityRepository,$countryRepository;

    public function __construct(CityRepository $cityRepository,CountryRepository $countryRepository)
    {
        parent::__construct();
        $this->cityRepository = $cityRepository;
        $this->countryRepository = $countryRepository;
    }
    public function loadViewData(): array
    {
        $this->pageTitle(__('Cities'));
        $this->tableColumns([
            __('ID'),
            __('Region'),
            __('Name'),
            __('lat'),
            __('lng'),
        ]);

        $this->jsColumns([
            'id' => 'address_city.id',
            'region_id' => 'address_city.region_id',
            'name' => 'address_city.name_'.lang(),
            'lat' => 'address_city.lat',
            'lng' => 'address_city.lng',
        ]);

        $this->breadcrumb('Setting');
        $this->breadcrumb('Localization');
        $this->filterIgnoreColumns(['action']);
        $this->otherData([
            'countries' => array_column($this->countryRepository->getForSelect(), 'name', 'id'),
        ]);
        return $this->retunData;
    }

    public function loadDataTableData()
    {
        $query = $this->cityRepository->getDataTableQuery();
        $eloquentData = app(Pipeline::class)
            ->send($query)
            ->through([
                Id::class,
                NameWithLang::class,
                AddressRegionId::class,
                AddressCountryRegion::class,
            ])->thenReturn();
        return Datatables::eloquent($eloquentData)
            ->addColumn('id', '{{$id}}')
            ->addColumn('region_id', function ($data) {
                return optional($data->region)->{ 'name_'.lang() } ?? '';
            })
            ->addColumn('name', function ($data) {
                return $data->{'name_'.lang()};
            })

            ->escapeColumns([])
            ->make(true);
    }


}
