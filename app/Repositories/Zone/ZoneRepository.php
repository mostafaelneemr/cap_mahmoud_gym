<?php

namespace App\Repositories\Zone;

use App\Models\Zone;
use App\Repositories\BaseRepository;

class ZoneRepository extends BaseRepository
{
    protected $modeler = Zone::class;

    public function getDataTableQuery()
    {
        return $this->modeler->select(['id', 'country_id','region_id', 'name_en','name_ar','code','status','city_id', 'lat','lng']);
    }

    public function getForSelect($word)
    {
        return $this->modeler->where('address_zone.name_'.lang(), 'Like', '%' . $word . '%')
            ->select('address_zone.name_'.lang(). ' as value', 'address_zone.id as id')->get()->toArray();
    }
}
