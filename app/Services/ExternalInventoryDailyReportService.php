<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExternalInventoryDailyReportService
{
    private function handleBuilder($builder, $request)
    {
        //庫存日期
        if (empty($request['counting_date']) === false) {
            $builder = $builder->where('counting_date', $request['counting_date']);
        }

        //倉庫
        if (empty($request['warehouse']) === false) {
            $builder = $builder->where('w.id', $request['warehouse']);
        }

        //庫存類型
        if (empty($request['stock_type']) === false) {
            $builder = $builder->where('p.stock_type', $request['stock_type']);
        }

        //Item編號 開始
        if (empty($request['item_no_start']) === false) {
            $builder = $builder->where('pi.item_no', '>=', $request['item_no_start']);
        }

        //Item編號 結束
        if (empty($request['item_no_end']) === false) {
            $builder = $builder->where('pi.item_no', '<=', $request['item_no_end']);
        }

        //商品名稱
        if (empty($request['product_name']) === false) {
            $builder = $builder->where('p.product_name', 'like', sprintf('%%%s%%', $request['product_name']));
        }

        //供應商
        if (empty($request['supplier_id']) === false) {
            $builder = $builder->where('p.supplier_id', $request['supplier_id']);
        }

        return $builder;
    }

    /**
     * 取得列表資料
     * @param array $request
     * @return mixed
     * @Author: Eric
     * @DateTime: 2022/1/20 上午 09:26
     */
    public function getIndexData($request = [])
    {
        $select1 = "counting_date, warehouse_name, item_no,
            product_name, spec_1_value, spec_2_value, pos_item_no,
            stock_type, supplier_name, expiry_date,
            is_additional_purchase, safty_qty, stock_qty,
            selling_price, item_cost,
            round( ( 1 - ( item_cost / selling_price) ) * 100, 2) as gross_margin,
            stock_qty * item_cost as stock_amount,
            (case when section_key = 'N' and goods_summary.goods_qty < i.safty_qty then 1 else 0 end) as is_dangerous /* 良品倉需比對是否低於安全庫存 */";

        $select2 = "ci.counting_date, w.number as warehouse_code, w.name as warehouse_name, ci.section_key, pi.item_no, ci.product_item_id,
            p.product_name, pi.spec_1_value, pi.spec_2_value, ci.sku as pos_item_no,
            p.stock_type, s.name as supplier_name, ci.expiry_date,
            pi.is_additional_purchase, pi.safty_qty, ci.qty as stock_qty,
            (case when p.tax_type = 'TABLEABLE' then round(p.selling_price/1.05, 2) else selling_price end) as selling_price,
            get_latest_product_cost(p.id, false) as item_cost";

        $sub_query = DB::table('choice_inventory')
            ->selectRaw('product_item_id, sum(qty) as goods_qty')
            ->where('counting_date', $request['counting_date'])
            ->where('section_key', 'N')
            ->groupBy('product_item_id');

        $builder = DB::query()
            ->selectRaw($select1)
            ->fromSub(function ($query) use ($select2, $request) {
                $builder = $query->from('choice_inventory as ci')
                    ->selectRaw($select2)
                    ->join('warehouse as w', 'w.id', '=', 'ci.warehouse_id')
                    ->leftJoin('products as p', 'p.id', '=', 'ci.product_id')
                    ->leftJoin('product_items as pi', 'pi.id', '=', 'ci.product_item_id')
                    ->leftJoin('supplier as s', 's.id', '=', 'p.supplier_id');

                return $this->handleBuilder($builder, $request);
            }, 'i')
            ->leftJoinSub($sub_query, 'goods_summary', 'goods_summary.product_item_id', '=', 'i.product_item_id');

        //庫存狀態
        if (isset($request['is_dangerous'])) {
            $builder = $builder->having('is_dangerous', $request['is_dangerous']);
        }

        $builder->orderBy('item_no');
        $builder->orderBy('warehouse_name');

        return $builder->get();
    }

    /**
     * 處理列表資料
     * @param Collection $collection
     * @return Collection
     * @Author: Eric
     * @DateTime: 2022/1/20 上午 09:26
     */
    public function handleIndexData(Collection $collection)
    {
        return $collection->map(function ($item) {

            switch ($item->stock_type) {
                case 'A':
                    $stock_type_chinese = '買斷[A]';
                    break;
                case 'B':
                    $stock_type_chinese = '寄售[B]';
                    break;
                default:
                    $stock_type_chinese = '';
            }

            $item->stock_type = $stock_type_chinese;

            //是否追加
            $item->is_additional_purchase = $item->is_additional_purchase == 1 ? '是' : '否';
            //安全庫存量
            $item->safty_qty = is_null($item->safty_qty) ? null : number_format($item->safty_qty);
            //庫存量(計算總量用)
            $item->original_stock_qty = $item->stock_qty;
            //庫存量
            $item->stock_qty = is_null($item->stock_qty) ? null : number_format($item->stock_qty);
            //售價
            $item->selling_price = is_null($item->selling_price) ? null : number_format($item->selling_price);
            //平均成本
            $item->item_cost = is_null($item->item_cost) ? null : number_format($item->item_cost, 2);
            //毛利率
            $item->gross_margin = is_null($item->gross_margin) ? null : number_format($item->gross_margin, 2);
            //庫存成本
            $item->stock_amount = is_null($item->stock_amount) ? null : number_format($item->stock_amount);

            return $item;
        });
    }

    /**
     * 處理excel資料
     * @param Collection $collection
     * @return Collection
     * @Author: Eric
     * @DateTime: 2022/1/20 上午 10:08
     */
    public function handleExcelData(Collection $collection)
    {
        return $collection->map(function ($item) {

            switch ($item->stock_type) {
                case 'A':
                    $stock_type_chinese = '買斷[A]';
                    break;
                case 'B':
                    $stock_type_chinese = '寄售[B]';
                    break;
                default:
                    $stock_type_chinese = '';
            }

            $item->stock_type = $stock_type_chinese;

            //是否追加
            $item->is_additional_purchase = $item->is_additional_purchase == 1 ? '是' : '否';
            //安全庫存量
            $item->safty_qty = is_null($item->safty_qty) ? null : number_format($item->safty_qty);
            //庫存量(計算總量用)
            $item->original_stock_qty = $item->stock_qty;
            //庫存量
            $item->stock_qty = is_null($item->stock_qty) ? null : number_format($item->stock_qty);
            //售價
            $item->selling_price = is_null($item->selling_price) ? null : number_format($item->selling_price);
            //平均成本
            $item->item_cost = is_null($item->item_cost) ? null : number_format($item->item_cost, 2);
            //毛利率
            $item->gross_margin = is_null($item->gross_margin) ? null : number_format($item->gross_margin, 2);
            //庫存成本
            $item->stock_amount = is_null($item->stock_amount) ? null : number_format($item->stock_amount);
            //低於安全庫存量
            $item->is_dangerous = $item->is_dangerous == 1 ? '是' : '否';

            return [
                (string) $item->counting_date,
                (string) $item->warehouse_name,
                (string) $item->item_no,
                (string) $item->product_name,
                (string) $item->spec_1_value,
                (string) $item->spec_2_value,
                (string) $item->pos_item_no,
                (string) $item->stock_type,
                (string) $item->supplier_name,
                (string) $item->expiry_date,
                (string) $item->is_additional_purchase,
                (string) $item->safty_qty,
                (string) $item->stock_qty,
                (string) $item->selling_price,
                (string) $item->item_cost,
                (string) $item->gross_margin,
                (string) $item->stock_amount,
                (string) $item->is_dangerous,
            ];
        });
    }
}
