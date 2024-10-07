<?php

namespace App\Services;

use App\Filters\AddressCityId;
use App\Filters\AddressCountryId;
use App\Filters\AddressRegionId;
use App\Filters\Code;
use App\Filters\Id;
use App\Filters\NameWithLang;
use App\Filters\Status;
use App\Filters\ZoneRegionId;
use App\Repositories\Location\CountryRepository;
use App\Repositories\Zone\ZoneRepository;
use Datatables;
use Illuminate\Pipeline\Pipeline;

class ZoneService extends BaseService
{
    protected $zoneRepository,$countryRepository;

    public function __construct(ZoneRepository $zoneRepository,CountryRepository $countryRepository)
    {
        parent::__construct();
        $this->zoneRepository = $zoneRepository;
        $this->countryRepository = $countryRepository;
    }

    public function loadViewData(): array
    {
        $this->pageTitle(__('Zones'));
        $this->tableColumns([
            __('ID'),
            __('Country'),
            __('Region'),
            __('Name'),
            __('Code'),
            __('City'),
            __('Status'),
        ]);

        $this->jsColumns([
            'id' => 'address_zone.id',
            'county_id' => 'address_zone.country_id',
            'region_id' => 'address_zone.region_id',
            'name' => 'address_zone.name_'.lang(),
            'code' => 'address_zone.code',
            'city_id' => 'address_zone.city_id',
            'status' => 'address_zone.status',
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
        $query = $this->zoneRepository->getDataTableQuery();
        $eloquentData = app(Pipeline::class)
            ->send($query)
            ->through([
                Id::class,
                AddressCountryId::class,
                ZoneRegionId::class,
                NameWithLang::class,
                Code::class,
                AddressCityId::class,
                Status::class,
            ])->thenReturn();
        return Datatables::eloquent($eloquentData)
            ->addColumn('id', '{{$id}}')
            ->addColumn('county_id', function ($data) {
                return optional($data->country)->{ 'name_'.lang() } ?? '';
            })
            ->addColumn('region_id', function ($data) {
                return optional($data->region)->{ 'name_'.lang() } ?? '';
            })
            ->addColumn('city_id', function ($data) {
                return optional($data->city)->{ 'name_'.lang() } ?? '';
            })
            ->addColumn('name', function ($data) {
                return $data->{'name_'.lang()} ?? '';
            })
            ->addColumn('status', function ($data) {
                return status_icon($data->status);
            })

            ->escapeColumns([])
            ->make(true);
    }

    public function search_zone($word){
        return $this->zoneRepository->getForSelect($word);
    }


}
