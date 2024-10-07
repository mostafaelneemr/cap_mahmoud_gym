<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;


class ExportExcel implements  FromCollection, WithHeadings
{
    protected $data,$columns;

    function __construct($columns,$data)
    {
        $this->columns = $columns;
        $this->data = $data;

    }
    public function collection()
    {

        return   $this->data;
    }

//    public function array(): array
//    {
//        return   $this->data;
//    }

    public function headings(): array
    {
        return $this->columns;
    }

}
