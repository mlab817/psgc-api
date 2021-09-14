<?php

namespace App\Exports;

ini_set('memory_limit', '-1');

use App\Eloquent\Barangay;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BarangaysExport implements FromQuery, WithMapping, WithHeadings, ShouldQueue
{
    /**
     * @return Builder
     */
    public function query()
    {
        return Barangay::query()
            ->with('geographic');
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->name, // barangay name
            $row->geographic->name, // city/municipality
            get_class($row->geographic) == 'App\\Eloquent\\SubMunicipality'
                ? $row->geographic->city->name
                : $row->geographic->geographic->name, // province,
            $row->code, // psgc_code,
        ];
    }

    public function headings(): array
    {
        return [
            'Barangay',
            'CityMunicipality',
            'Province',
            'Code',
        ];
    }
}
