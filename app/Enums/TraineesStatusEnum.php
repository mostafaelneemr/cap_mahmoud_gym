<?php

namespace App\Enums;


enum TraineesStatusEnum: string
{
    case active = "active" ;
    case inactive = "inactive" ;
    case expired = "expired" ;

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
