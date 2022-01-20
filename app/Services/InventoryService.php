<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\Shipment;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\InvoiceDetail;
use App\Models\ProductPhotos;
use App\Models\ShipmentDetail;
use App\Models\InvoiceAllowance;
use App\Models\SysConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrderCampaignDiscount;
use App\Models\InvoiceAllowanceDetail;

class InventoryService
{
    public function getInventories($request = [])
    {
        $sysConfig = SysConfig::where('agent_id', Auth::user()->agent_id)
            ->where('config_key', config('uec.config_key'))
            ->where('active', 1)
            ->first();

        $configValue = $sysConfig->config_value ?? null;

        $select = "w.name, pi.item_no, p.product_name, pi.spec_1_value, pi.spec_2_value, pi.pos_item_no,
        p.stock_type, pi.safty_qty, ws.stock_qty,
        p.selling_price, p.item_cost, p.gross_margin,
        (p.item_cost * ws.stock_qty) as stock_amount,
        (case when w.number = '${configValue}' and ws.stock_qty < pi.safty_qty then 1 else 0 end) as `is_dangerous`";

        $builder = DB::table('warehouse_stock as ws')
            ->selectRaw($select)
            ->join('warehouse as w', 'w.id', '=', 'ws.warehouse_id')
            ->join('product_items as pi', 'pi.id', '=', 'ws.product_item_id')
            ->join('products_v as p', 'p.id', '=', 'pi.product_id')
            ->orderBy('pi.item_no')
            ->orderBy('w.name');

        //倉庫
        if (empty($request['warehouse']) === false) {
            $builder = $builder->where('w.id', $request['warehouse']);
        }

        //庫存類型
        if (empty($request['stock_type']) === false) {
            $builder = $builder->where('p.stock_type', $request['stock_type']);
        }

        //庫存狀態
        if (isset($request['stock_status'])) {
            $builder = $builder->having('is_dangerous', $request['stock_status']);
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
        if (empty($request['supplier']) === false) {
            $builder = $builder->where('p.supplier_id', $request['supplier']);
        }

        return $builder->get();
    }

    public function handleInventories(collection $inventories)
    {
        return $inventories->map(function ($inventory) {

            switch ($inventory->stock_type) {
                case 'A':
                    $stock_type_chinese = '買斷[A]';
                    break;
                case 'B':
                    $stock_type_chinese = '寄售[B]';
                    break;
                default:
                    $stock_type_chinese = '';
            }

            $inventory->stock_type = $stock_type_chinese;
            //安全庫存
            $inventory->safty_qty = is_null($inventory->safty_qty) ? null : number_format($inventory->safty_qty);
            //庫存量(計算總量用)
            $inventory->original_stock_qty = $inventory->stock_qty;
            //庫存量
            $inventory->stock_qty = is_null($inventory->stock_qty) ? null : number_format($inventory->stock_qty);
            //毛利率
            $inventory->gross_margin = is_null($inventory->gross_margin) ? null : $inventory->gross_margin . '%';
            //售價
            $inventory->selling_price = is_null($inventory->selling_price) ? null : number_format($inventory->selling_price, 2);
            //平均成本
            $inventory->item_cost = is_null($inventory->item_cost) ? null : number_format($inventory->item_cost, 2);
            //庫存成本
            $inventory->stock_amount = is_null($inventory->stock_amount) ? null : number_format($inventory->stock_amount, 2);
            return $inventory;
        });
    }
}
