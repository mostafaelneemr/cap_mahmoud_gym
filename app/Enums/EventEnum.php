<?php

namespace App\Enums;


enum EventEnum: string
{
    case created = "created" ;
    case updated = "updated" ;
    case deleted = "deleted" ;

    public static function values(): array
    {
        return array_column(self::cases(), 'value','name');
    }

    public static function values_lang(): array
    {
        $data = [];
        foreach (self::cases() as  $row){
            $data[$row->value] =  __($row->name) ;
        }
        return $data;
    }

}
