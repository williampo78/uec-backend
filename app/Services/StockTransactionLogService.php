<?php

namespace App\Services;

use App\Models\StockTransactionLog;
use Illuminate\Support\Collection;

class StockTransactionLogService
{
    /**
     * 取得列表資料
     * @param array $payload
     * @return Collection
     * @Author: Eric
     * @DateTime: 2022/7/29 上午 11:13
     */
    public function getIndexData(array $payload = []): Collection
    {
        $builder = StockTransactionLog::select(['id', 'transaction_type', 'transaction_date', 'warehouse_id', 'product_item_id', 'transaction_qty', 'source_doc_no', 'source_table_name'])
            ->with(['warehouse:id,name', 'productItem' => function ($query) {
                $query->select(['id', 'product_id', 'spec_1_value', 'spec_2_value', 'item_no'])
                    ->with(['product:id,stock_type,product_no,product_name']);
            }])
            ->has('productItem')
            //異動日期開始
            ->when(empty($payload['dateStart']) === false, function ($query) use ($payload) {
                $query->where('transaction_date', '>=', $payload['dateStart']);
            })
            //異動日期結束
            ->when(empty($payload['dateEnd']) === false, function ($query) use ($payload) {
                $query->where('transaction_date', '<=', $payload['dateEnd']);
            })
            //倉庫
            ->when(empty($payload['warehouse']) === false, function ($query) use ($payload) {
                $query->whereHas('warehouse', function ($query) use ($payload) {
                    $query->where('id', $payload['warehouse']);
                });
            })
            //供應商
            ->when(empty($payload['supplierId']) === false, function ($query) use ($payload) {
                $query->whereHas('productItem.product', function ($query) use ($payload) {
                    $query->where('supplier_id', $payload['supplierId']);
                });
            })
            //Item編號
            ->when(empty($payload['itemNoStart']) === false, function ($query) use ($payload) {
                $query->where('item_no', '>=', $payload['itemNoStart']);
            })
            //Item編號
            ->when(empty($payload['itemNoEnd']) === false, function ($query) use ($payload) {
                $query->where('item_no', '<=', $payload['itemNoEnd']);
            })
            //來源單據名稱
            ->when(empty($payload['sourceTableName']) === false, function ($query) use ($payload) {

                list($sourceTableName, $transactionType) = explode('-', $payload['sourceTableName']);

                $query->where('source_table_name', $sourceTableName)
                    ->when(empty($transactionType) === false, function ($query) use ($transactionType) {
                        $query->where('transaction_type', $transactionType);
                    });
            })
            //來源單號
            ->when(empty($payload['sourceDocNo']) === false, function ($query) use ($payload) {
                $query->where('source_doc_no', 'like', "%{$payload['sourceDocNo']}%");
            })
            //商品序號
            ->when(empty($payload['productNoStart']) === false, function ($query) use ($payload) {
                $query->whereHas('productItem.product', function ($query) use ($payload) {
                    $query->where('product_no', '>=', $payload['productNoStart']);
                });
            })
            //商品序號
            ->when(empty($payload['productNoEnd']) === false, function ($query) use ($payload) {
                $query->whereHas('productItem.product', function ($query) use ($payload) {
                    $query->where('product_no', '<=', $payload['productNoEnd']);
                });
            })
            //庫存類型
            ->when(empty($payload['stockType']) === false, function ($query) use ($payload) {
                $query->whereHas('productItem.product', function ($query) use ($payload) {
                    $query->where('stock_type', $payload['stockType']);
                });
            })
            ->orderBy('transaction_date', 'asc')
            ->orderBy('item_no', 'asc')
            //先寫死不帶參數
            ->limit(500);

        return $builder->get();
    }

    /**
     * 處理列表資料
     * @param Collection $collection
     * @return Collection
     * @Author: Eric
     * @DateTime: 2022/7/29 上午 11:16
     */
    public function handleIndexData(Collection $collection): Collection
    {
        $stockTypes       = config('uec.stock_type_options');
        $transactionTypes = config('uec.transaction_type');
        $sourceTableNames = config('uec.source_table_name');

        return $collection->map(function ($stockTransactionLog) use ($stockTypes, $transactionTypes, $sourceTableNames) {

            return [
                'id'                => $stockTransactionLog->id,
                'transaction_date'  => $stockTransactionLog->transaction_date ?? null,
                'product_no'        => $stockTransactionLog->productItem->product->product_no ?? null,
                'stock_type'        => $stockTypes[$stockTransactionLog->productItem->product->stock_type ?? null] ?? null,
                'item_no'           => $stockTransactionLog->productItem->item_no ?? null,
                'product_name'      => $stockTransactionLog->productItem->product->product_name ?? null,
                'spec_1_value'      => $stockTransactionLog->productItem->spec_1_value ?? null,
                'spec_2_value'      => $stockTransactionLog->productItem->spec_2_value ?? null,
                'transaction_qty'   => $stockTransactionLog->transaction_qty ?? null,
                'warehouse_name'    => $stockTransactionLog->warehouse->name ?? null,
                'transaction_type'  => $transactionTypes[$stockTransactionLog->transaction_type ?? null] ?? null,
                'source_doc_no'     => $stockTransactionLog->source_doc_no ?? null,
                'source_table_name' => $sourceTableNames[$stockTransactionLog->source_table_name ?? null]['chinese_name'] ?? null,
            ];
        });
    }
}
