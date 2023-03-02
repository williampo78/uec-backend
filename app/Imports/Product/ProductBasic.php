<?php

namespace App\Imports\Product;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithColumnLimit;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ProductBasic implements ToCollection, WithMapping, WithStartRow, WithColumnLimit, SkipsEmptyRows
{
    use Importable;

    //計算行數
    protected $rowNum = 2;

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows) {}

    /**
     * 轉換成自訂的欄位名稱
     *
     * @param  mixed  $row
     * @return array
     */
    public function map($row): array
    {
        $keys = collect([
            'stock_type', //庫存類型
            'display_number', //供應商代碼
            'supplier_name', //供應商名稱
            'product_name', //商品名稱
            'brand_name', //品牌名稱
            'supplier_product_no', //廠商料號
            'product_type', //商品類型
            'spec_dimension', //規格維度，最多支援至2維
            'spec_1', //規格一(名稱)
            'spec_2', //規格二(名稱)
            'spec_1_value', //規格一
            'spec_2_value', //規格二
            'supplier_item_no', //廠商貨號
            'ean', //國際條碼
            'pos_item_no',//POS品號
            'safty_qty', //安全庫存量
            'min_purchase_qty', //最小入庫量
            'photo_name', //Item圖示
            'uom', //單位
            'model', //商品型號
            'length', //材積_長(公分)
            'width', //材積_寬(公分)
            'height', //材積_高(公分)
            'weight', //重量(公克)
            'list_price', //市價(含稅)
            'selling_price', //售價(含稅)
            'purchase_price', //成本(含稅)
            'product_brief_1', //商品簡述1
            'product_brief_2', //商品簡述2
            'product_brief_3', //商品簡述3
            'patent_no', //專利字號
            'is_with_warranty', //有無保固
            'warranty_days', //保固天數
            'warranty_scope', //保固範圍
            'promotion_desc', //促銷小標
            'promotion_start_at', //促銷小標-生效時間起
            'promotion_end_at', //促銷小標-生效時間訖
            'has_expiry_date', //效期控管
            'expiry_days', //效期控管天數
            'expiry_receiving_days', //效期控管 允收期(天)
            'description', //商品內容
            'specification', //商品規格(html編輯器)
            'category_number',//  指定「POS小分類」代碼
            'web_category_hierarchy_ids',//  前台分類
            'selling_channel',
            'lgst_temperature', //配送溫層
            'storage_temperature', //存放溫層
        ]);
        $datekey = [];
        $index = 0;
        foreach ($keys as $k) {
            if ($k == 'promotion_start_at' || $k == 'promotion_end_at') {
                array_push($datekey, $index);
            }
            $index += 1;
        }
        foreach ($datekey as $key) {
            $date = [] ;
            $date['status']   = true ;
            $date['date']     = ''   ; 
            $date['old_date'] = $row[$key] ; 

            if ($row[$key] == null) {
                $date['date'] = '';
            } elseif (is_string($row[$key])) {
                try {
                    $date['date']   = Carbon::createFromFormat('Y-m-d H:i', $row[$key]);
                } catch (\Throwable $th) {
                    $date['status'] = false;
                }
            } else {
                try {
                    $date['old_date'] = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[$key]))->format('Y-m-d H:i') ;
                    $date['date']     = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[$key]));
                } catch (\Throwable $th) {
                    $date['status'] = false;
                }
            }
            $row[$key] = $date ;
        }

        $values = collect($row)->values();
        $result = $keys->combine($values)->toArray();
        $result['rowNum'] = ++$this->rowNum;

        return $result;
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
        return 'AU';
    }

}
