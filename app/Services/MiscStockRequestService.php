<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\ProductItem;
use App\Models\MiscStockRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class MiscStockRequestService
{
    /**
     * 取得進貨退出單列表
     *
     * @param array $data
     * @return Collection
     */
    public function getStockRequestTableList(array $data = []): Collection
    {
        $miscStockRequests = MiscStockRequest::with([
            'suppliers',
        ]);

        // 申請單時間-開始日期
        if (isset($data['requestDateStart'])) {
            $miscStockRequests = $miscStockRequests->whereDate('request_date', '>=', $data['requestDateStart']);
        }

        // 申請單時間-結束日期
        if (isset($data['requestDateEnd'])) {
            $miscStockRequests = $miscStockRequests->whereDate('request_date', '<=', $data['requestDateEnd']);
        }

        // 申請單號
        if (isset($data['requestNo'])) {
            $miscStockRequests = $miscStockRequests->where('request_no', $data['requestNo']);
        }

        // 狀態
        if (isset($data['statusCode'])) {
            $miscStockRequests = $miscStockRequests->whereHas('suppliers', function (Builder $query) use ($data) {
                $query->where('misc_stock_request_suppliers.status_code', $data['statusCode']);
            });
        }

        // 實際出入庫日期-開始日期
        if (isset($data['actualDateStart'])) {
            $miscStockRequests = $miscStockRequests->whereDate('actual_date', '>=', $data['actualDateStart']);
        }

        // 實際出入庫日期-結束日期
        if (isset($data['actualDateEnd'])) {
            $miscStockRequests = $miscStockRequests->whereDate('actual_date', '<=', $data['actualDateEnd']);
        }

        // 商品序號
        if (isset($data['productNo'])) {
            $productNos = explode(',', $data['productNo']);
            $productNos = array_unique($productNos);

            if (!empty($productNos)) {
                $miscStockRequests = $miscStockRequests->whereHas('miscStockRequestDetails.productItem.product', function (Builder $query) use ($productNos) {
                    foreach ($productNos as $productNo) {
                        $query->orWhere('product_no', 'like', "%{$productNo}%");
                    }
                });
            }
        }

        // 供應商
        if (isset($data['supplierId'])) {
            $miscStockRequests = $miscStockRequests->whereHas('suppliers', function (Builder $query) use ($data) {
                $query->where('supplier.id', $data['supplierId']);
            });
        }

        // 限制筆數
        if (isset($data['limit'])) {
            $miscStockRequests = $miscStockRequests->limit($data['limit']);
        }

        return $miscStockRequests->latest('request_no')->get();
    }

    /**
     * 整理進貨退出單列表
     *
     * @param Collection $miscStockRequests
     * @return array
     */
    public function formatStockRequestTableList(Collection $miscStockRequests): array
    {
        $result = [];

        foreach ($miscStockRequests as $stockRequest) {
            $tmpStockRequest = [
                'id' => $stockRequest->id,
                'request_no' => $stockRequest->request_no,
                'request_date' => Carbon::parse($stockRequest->request_date)->format('Y-m-d H:i'),
                'expected_date' => Carbon::parse($stockRequest->expected_date)->format('Y-m-d'),
                'submitted_at' => isset($stockRequest->submitted_at) ? Carbon::parse($stockRequest->submitted_at)->format('Y-m-d H:i') : null,
                'supplier_count' => 0,
                'approved_supplier_count' => 0,
                'rejected_supplier_count' => 0,
                'approved_at' => isset($stockRequest->approved_at) ? Carbon::parse($stockRequest->approved_at)->format('Y-m-d H:i') : null,
                'edi_exported_at' => isset($stockRequest->edi_exported_at) ? Carbon::parse($stockRequest->edi_exported_at)->format('Y-m-d H:i') : null,
                'actual_date' => isset($stockRequest->actual_date) ? Carbon::parse($stockRequest->actual_date)->format('Y-m-d H:i') : null,
            ];

            if ($stockRequest->suppliers->isNotEmpty()) {

            }

            $result[] = $tmpStockRequest;
        }

        return $result;
    }

    /**
     * 取得品項modal的列表
     *
     * @param array $data
     * @return Collection
     */
    public function getProductItemModalList(array $data = []): Collection
    {
        $user = auth()->user();
        $productItems = ProductItem::with([
            'product' => function ($query) {
                $query->select()->addSelect(DB::raw('get_latest_product_cost(id, TRUE) AS item_cost'));
            },
            'product.supplier',
            'warehouses' => function ($query) use ($data) {
                $query->where('warehouse.id', $data['warehouseId']);
            },
        ])->where('agent_id', $user->agent_id);

        $productItems = $productItems->whereHas('warehouses', function (Builder $query) use ($data) {
            $query->where('warehouse.id', $data['warehouseId'])->where('warehouse_stock.stock_qty', '>', 0);
        });

        // 商品序號
        if (isset($data['productNo'])) {
            $productNos = explode(',', $data['productNo']);
            $productNos = array_unique($productNos);

            if (!empty($productNos)) {
                $productItems = $productItems->whereHas('product', function (Builder $query) use ($productNos) {
                    $query->where(function ($query) use ($productNos) {
                        foreach ($productNos as $productNo) {
                            $query->orWhere('product_no', 'like', "%{$productNo}%");
                        }
                    });
                });
            }
        }

        // 商品名稱
        if (isset($data['productName'])) {
            $productItems = $productItems->whereHas('product', function (Builder $query) use ($data) {
                $query->where('product_name', 'like', "%{$data['productName']}%");
            });
        }

        // 供應商
        if (isset($data['supplierId'])) {
            $productItems = $productItems->whereHas('product.supplier', function (Builder $query) use ($data) {
                $query->where('supplier_id', $data['supplierId']);
            });
        }

        // 限制筆數
        if (isset($data['limit'])) {
            $productItems = $productItems->limit($data['limit']);
        }

        // 排除已存在的品項
        if (!empty($data['excludeProductItemIds'])) {
            $productItems = $productItems->whereNotIn('id', $data['excludeProductItemIds']);
        }

        return $productItems->oldest('id')->get();
    }

    /**
     * 整理品項modal的列表
     *
     * @param Collection $list
     * @return array
     */
    public function formatProductItemModalList(Collection $list): array
    {
        $result = [];
        foreach ($list as $item) {
            $tmpItem = [
                'id' => $item->id,
                'productNo' => null,
                'productName' => null,
                'itemNo' => $item->item_no,
                'spec1Value' => $item->spec_1_value,
                'spec2Value' => $item->spec_2_value,
                'supplierName' => null,
                'stockQty' => null,
                'unitPrice' => null,
                'uom' => null,
            ];

            if (isset($item->product)) {
                $tmpItem['productNo'] = $item->product->product_no;
                $tmpItem['productName'] = $item->product->product_name;
                $tmpItem['unitPrice'] = $item->product->item_cost;
                $tmpItem['uom'] = $item->product->uom;

                if (isset($item->product->supplier)) {
                    $tmpItem['supplierName'] = $item->product->supplier->name;
                }
            }

            $warehouse = $item->warehouses->first();
            if (isset($warehouse)) {
                $tmpItem['stockQty'] = $warehouse->pivot->stock_qty;
            }

            $result[] = $tmpItem;
        }

        return $result;
    }
}
