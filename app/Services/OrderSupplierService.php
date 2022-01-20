<?php

namespace App\Services;

use App\Models\OrderSupplier;
use App\Models\OrderSupplierDetail;
use App\Models\RequisitionsPurchase;
use Batch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderSupplierService
{
    private $universalService;
    private $itemService;
    public function __construct(UniversalService $universalService, ItemService $itemService)
    {
        $this->universalService = $universalService;
        $this->itemService = $itemService;
    }

    public function getOrderSupplier($data)
    {
        $agent_id = Auth::user()->agent_id;

        $result = OrderSupplier::select(DB::raw('order_supplier.*'),
            DB::raw('requisitions_purchase.number as requisitions_purchase_number'),
            'supplier_deliver_date', 'expect_deliver_date')
            ->where('order_supplier.agent_id', $agent_id)
            ->leftJoin('supplier', 'order_supplier.supplier_id', '=', 'supplier.id')
            ->leftJoin('requisitions_purchase', 'order_supplier.requisitions_purchase_id', '=', 'requisitions_purchase.id');

        if (isset($data['supplier'])) {
            $result->where('order_supplier.supplier_id', $data['supplier']);
        }

        if (isset($data['company_number'])) {
            $result->where('company_number', 'like', '%' . $data['company_number'] . '%');
        }

        if (isset($data['status'])) {
            $result->where('order_supplier.status', $data['status']);
        }

        if (isset($data['select_start_date']) && isset($data['select_end_date'])) {
            $result->whereBetween('order_supplier.trade_date', [$data['select_start_date'], $data['select_end_date']]);
        }

        if (isset($data['order_number'])) {
            $result->where('order_supplier.number', 'like', '%' . $data['order_number'] . '%');
        }

        if (isset($data['requisitions_purchase_number'])) {
            $result->where('requisitions_purchase.number', 'like', '%' . $data['requisitions_purchase_number'] . '%');
        }
        $result->orderBy('order_supplier.number' ,'DESC');
        $result = $result->get();

        return $result;
    }

    public function getOrderSupplierById($id)
    {
        return OrderSupplier::select(DB::raw('order_supplier.*'),
            DB::raw('requisitions_purchase.warehouse_id as warehouse_id'),
            DB::raw('warehouse.name as warehouse_name'),
            DB::raw('supplier.name as supplier_name'),
            DB::raw('requisitions_purchase.number as requisitions_purchase_number')
        )
            ->where('order_supplier.id', $id)
            ->leftJoin('supplier', 'supplier.id', '=', 'order_supplier.supplier_id')
            ->leftJoin('requisitions_purchase', 'requisitions_purchase.id', '=', 'order_supplier.requisitions_purchase_id')
            ->leftJoin('warehouse', 'warehouse.id', '=', 'warehouse_id')
            ->first();
    }

    /*
     *每次刪除採購單要更改請購單的畫面
     */
    public function delOrderSupplierById($id)
    {
        $OrderSupplier = $this->getOrderSupplierById($id);
        $requisitions_purchase_id = $OrderSupplier->requisitions_purchase_id;
        RequisitionsPurchase::where('id', $requisitions_purchase_id)->update(['is_transfer' => 0]);
        OrderSupplier::where('id', '=', $id)->delete();
        return true;
    }

    public function getOrderSupplierDetail($order_supplier_id)
    {
        $result = OrderSupplierDetail::select(
            DB::raw('order_supplier_detail.*'),
            DB::raw('product_items.product_id as product_id'),
            DB::raw('product_items.spec_1_value as spec_1_value'),
            DB::raw('product_items.spec_2_value as spec_2_value'),
            DB::raw('product_items.pos_item_no as pos_item_no'),
            DB::raw('product_items.ean as ean'),
            DB::raw('products.product_name as product_name'),
            DB::raw('products.uom as uom'),
            DB::raw('products.brand_id as brand_id'),
            DB::raw('product_items.item_no as product_items_no'),
            DB::raw('products.min_purchase_qty as min_purchase_qty'),
        )
            ->where('order_supplier_detail.order_supplier_id', $order_supplier_id)
            ->leftJoin('product_items', 'product_items.id', 'order_supplier_detail.product_item_id')
            ->leftJoin('products', 'products.id', 'product_items.product_id')
            ->get();
        return $result;
    }
    public function updateSupplierDeliverTime($in)
    {
        $user_id = Auth::user()->id;

        $result = OrderSupplier::where('id', $in['id'])->update([
            'supplier_deliver_date' => $in['supplier_deliver_date'],
            'expect_deliver_date' => $in['expect_deliver_date'],
            'updated_by' => $user_id,
        ]);
        return $result;
    }

    public function updateOrderSupplier($data, $act)
    {

        $now = Carbon::now();
        $user_id = Auth::user()->id;
        $agent_id = Auth::user()->agent_id;

        DB::beginTransaction();
        try {
            $orderSupplierData = [
                'trade_date' => $data['trade_date'],
                'total_tax_price' => $data['total_tax_price'],
                'total_price' => $data['total_price'],
                'original_total_tax_price' => $data['original_total_tax_price'],
                'original_total_price' => $data['original_total_price'],
                'currency_id' => $data['currency_id'],
                'currency_code' => $data['currency_code'],
                'currency_price' => $data['currency_price'],
                'tax' => $data['tax'],
                'requisitions_purchase_id' => $data['requisitions_purchase_id'],
                'receiver_name' => $data['receiver_name'],
                'receiver_address' => $data['receiver_address'],
                'invoice_company_number' => $data['invoice_company_number'],
                'invoice_name' => $data['invoice_name'],
                'invoice_address' => $data['invoice_address'],
                'supplier_deliver_date' => $data['supplier_deliver_date'],
                'expect_deliver_date' => $data['expect_deliver_date'],
                'remark' => $data['remark'],
                'status' => $data['status_code'],
                'updated_by' => $user_id,
                'updated_at' => $now,
            ];

            if ($act == 'add') {
                $orderSupplierData['agent_id'] = $agent_id;
                $orderSupplierData['created_at'] = $now;
                $orderSupplierData['created_by'] = $user_id;
                $orderSupplierData['supplier_id'] = $data['supplier_id'];
                $orderSupplierData['number'] = $this->universalService->getDocNumber('order_supplier');
                RequisitionsPurchase::where('id', $data['requisitions_purchase_id'])->update(['is_transfer' => 1]);
                $order_supplier_id = OrderSupplier::insertGetId($orderSupplierData);
            } elseif ($act == 'upd') {
                OrderSupplier::where('id', $data['id'])->update($orderSupplierData);
                $order_supplier_id = $data['id'];
            }

            $orderSupplierDetailData = [];

            $order_supplier_detail = json_decode($data['order_supplier_detail_json'], true);
            foreach ($order_supplier_detail as $key => $val) {
                $orderSupplierDetailData[$key] = [
                    'order_supplier_id' => $order_supplier_id,
                    'requisitions_purchase_dtl_id' => $val['requisitions_purchase_dtl_id'],
                    'product_item_id' => $val['product_item_id'],
                    'item_no' => $val['item_no'],
                    'item_qty' => $val['item_qty'],
                    'item_price' => $val['item_price'],
                    'subtotal_price' => $val['subtotal_price'],
                    'original_subtotal_price' => $val['original_subtotal_price'],
                    'currency_id' => '1', //目前暫未開放
                    'currency_code' => 'TWD', //目前暫未開放
                    'currency_price' => '1', //目前暫未開放
                    'is_giveaway' => $val['is_giveaway'],
                    'purchase_qty' => $val['purchase_qty'],
                    'created_at' => $now,
                    'updated_at' => $now,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'pos_item_no' => $val['pos_item_no'],

                ];
                if ($act == 'upd') {
                    $orderSupplierDetailData[$key]['id'] = $val['id'];
                    unset($orderSupplierDetailData[$key]['created_by']);
                    unset($orderSupplierDetailData[$key]['created_at']);
                }
            }

            $orderSupplierDetailInstance = new OrderSupplierDetail();
            if ($act == 'add') {
                $orderSupplierDetailInstance->insert($orderSupplierDetailData);
            } elseif ($act == 'upd') {
                Batch::update($orderSupplierDetailInstance, $orderSupplierDetailData, 'id');
            }
            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            $result = false;
        }
        return $result;
    }
}
