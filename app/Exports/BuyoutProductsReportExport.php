<?php

namespace App\Exports;

use App\Models\Products;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;

class BuyoutProductsReportExport implements FromQuery, WithHeadings
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        // $type = DB::table('location_type')->select('id','name')->get();

        return Products::query()->where('id', '1');
    }
    public function headings(): array
    {
        return ["your", "headings", "here"];
    }
    // public function collection()
    // {
    //     return Products::all();
    // }
}
