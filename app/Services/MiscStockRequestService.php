<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\MiscStockRequest;
use Illuminate\Support\Collection;
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
                $query->where('idd', $data['supplierId']);
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
}
