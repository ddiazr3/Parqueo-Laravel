<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketsExport implements FromCollection, WithHeadings
{

    use Exportable;

    protected $data;
    protected $header;
    public function __construct($data = [],$header = [])
    {
        $this->data  = $data;
        $this->header  = $header;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array{
//        return [
//            [""],
//            ["Fecha Reporte",Carbon::now()->format("Y-m-d")],
//            [""],
//            [""],
//            $this->header
//        ];
        return $this->header ;
    }


}
