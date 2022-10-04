<?php

namespace App\Imports\Product;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithColumnLimit;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProductPhoto implements ToCollection, WithMapping, WithStartRow, WithColumnLimit, SkipsEmptyRows
{
    use Importable;

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {

    }
    /**
     * 轉換成自訂的欄位名稱
     *
     * @param  mixed  $row
     * @return array
     */
    public function map($row): array
    {
        $ary = [
            'photos'=>[],
        ] ;
        foreach($row as $k => $v){
            if($k == 0){
                $ary['supplier_product_no'] = $v ;
            }
            if($k >= 1 && $k <= 15 && $v !== null){
                array_push($ary['photos'] , $v);
            }
            if($k == 16){
                $ary['google_shop_photo_name'] = $v;
            }
        }
        return $ary ;
    }

    /**
     * 起始列
     *
     * @return int
     */
    public function startRow(): int
    {
        return 3;
    }

    /**
     * 結束行
     *
     * @return string
     */
    public function endColumn(): string
    {
        return 'Q';
    }

}
