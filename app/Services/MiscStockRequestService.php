<?php

namespace App\Services;

use App\Models\MiscStockRequest;
use App\Models\MiscStockRequestSupplier;
use App\Models\ProductItem;
use App\Services\MoneyAmount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        return MiscStockRequest::latest('request_no')
        // 申請單時間-開始日期
            ->when(isset($data['request_date_start']), function ($query) use ($data) {
                $query->whereDate('request_date', '>=', $data['request_date_start']);
            })
        // 申請單時間-結束日期
            ->when(isset($data['request_date_end']), function ($query) use ($data) {
                $query->whereDate('request_date', '<=', $data['request_date_end']);
            })
        // 申請單號
            ->when(isset($data['request_no']), function ($query) use ($data) {
                $query->where('request_no', $data['request_no']);
            })
        // 狀態
            ->when(isset($data['request_status']), function ($query) use ($data) {
                $query->where('request_status', $data['request_status']);
            })
        // 實際出入庫日期-開始日期
            ->when(isset($data['actual_date_start']), function ($query) use ($data) {
                $query->whereDate('actual_date', '>=', $data['actual_date_start']);
            })
        // 實際出入庫日期-結束日期
            ->when(isset($data['actual_date_end']), function ($query) use ($data) {
                $query->whereDate('actual_date', '<=', $data['actual_date_end']);
            })
        // 商品序號
            ->when(isset($data['product_no']), function ($query) use ($data) {
                $productNos = explode(',', $data['product_no']);
                $productNos = array_unique($productNos);

                if (!empty($productNos)) {
                    $query->whereHas('miscStockRequestDetails.productItem.product', function (Builder $query) use ($productNos) {
                        $query->where(function ($query) use ($productNos) {
                            foreach ($productNos as $productNo) {
                                $query->orWhere('product_no', 'like', "%{$productNo}%");
                            }
                        });
                    });
                }
            })
        // 供應商
            ->when(isset($data['supplier_id']), function ($query) use ($data) {
                $query->whereHas('suppliers', function (Builder $query) use ($data) {
                    $query->where('supplier.id', $data['supplier_id']);
                });
            })
        // 限制筆數
            ->when(isset($data['limit']), function ($query) use ($data) {
                $query->limit($data['limit']);
            })
            ->get();
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

        foreach ($miscStockRequests as $request) {
            $tmpStockRequest = [
                'id' => $request->id,
                'request_no' => $request->request_no,
                'request_date' => $request->request_date,
                'expected_date' => $request->expected_date,
                'submitted_at' => $request->submitted_at,
                'total_sup_count' => $request->total_sup_count,
                'approved_sup_count' => $request->approved_sup_count,
                'rejected_sup_count' => $request->rejected_sup_count,
                'approved_at' => $request->approved_at,
                'edi_exported_at' => $request->edi_exported_at,
                'actual_date' => $request->actual_date,
                'request_status' => $request->request_status,
            ];

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
                $query->where('warehouse.id', $data['warehouse_id']);
            },
        ])
            ->where('agent_id', $user->agent_id)
        // 庫存必需大於0
            ->whereHas('warehouses', function (Builder $query) use ($data) {
                $query->where('warehouse.id', $data['warehouse_id'])->where('warehouse_stock.stock_qty', '>', 0);
            })
        // 庫存類型:買斷
            ->whereHas('product', function (Builder $query) {
                $query->where('stock_type', 'A');
            })
        // 商品序號
            ->when(isset($data['product_no']), function ($query) use ($data) {
                $productNos = explode(',', $data['product_no']);
                $productNos = array_unique($productNos);

                if (!empty($productNos)) {
                    $query->whereHas('product', function (Builder $query) use ($productNos) {
                        $query->where(function ($query) use ($productNos) {
                            foreach ($productNos as $productNo) {
                                $query->orWhere('product_no', 'like', "%{$productNo}%");
                            }
                        });
                    });
                }
            })
        // 商品名稱
            ->when(isset($data['product_name']), function ($query) use ($data) {
                $query->whereHas('product', function (Builder $query) use ($data) {
                    $query->where('product_name', 'like', "%{$data['product_name']}%");
                });
            })
        // 供應商
            ->when(isset($data['supplier_id']), function ($query) use ($data) {
                $query->whereHas('product.supplier', function (Builder $query) use ($data) {
                    $query->where('supplier_id', $data['supplier_id']);
                });
            })
        // 限制筆數
            ->when(isset($data['limit']), function ($query) use ($data) {
                $query->limit($data['limit']);
            })
        // 排除已存在的品項
            ->when(!empty($data['exclude_product_item_ids']), function ($query) use ($data) {
                $query->whereNotIn('id', $data['exclude_product_item_ids']);
            })
            ->oldest('id')
            ->get();

        return $productItems;
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
                'product_no' => null,
                'product_name' => null,
                'item_no' => $item->item_no,
                'spec_1_value' => $item->spec_1_value,
                'spec_2_value' => $item->spec_2_value,
                'supplier_id' => null,
                'supplier_name' => null,
                'stock_qty' => null,
                'unit_price' => null,
                'uom' => null,
            ];

            if (isset($item->product)) {
                $tmpItem['product_no'] = $item->product->product_no;
                $tmpItem['product_name'] = $item->product->product_name;
                $tmpItem['unit_price'] = $item->product->item_cost;
                $tmpItem['uom'] = $item->product->uom;

                if (isset($item->product->supplier)) {
                    $tmpItem['supplier_id'] = $item->product->supplier->id;
                    $tmpItem['supplier_name'] = $item->product->supplier->name;
                }
            }

            $warehouse = $item->warehouses->first();
            if (isset($warehouse)) {
                $tmpItem['stock_qty'] = $warehouse->pivot->stock_qty;
            }

            $result[] = $tmpItem;
        }

        return $result;
    }

    /**
     * 建立進貨退出單
     *
     * @param array $data
     * @return boolean
     */
    public function createStockRequest(array $data): bool
    {
        $user = auth()->user();
        $result = false;

        DB::beginTransaction();
        try {
            $requestData = [
                'request_no' => $this->generateNumber(),
                'request_type' => 'ISSUE',
                'request_date' => now(),
                'warehouse_id' => $data['warehouse_id'],
                'expected_qty' => 0,
                'tax' => $data['tax'],
                'expected_amount' => 0,
                'expected_tax_amount' => 0,
                'total_sup_count' => 0,
                'approved_sup_count' => 0,
                'rejected_sup_count' => 0,
                'ship_to_name' => $data['ship_to_name'] ?? null,
                'ship_to_mobile' => $data['ship_to_mobile'] ?? null,
                'ship_to_address' => $data['ship_to_address'] ?? null,
                'remark' => $data['remark'] ?? null,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ];

            // 儲存類型
            if ($data['save_type'] == 'draft') {
                $requestData['request_status'] = 'DRAFTED';
            } else {
                $requestData['request_status'] = 'REVIEWING';
                $requestData['submitted_at'] = now();
            }

            $createdRequest = MiscStockRequest::create($requestData);

            $requestQty = 0;
            $requestAmount = 0;
            $totalSupCount = 0;
            if (isset($data['items'])) {
                // 依供應商分群
                $supplierGroups = collect($data['items'])->groupBy('supplier_id');
                foreach ($supplierGroups as $supplierId => $items) {
                    $requestSupplierData = [
                        'misc_stock_request_id' => $createdRequest->id,
                        'supplier_id' => $supplierId,
                        'expected_qty' => 0,
                        'expected_amount' => 0,
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                    ];

                    // 儲存類型
                    if ($data['save_type'] == 'draft') {
                        $requestSupplierData['status_code'] = 'DRAFTED';
                    } else {
                        $requestSupplierData['status_code'] = 'REVIEWING';
                    }

                    // 建立申請單、供應商的中間表
                    $createdRequestSupplier = MiscStockRequestSupplier::create($requestSupplierData);

                    $requestSupplierQty = 0;
                    $requestSupplierAmount = 0;
                    foreach ($items as $item) {
                        $expectedSubtotal = round($item['unit_price'] * $item['expected_qty']);
                        // 建立申請單明細
                        $createdRequest->miscStockRequestDetails()->create([
                            'misc_stock_request_sup_id' => $createdRequestSupplier->id,
                            'product_item_id' => $item['product_item_id'],
                            'unit_price' => $item['unit_price'],
                            'expected_qty' => $item['expected_qty'],
                            'expected_subtotal' => $expectedSubtotal,
                            'onhand_qty' => $item['stock_qty'],
                            'created_by' => $user->id,
                            'updated_by' => $user->id,
                        ]);

                        $requestSupplierQty += $item['expected_qty'];
                        $requestSupplierAmount += $expectedSubtotal;
                    }

                    // 更新申請單、供應商的中間表數量、金額
                    $createdRequestSupplier->update([
                        'expected_qty' => $requestSupplierQty,
                        'expected_amount' => $requestSupplierAmount,
                    ]);

                    $requestQty += $requestSupplierQty;
                    $requestAmount += $requestSupplierAmount;
                    $totalSupCount++;
                }
            }

            $moneyAmount = MoneyAmount::makeByPrice($requestAmount, (int) $data['tax'])->calculate('local', true);

            // 更新申請單數量、金額
            $createdRequest->update([
                'expected_qty' => $requestQty,
                'expected_amount' => $requestAmount,
                'expected_tax_amount' => $moneyAmount->getTaxPrice(),
                'total_sup_count' => $totalSupCount,
            ]);

            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
     * 產生單號
     *
     * @return string
     */
    public function generateNumber(): string
    {
        $numberHead = 'RS' . now()->format('ymd');

        do {
            $lastNumber = $this->getTodayLastNumber($numberHead);

            if (isset($lastNumber)) {
                $lastNumberTail = (string) Str::of($lastNumber)->substr(8);
                $newNumberTail = (int) $lastNumberTail + 1;
                $newNumber = $numberHead . Str::of($newNumberTail)->padLeft(6, '0');
            } else {
                $newNumberTail = Str::of('1')->padLeft(6, '0');
                $newNumber = $numberHead . $newNumberTail;
            }
        } while ($this->numberExists($newNumber));

        return $newNumber;
    }

    /**
     * 單號是否已存在
     *
     * @param string $number
     * @return boolean
     */
    public function numberExists(string $number): bool
    {
        return MiscStockRequest::where('request_no', $number)->count() > 0 ? true : false;
    }

    /**
     * 取得今天最後一筆單號
     *
     * @param string $numberHead
     * @return string|null
     */
    public function getTodayLastNumber(string $numberHead): ?string
    {
        $request = MiscStockRequest::where('request_no', 'like', "{$numberHead}%")
            ->latest('request_no')
            ->first();

        return isset($request) ? $request->request_no : null;
    }

    /**
     * 取得編輯頁的進貨退出單
     *
     * @param integer $id
     * @return Model
     */
    public function getStockRequestForEditPage(int $id): Model
    {
        $request = MiscStockRequest::with([
            'miscStockRequestDetails',
            'miscStockRequestDetails.productItem',
            'miscStockRequestDetails.productItem.product' => function ($query) {
                $query->select()->addSelect(DB::raw('get_latest_product_cost(id, TRUE) AS item_cost'));
            },
            'miscStockRequestDetails.productItem.product.supplier',
        ])->find($id);

        $request->loadMissing(['miscStockRequestDetails.productItem.warehouses' => function ($query) use ($request) {
            $query->where('warehouse.id', $request->warehouse_id);
        }]);

        return $request;
    }

    /**
     * 整理編輯頁的進貨退出單
     *
     * @param Model $miscStockRequest
     * @return array
     */
    public function formatStockRequestForEditPage(Model $miscStockRequest): array
    {
        $result = [
            'id' => $miscStockRequest->id,
            'request_no' => $miscStockRequest->request_no,
            'warehouse_id' => $miscStockRequest->warehouse_id,
            'tax' => $miscStockRequest->tax,
            'remark' => $miscStockRequest->remark,
            'ship_to_name' => $miscStockRequest->ship_to_name,
            'ship_to_mobile' => $miscStockRequest->ship_to_mobile,
            'ship_to_address' => $miscStockRequest->ship_to_address,
            'items' => null,
        ];

        if ($miscStockRequest->miscStockRequestDetails->isNotEmpty()) {
            foreach ($miscStockRequest->miscStockRequestDetails as $detail) {
                $tmpDetail = [
                    'id' => $detail->id,
                    'product_item_id' => $detail->product_item_id,
                    'product_no' => null,
                    'product_name' => null,
                    'item_no' => null,
                    'spec_1_value' => null,
                    'spec_2_value' => null,
                    'unit_price' => null,
                    'stock_qty' => null,
                    'expected_qty' => $detail->expected_qty,
                    'supplier_id' => null,
                    'supplier_name' => null,
                ];

                if (isset($detail->productItem)) {
                    $tmpDetail['item_no'] = $detail->productItem->item_no;
                    $tmpDetail['spec_1_value'] = $detail->productItem->spec_1_value;
                    $tmpDetail['spec_2_value'] = $detail->productItem->spec_2_value;

                    if (isset($detail->productItem->product)) {
                        $tmpDetail['product_no'] = $detail->productItem->product->product_no;
                        $tmpDetail['product_name'] = $detail->productItem->product->product_name;
                        $tmpDetail['unit_price'] = $detail->productItem->product->item_cost;

                        if (isset($detail->productItem->product->supplier)) {
                            $tmpDetail['supplier_id'] = $detail->productItem->product->supplier->id;
                            $tmpDetail['supplier_name'] = $detail->productItem->product->supplier->name;
                        }
                    }

                    $warehouse = $detail->productItem->warehouses->first();
                    if (isset($warehouse)) {
                        $tmpDetail['stock_qty'] = $warehouse->pivot->stock_qty;
                    }
                }

                $result['items'][] = $tmpDetail;
            }
        }

        return $result;
    }

    /**
     * 更新進貨退出單
     *
     * @param integer $id
     * @param array $data
     * @return boolean
     */
    public function updateStockRequest(int $id, array $data): bool
    {
        $user = auth()->user();
        $result = false;

        DB::beginTransaction();
        try {
            $request = MiscStockRequest::findOrFail($id);
            $requestData = [
                'warehouse_id' => $data['warehouse_id'],
                'expected_qty' => 0,
                'expected_amount' => 0,
                'expected_tax_amount' => 0,
                'total_sup_count' => 0,
                'ship_to_name' => $data['ship_to_name'] ?? null,
                'ship_to_mobile' => $data['ship_to_mobile'] ?? null,
                'ship_to_address' => $data['ship_to_address'] ?? null,
                'remark' => $data['remark'] ?? null,
                'updated_by' => $user->id,
            ];

            // 儲存類型
            if ($data['save_type'] == 'draft') {
                $requestData['request_status'] = 'DRAFTED';
            } else {
                $requestData['request_status'] = 'REVIEWING';
                $requestData['submitted_at'] = now();
            }

            $request->update($requestData);

            // 移除申請單、供應商的中間表
            $request->suppliers()->detach();
            // 移除申請單明細
            $request->miscStockRequestDetails()->delete();

            $requestQty = 0;
            $requestAmount = 0;
            $totalSupCount = 0;
            if (isset($data['items'])) {
                // 依供應商分群
                $supplierGroups = collect($data['items'])->groupBy('supplier_id');
                foreach ($supplierGroups as $supplierId => $items) {
                    $requestSupplierData = [
                        'misc_stock_request_id' => $id,
                        'supplier_id' => $supplierId,
                        'expected_qty' => 0,
                        'expected_amount' => 0,
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                    ];

                    // 儲存類型
                    if ($data['save_type'] == 'draft') {
                        $requestSupplierData['status_code'] = 'DRAFTED';
                    } else {
                        $requestSupplierData['status_code'] = 'REVIEWING';
                    }

                    // 建立申請單、供應商的中間表
                    $createdRequestSupplier = MiscStockRequestSupplier::create($requestSupplierData);

                    $requestSupplierQty = 0;
                    $requestSupplierAmount = 0;
                    foreach ($items as $item) {
                        $expectedSubtotal = round($item['unit_price'] * $item['expected_qty']);
                        // 建立申請單明細
                        $request->miscStockRequestDetails()->create([
                            'misc_stock_request_sup_id' => $createdRequestSupplier->id,
                            'product_item_id' => $item['product_item_id'],
                            'unit_price' => $item['unit_price'],
                            'expected_qty' => $item['expected_qty'],
                            'expected_subtotal' => $expectedSubtotal,
                            'onhand_qty' => $item['stock_qty'],
                            'created_by' => $user->id,
                            'updated_by' => $user->id,
                        ]);

                        $requestSupplierQty += $item['expected_qty'];
                        $requestSupplierAmount += $expectedSubtotal;
                    }

                    // 更新申請單、供應商的中間表數量、金額
                    $createdRequestSupplier->update([
                        'expected_qty' => $requestSupplierQty,
                        'expected_amount' => $requestSupplierAmount,
                    ]);

                    $requestQty += $requestSupplierQty;
                    $requestAmount += $requestSupplierAmount;
                    $totalSupCount++;
                }
            }

            $moneyAmount = MoneyAmount::makeByPrice($requestAmount, (int) $request->tax)->calculate('local', true);

            // 更新申請單數量、金額
            $request->update([
                'expected_qty' => $requestQty,
                'expected_amount' => $requestAmount,
                'expected_tax_amount' => $moneyAmount->getTaxPrice(),
                'total_sup_count' => $totalSupCount,
            ]);

            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
     * 刪除進貨退出單
     *
     * @param integer $id
     * @return boolean
     */
    public function deleteStockRequest(int $id): bool
    {
        $result = false;

        DB::beginTransaction();
        try {
            $request = MiscStockRequest::findOrFail($id);
            // 移除申請單、供應商的中間表
            $request->suppliers()->detach();
            // 移除申請單明細
            $request->miscStockRequestDetails()->delete();
            // 移除申請單
            $request->delete();

            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
     * 取得檢視頁的進貨退出單
     *
     * @param integer $id
     * @return Model
     */
    public function getStockRequestForShowPage(int $id): Model
    {
        $request = MiscStockRequest::with([
            'warehouse',
            'miscStockRequestDetails',
            'miscStockRequestDetails.productItem',
            'miscStockRequestDetails.productItem.product' => function ($query) {
                $query->select()->addSelect(DB::raw('get_latest_product_cost(id, TRUE) AS item_cost'));
            },
            'miscStockRequestDetails.productItem.product.supplier',
        ])->find($id);

        $request->loadMissing(['miscStockRequestDetails.productItem.warehouses' => function ($query) use ($request) {
            $query->where('warehouse.id', $request->warehouse_id);
        }]);

        return $request;
    }

    /**
     * 整理檢視頁的進貨退出單
     *
     * @param Model $miscStockRequest
     * @return array
     */
    public function formatStockRequestForShowPage(Model $miscStockRequest): array
    {
        $result = [
            'id' => $miscStockRequest->id,
            'request_no' => $miscStockRequest->request_no,
            'warehouse_name' => null,
            'expected_qty' => $miscStockRequest->expected_qty,
            'request_date' => $miscStockRequest->request_date,
            'submitted_at' => $miscStockRequest->submitted_at,
            'expected_date' => $miscStockRequest->expected_date,
            'tax' => config('uec.options.taxes')[$miscStockRequest->tax] ?? null,
            'expected_tax_amount' => null,
            'expected_amount' => null,
            'remark' => $miscStockRequest->remark,
            'ship_to_name' => $miscStockRequest->ship_to_name,
            'ship_to_mobile' => $miscStockRequest->ship_to_mobile,
            'ship_to_address' => $miscStockRequest->ship_to_address,
            'actual_date' => $miscStockRequest->actual_date,
            'actual_tax_amount' => round($miscStockRequest->actual_tax_amount),
            'actual_amount' => round($miscStockRequest->actual_amount),
            'items' => null,
        ];

        if (isset($miscStockRequest->warehouse)) {
            $result['warehouse_name'] = $miscStockRequest->warehouse->name;
        }

        $requestAmount = 0;
        if ($miscStockRequest->miscStockRequestDetails->isNotEmpty()) {
            foreach ($miscStockRequest->miscStockRequestDetails as $detail) {
                $tmpDetail = [
                    'product_no' => null,
                    'product_name' => null,
                    'item_no' => null,
                    'spec_1_value' => null,
                    'spec_2_value' => null,
                    'unit_price' => null,
                    'stock_qty' => null,
                    'expected_qty' => $detail->expected_qty,
                    'expected_subtotal' => 0,
                    'supplier_name' => null,
                    'actual_qty' => $detail->actual_qty,
                    'actual_subtotal' => round($detail->actual_subtotal),
                ];

                $unitPrice = null;
                $expectedSubtotal = null;
                $stockQty = null;
                if (isset($detail->productItem)) {
                    $tmpDetail['item_no'] = $detail->productItem->item_no;
                    $tmpDetail['spec_1_value'] = $detail->productItem->spec_1_value;
                    $tmpDetail['spec_2_value'] = $detail->productItem->spec_2_value;

                    if (isset($detail->productItem->product)) {
                        $tmpDetail['product_no'] = $detail->productItem->product->product_no;
                        $tmpDetail['product_name'] = $detail->productItem->product->product_name;
                        $unitPrice = $detail->productItem->product->item_cost * 100 / 100;
                        $expectedSubtotal = round($unitPrice * $tmpDetail['expected_qty']);
                        $requestAmount += $expectedSubtotal;

                        if (isset($detail->productItem->product->supplier)) {
                            $tmpDetail['supplier_name'] = $detail->productItem->product->supplier->name;
                        }
                    }

                    $warehouse = $detail->productItem->warehouses->first();
                    if (isset($warehouse)) {
                        $stockQty = $warehouse->pivot->stock_qty;
                    }
                }

                // 草稿需重新計算金額、庫存
                if ($miscStockRequest->request_status == 'DRAFTED') {
                    $tmpDetail['unit_price'] = $unitPrice;
                    $tmpDetail['stock_qty'] = $stockQty;
                    $tmpDetail['expected_subtotal'] = $expectedSubtotal;
                } else {
                    $tmpDetail['unit_price'] = $detail->unit_price * 100 / 100;
                    $tmpDetail['stock_qty'] = $detail->onhand_qty;
                    $tmpDetail['expected_subtotal'] = round($detail->expected_subtotal);
                }

                $result['items'][] = $tmpDetail;
            }
        }

        // 草稿需重新計算金額
        if ($miscStockRequest->request_status == 'DRAFTED') {
            $moneyAmount = MoneyAmount::makeByPrice($requestAmount, (int) $miscStockRequest->tax)->calculate('local', true);

            $result['expected_tax_amount'] = round($moneyAmount->getTaxPrice());
            $result['expected_amount'] = round($requestAmount);
        } else {
            $result['expected_tax_amount'] = round($miscStockRequest->expected_tax_amount);
            $result['expected_amount'] = round($miscStockRequest->expected_amount);
        }

        return $result;
    }

    /**
     * 取得供應商modal的列表
     *
     * @param integer $requestId
     * @return Collection
     */
    public function getSupplierModalList(int $requestId): Collection
    {
        $requestSuppliers = MiscStockRequestSupplier::with([
            'supplier',
            'miscStockRequestDetails',
            'miscStockRequestDetails.productItem',
            'miscStockRequestDetails.productItem.product' => function ($query) {
                $query->select()->addSelect(DB::raw('get_latest_product_cost(id, TRUE) AS item_cost'));
            },
        ])->where('misc_stock_request_id', $requestId);

        return $requestSuppliers->get();
    }

    /**
     * 整理供應商modal的列表
     *
     * @param Collection $requestSuppliers
     * @return array
     */
    public function formatSupplierModalList(Collection $requestSuppliers): array
    {
        $result = [];
        foreach ($requestSuppliers as $requestSupplier) {
            $tmpRequestSupplier = [
                'id' => $requestSupplier->supplier_id,
                'name' => null,
                'status_code' => config('uec.options.misc_stock_requests.status_codes.out')[$requestSupplier->status_code] ?? null,
                'expected_qty' => $requestSupplier->expected_qty,
                'expected_amount' => null,
            ];

            if (isset($requestSupplier->supplier)) {
                $tmpRequestSupplier['name'] = $requestSupplier->supplier->name;
            }

            // 草稿需重新計算金額
            if ($requestSupplier->status_code == 'DRAFTED') {
                $expectedAmount = 0;
                if ($requestSupplier->miscStockRequestDetails->isNotEmpty()) {
                    foreach ($requestSupplier->miscStockRequestDetails as $detail) {
                        if (isset($detail->productItem)) {
                            if (isset($detail->productItem->product)) {
                                $unitPrice = $detail->productItem->product->item_cost * 100 / 100;
                                $expectedAmount += round($unitPrice * $detail->expected_qty);
                            }
                        }
                    }
                }

                $tmpRequestSupplier['expected_amount'] = round($expectedAmount);
            } else {
                $tmpRequestSupplier['expected_amount'] = round($requestSupplier->expected_amount);
            }

            $result[] = $tmpRequestSupplier;
        }

        return $result;
    }

    /**
     * 取得供應商modal的明細
     *
     * @param integer $requestId
     * @param integer $supplierId
     * @return Model
     */
    public function getSupplierModalDetail(int $requestId, int $supplierId): Model
    {
        $request = MiscStockRequest::find($requestId);
        $requestSupplier = MiscStockRequestSupplier::with([
            'miscStockRequestDetails',
            'miscStockRequestDetails.productItem',
            'miscStockRequestDetails.productItem.product' => function ($query) {
                $query->select()->addSelect(DB::raw('get_latest_product_cost(id, TRUE) AS item_cost'));
            },
            'reviewedBy',
        ])->where('misc_stock_request_id', $requestId)
            ->where('supplier_id', $supplierId)
            ->first();

        $requestSupplier->loadMissing(['miscStockRequestDetails.productItem.warehouses' => function ($query) use ($request) {
            $query->where('warehouse.id', $request->warehouse_id);
        }]);

        return $requestSupplier;
    }

    /**
     * 整理供應商modal的明細
     *
     * @param Model $requestSupplier
     * @return array
     */
    public function formatSupplierModalDetail(Model $requestSupplier): array
    {
        $result = [
            'review_at' => $requestSupplier->review_at,
            'reviewer_name' => null,
            'review_result' => config('uec.options.review_results')[$requestSupplier->review_result] ?? null,
            'review_remark' => $requestSupplier->review_remark,
            'items' => null,
        ];

        if (isset($requestSupplier->reviewedBy)) {
            $result['reviewer_name'] = $requestSupplier->reviewedBy->user_name;
        }

        if ($requestSupplier->miscStockRequestDetails->isNotEmpty()) {
            foreach ($requestSupplier->miscStockRequestDetails as $detail) {
                $tmpDetail = [
                    'product_no' => null,
                    'product_name' => null,
                    'item_no' => null,
                    'spec_1_value' => null,
                    'spec_2_value' => null,
                    'unit_price' => null,
                    'stock_qty' => null,
                    'expected_qty' => $detail->expected_qty,
                    'expected_subtotal' => null,
                ];

                $unitPrice = null;
                $expectedSubtotal = null;
                $stockQty = null;
                if (isset($detail->productItem)) {
                    $tmpDetail['item_no'] = $detail->productItem->item_no;
                    $tmpDetail['spec_1_value'] = $detail->productItem->spec_1_value;
                    $tmpDetail['spec_2_value'] = $detail->productItem->spec_2_value;

                    if (isset($detail->productItem->product)) {
                        $tmpDetail['product_no'] = $detail->productItem->product->product_no;
                        $tmpDetail['product_name'] = $detail->productItem->product->product_name;
                        $unitPrice = $detail->productItem->product->item_cost * 100 / 100;
                        $expectedSubtotal = round($unitPrice * $tmpDetail['expected_qty']);
                    }

                    $warehouse = $detail->productItem->warehouses->first();
                    if (isset($warehouse)) {
                        $stockQty = $warehouse->pivot->stock_qty;
                    }
                }

                // 草稿需重新計算金額、庫存
                if ($requestSupplier->status_code == 'DRAFTED') {
                    $tmpDetail['unit_price'] = $unitPrice;
                    $tmpDetail['stock_qty'] = $stockQty;
                    $tmpDetail['expected_subtotal'] = $expectedSubtotal;
                } else {
                    $tmpDetail['unit_price'] = $detail->unit_price * 100 / 100;
                    $tmpDetail['stock_qty'] = $detail->onhand_qty;
                    $tmpDetail['expected_subtotal'] = round($detail->expected_subtotal);
                }

                $result['items'][] = $tmpDetail;
            }
        }

        return $result;
    }

    /**
     * 更新預出日
     *
     * @param integer $id
     * @param array $data
     * @return boolean
     */
    public function updateExpectedDate(int $id, array $data): bool
    {
        $user = auth()->user();
        $result = false;

        try {
            $request = MiscStockRequest::findOrFail($id);
            $requestData = [
                'expected_date' => $data['expected_date'] ?? null,
                'ship_to_name' => $data['ship_to_name'] ?? null,
                'ship_to_mobile' => $data['ship_to_mobile'] ?? null,
                'ship_to_address' => $data['ship_to_address'] ?? null,
                'updated_by' => $user->id,
            ];

            $request->update($requestData);
            $result = true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
     * 取得進貨退出單審核列表
     *
     * @param array $data
     * @return Collection
     */
    public function getStockReviewTableList(array $data = []): Collection
    {
        $miscStockRequests = MiscStockRequest::oldest('request_no')
            ->where('request_status', 'REVIEWING')
        // 申請單號
            ->when(isset($data['request_no']), function ($query) use ($data) {
                $query->where('request_no', $data['request_no']);
            })
        // 送審時間-開始日期
            ->when(isset($data['submitted_at_start']), function ($query) use ($data) {
                $query->whereDate('submitted_at', '>=', $data['submitted_at_start']);
            })
        // 送審時間-結束日期
            ->when(isset($data['submitted_at_end']), function ($query) use ($data) {
                $query->whereDate('submitted_at', '<=', $data['submitted_at_end']);
            })
            ->get();

        return $miscStockRequests;
    }

    /**
     * 整理進貨退出單審核列表
     *
     * @param Collection $miscStockRequests
     * @return array
     */
    public function formatStockReviewTableList(Collection $miscStockRequests): array
    {
        $result = [];

        foreach ($miscStockRequests as $request) {
            $tmpStockRequest = [
                'id' => $request->id,
                'request_no' => $request->request_no,
                'submitted_at' => $request->submitted_at,
                'total_sup_count' => $request->total_sup_count,
                'expected_amount' => round($request->expected_amount),
                'expected_qty' => $request->expected_qty,
            ];

            $result[] = $tmpStockRequest;
        }

        return $result;
    }

    /**
     * 取得審核頁的進貨退出單
     *
     * @param integer $id
     * @return Model
     */
    public function getStockRequestForReviewPage(int $id): Model
    {
        $request = MiscStockRequest::with([
            'warehouse',
            'suppliers' => function ($query) {
                $query->wherePivotNull('reviewer');
            },
        ])->find($id);

        return $request;
    }

    /**
     * 整理審核頁的進貨退出單
     *
     * @param Model $miscStockRequest
     * @return array
     */
    public function formatStockRequestForReviewPage(Model $miscStockRequest): array
    {
        $result = [
            'id' => $miscStockRequest->id,
            'request_no' => $miscStockRequest->request_no,
            'warehouse_name' => null,
            'expected_qty' => $miscStockRequest->expected_qty,
            'request_date' => $miscStockRequest->request_date,
            'submitted_at' => $miscStockRequest->submitted_at,
            'expected_date' => $miscStockRequest->expected_date,
            'tax' => config('uec.options.taxes')[$miscStockRequest->tax] ?? null,
            'expected_tax_amount' => round($miscStockRequest->expected_tax_amount),
            'expected_amount' => round($miscStockRequest->expected_amount),
            'remark' => $miscStockRequest->remark,
            'suppliers' => null,
        ];

        if (isset($miscStockRequest->warehouse)) {
            $result['warehouse_name'] = $miscStockRequest->warehouse->name;
        }

        if ($miscStockRequest->suppliers->isNotEmpty()) {
            foreach ($miscStockRequest->suppliers as $supplier) {
                $tmpSupplier = [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'expected_qty' => $supplier->pivot->expected_qty,
                    'expected_amount' => round($supplier->pivot->expected_amount),
                ];

                $result['suppliers'][] = $tmpSupplier;
            }
        }

        return $result;
    }

    /**
     * 取得審核modal的明細
     *
     * @param integer $requestId
     * @param integer $supplierId
     * @return Model
     */
    public function getReviewModalDetail(int $requestId, int $supplierId): Model
    {
        $requestSupplier = MiscStockRequestSupplier::with([
            'miscStockRequestDetails',
            'miscStockRequestDetails.productItem',
            'miscStockRequestDetails.productItem.product',
        ])->where('misc_stock_request_id', $requestId)
            ->where('supplier_id', $supplierId)
            ->first();

        return $requestSupplier;
    }

    /**
     * 整理審核modal的明細
     *
     * @param Model $requestSupplier
     * @return array
     */
    public function formatReviewModalDetail(Model $requestSupplier): array
    {
        $result = [
            'list' => null,
        ];

        if ($requestSupplier->miscStockRequestDetails->isNotEmpty()) {
            foreach ($requestSupplier->miscStockRequestDetails as $detail) {
                $tmpDetail = [
                    'product_no' => null,
                    'product_name' => null,
                    'item_no' => null,
                    'spec_1_value' => null,
                    'spec_2_value' => null,
                    'unit_price' => $detail->unit_price * 100 / 100,
                    'stock_qty' => $detail->onhand_qty,
                    'expected_qty' => $detail->expected_qty,
                    'expected_subtotal' => round($detail->expected_subtotal),
                ];

                if (isset($detail->productItem)) {
                    $tmpDetail['item_no'] = $detail->productItem->item_no;
                    $tmpDetail['spec_1_value'] = $detail->productItem->spec_1_value;
                    $tmpDetail['spec_2_value'] = $detail->productItem->spec_2_value;

                    if (isset($detail->productItem->product)) {
                        $tmpDetail['product_no'] = $detail->productItem->product->product_no;
                        $tmpDetail['product_name'] = $detail->productItem->product->product_name;
                    }
                }

                $result['list'][] = $tmpDetail;
            }
        }

        return $result;
    }

    /**
     * 審核申請單
     *
     * @param integer $id
     * @param array $data
     * @return array
     */
    public function reviewStockRequest(int $id, array $data): array
    {
        $user = auth()->user();
        $isSuccess = false;
        $remainingSupplierCount = null;

        DB::beginTransaction();
        try {
            // 取得申請單
            $request = MiscStockRequest::findOrFail($id);
            // 審核的供應商數量
            $reviewSupplierCount = count($data['supplier_ids']);

            $requestSupplierData = [
                'reviewer' => $user->id,
                'review_at' => now(),
                'review_result' => $data['review_result'],
                'review_remark' => $data['review_remark'],
                'updated_by' => $user->id,
            ];

            $requestData = [
                'updated_by' => $user->id,
            ];

            // 核准
            if ($data['review_result'] == 'APPROVE') {
                $requestSupplierData['status_code'] = 'APPROVED';
                $requestData['approved_sup_count'] = $request->approved_sup_count + $reviewSupplierCount;
            }
            // 駁回
            else {
                $requestSupplierData['status_code'] = 'REJECTED';
                $requestData['rejected_sup_count'] = $request->rejected_sup_count + $reviewSupplierCount;
            }

            // 更新申請單、供應商的中間表
            $request->suppliers()->updateExistingPivot($data['supplier_ids'], $requestSupplierData);

            // 剩餘未審核的供應商數量
            $remainingSupplierCount = $request->total_sup_count - ($request->approved_sup_count + $request->rejected_sup_count) - $reviewSupplierCount;
            // 審核完成
            if ($remainingSupplierCount <= 0) {
                $requestData['request_status'] = 'COMPLETED';
                $requestData['approved_at'] = now();
            }

            // 更新申請單
            $request->update($requestData);

            DB::commit();
            $isSuccess = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }

        return [
            'is_success' => $isSuccess,
            'remaining_supplier_count' => $remainingSupplierCount,
        ];
    }
}
