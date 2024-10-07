<?php

namespace App\Services\Address;

use App\Repositories\Location\CountryRepository;
use App\Services\BaseService;
use Datatables;

class CountryService extends BaseService
{
    protected $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        parent::__construct();
        $this->countryRepository = $countryRepository;
    }
    public function loadViewData(): array
    {
        $this->pageTitle(__('Countries'));
        $this->tableColumns([
            __('ID'),
            __('Name'),
            __('Image'),
            __('Iso Code 2'),
            __('Iso Code 3'),
            __('Status'),
         ]);

        $this->jsColumns([
            'id' => 'address_country.id',
            'name' => 'address_country.name_'.lang(),
            'thumb' => 'address_country.thumb',
            'iso_code_2' => 'address_country.iso_code_2',
            'iso_code_3' => 'address_country.iso_code_3',
            'status' => 'address_country.status',
         ]);

        $this->breadcrumb('Setting');
        $this->breadcrumb('Localization');
        $this->filterIgnoreColumns(['action','status','image']);
        return $this->retunData;
    }

    public function loadDataTableData()
    {
        $eloquentData = $this->countryRepository->getDataTableQuery();

        return Datatables::eloquent($eloquentData)
            ->addColumn('id', '{{$id}}')
            ->addColumn('name', function ($data) {
                return $data->{'name_'.lang()};
            })
            ->addColumn('thumb', function ($data) {
                if ($data->thumb) {
                    return datatableImageFullPath($data->thumb);
                }
                return '--';
            })
            ->addColumn('iso_code_2', function ($data) {
                return $data->iso_code_2;
            })
            ->addColumn('iso_code_3', function ($data) {
                return $data->iso_code_3;
            })
            ->addColumn('status', function ($data) {
                return status_icon($data->status);
            })

            ->escapeColumns([])
            ->make(true);
    }


}
