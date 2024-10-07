<?php

namespace App\Services\Address;

use App\Filters\{AddressCountry, Id, NameWithLang, Status};
use App\Repositories\Location\{CountryRepository,RegionRepository};
use App\Services\BaseService;
use Datatables;
use Illuminate\Pipeline\Pipeline;

class RegionService extends BaseService
{
    protected $regionRepository,$countryRepository;

    public function __construct(RegionRepository $regionRepository,CountryRepository $countryRepository)
    {
        parent::__construct();
        $this->regionRepository = $regionRepository;
        $this->countryRepository = $countryRepository;
    }
    public function loadViewData(): array
    {
        $this->pageTitle(__('Regions'));
        $this->tableColumns([
            __('ID'),
            __('Country'),
            __('Name'),
            __('Status'),
        ]);

        $this->jsColumns([
            'id' => 'address_region.id',
            'county_id' => 'address_region.county_id',
            'name' => 'address_region.name_'.lang(),
            'status' => 'address_region.status',
        ]);

        $this->breadcrumb('Setting');
        $this->breadcrumb('Localization');
        $this->filterIgnoreColumns(['action','status']);
        $this->otherData([
            'countries' => array_column($this->countryRepository->getForSelect(), 'name', 'id'),
        ]);
        return $this->retunData;
    }

    public function loadDataTableData()
    {
        $query = $this->regionRepository->getDataTableQuery();
        $eloquentData = app(Pipeline::class)
            ->send($query)
            ->through([
                Id::class,
                AddressCountry::class,
                NameWithLang::class,
                Status::class,
            ])->thenReturn();
        return Datatables::eloquent($eloquentData)
            ->addColumn('id', '{{$id}}')
            ->addColumn('county_id', function ($data) {
                return optional($data->country)->{ 'name_'.lang() } ?? '';
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

    public function search_region($word){
        return $this->regionRepository->getSelectRegion($word);
    }

}
