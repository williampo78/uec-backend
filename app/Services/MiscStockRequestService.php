<?php

namespace App\Services;

use App\Models\MiscStockRequest;
use App\Models\MiscStockRequestSupplier;
use App\Models\ProductItem;
use App\Services\MoneyAmountService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MiscStockRequestService
{
    private $moneyAmountService;

    public function __construct(MoneyAmountService $moneyAmountService)
    {
        $this->moneyAmountService = $moneyAmountService;
    }

    /**
     * 取得進貨退出單列表
     *
     * @param array $data
     * @return Collection
     */
    public function getStockRequestTableList(array $data = []): Collection
    {
        $miscStockRequests = MiscStockRequest::latest('request_no')
        // 申請單時間-開始日期
            ->when(isset($data['requestDateStart']), function ($query) use ($data) {
                $query->whereDate('request_date', '>=', $data['requestDateStart']);
            })
        // 申請單時間-結束日期
            ->when(isset($data['requestDateEnd']), function ($query) use ($data) {
                $query->whereDate('request_date', '<=', $data['requestDateEnd']);
            })
        // 申請單號
            ->when(isset($data['requestNo']), function ($query) use ($data) {
                $query->where('request_no', $data['requestNo']);
            })
        // 狀態
            ->when(isset($data['requestStatus']), function ($query) use ($data) {
                $query->where('request_status', $data['requestStatus']);
            })
        // 實際出入庫日期-開始日期
            ->when(isset($data['actualDateStart']), function ($query) use ($data) {
                $query->whereDate('actual_date', '>=', $data['actualDateStart']);
            })
        // 實際出入庫日期-結束日期
            ->when(isset($data['actualDateEnd']), function ($query) use ($data) {
                $query->whereDate('actual_date', '<=', $data['actualDateEnd']);
            })
        // 商品序號
            ->when(isset($data['productNo']), function ($query) use ($data) {
                $productNos = explode(',', $data['productNo']);
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
            ->when(isset($data['supplierId']), function ($query) use ($data) {
                $query->whereHas('suppliers', function (Builder $query) use ($data) {
                    $query->where('supplier.id', $data['supplierId']);
                });
            })
        // 限制筆數
            ->when(isset($data['limit']), function ($query) use ($data) {
                $query->limit($data['limit']);
            })
            ->get();

        return $miscStockRequests;
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
                'requestNo' => $request->request_no,
                'requestDate' => $request->request_date,
                'expectedDate' => $request->expected_date,
                'submittedAt' => $request->submitted_at,
                'totalSupCount' => $request->total_sup_count,
                'approvedSupCount' => $request->approved_sup_count,
                'rejectedSupCount' => $request->rejected_sup_count,
                'approvedAt' => $request->approved_at,
                'ediExportedAt' => $request->edi_exported_at,
                'actualDate' => $request->actual_date,
                'requestStatus' => $request->request_status,
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
                $query->where('warehouse.id', $data['warehouseId']);
            },
        ])
            ->where('agent_id', $user->agent_id)
        // 庫存必需大於0
            ->whereHas('warehouses', function (Builder $query) use ($data) {
                $query->where('warehouse.id', $data['warehouseId'])->where('warehouse_stock.stock_qty', '>', 0);
            })
        // 庫存類型:買斷
            ->whereHas('product', function (Builder $query) {
                $query->where('stock_type', 'A');
            })
        // 商品序號
            ->when(isset($data['productNo']), function ($query) use ($data) {
                $productNos = explode(',', $data['productNo']);
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
            ->when(isset($data['productName']), function ($query) use ($data) {
                $query->whereHas('product', function (Builder $query) use ($data) {
                    $query->where('product_name', 'like', "%{$data['productName']}%");
                });
            })
        // 供應商
            ->when(isset($data['supplierId']), function ($query) use ($data) {
                $query->whereHas('product.supplier', function (Builder $query) use ($data) {
                    $query->where('supplier_id', $data['supplierId']);
                });
            })
        // 限制筆數
            ->when(isset($data['limit']), function ($query) use ($data) {
                $query->limit($data['limit']);
            })
        // 排除已存在的品項
            ->when(!empty($data['excludeProductItemIds']), function ($query) use ($data) {
                $query->whereNotIn('id', $data['excludeProductItemIds']);
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
                'productNo' => null,
                'productName' => null,
                'itemNo' => $item->item_no,
                'spec1Value' => $item->spec_1_value,
                'spec2Value' => $item->spec_2_value,
                'supplierId' => null,
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
                    $tmpItem['supplierId'] = $item->product->supplier->id;
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
                'request_type' => 'TODO',
                'request_date' => now(),
                'warehouse_id' => $data['warehouseId'],
                'expected_qty' => 0,
                'tax' => $data['tax'],
                'expected_amount' => 0,
                'expected_tax_amount' => 0,
                'total_sup_count' => 0,
                'approved_sup_count' => 0,
                'rejected_sup_count' => 0,
                'ship_to_name' => $data['shipToName'] ?? null,
                'ship_to_mobile' => $data['shipToMobile'] ?? null,
                'ship_to_address' => $data['shipToAddress'] ?? null,
                'remark' => $data['remark'] ?? null,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ];

            // 儲存類型
            if ($data['saveType'] == 'draft') {
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
                $supplierGroups = collect($data['items'])->groupBy('supplierId');
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
                    if ($data['saveType'] == 'draft') {
                        $requestSupplierData['status_code'] = 'DRAFTED';
                    } else {
                        $requestSupplierData['status_code'] = 'REVIEWING';
                    }

                    // 建立申請單、供應商的中間表
                    $createdRequestSupplier = MiscStockRequestSupplier::create($requestSupplierData);

                    $requestSupplierQty = 0;
                    $requestSupplierAmount = 0;
                    foreach ($items as $item) {
                        $expectedSubtotal = round($item['unitPrice'] * $item['expectedQty']);
                        // 建立申請單明細
                        $createdRequest->miscStockRequestDetails()->create([
                            'misc_stock_request_sup_id' => $createdRequestSupplier->id,
                            'product_item_id' => $item['productItemId'],
                            'unit_price' => $item['unitPrice'],
                            'expected_qty' => $item['expectedQty'],
                            'expected_subtotal' => $expectedSubtotal,
                            'onhand_qty' => $item['stockQty'],
                            'created_by' => $user->id,
                            'updated_by' => $user->id,
                        ]);

                        $requestSupplierQty += $item['expectedQty'];
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

            $this->moneyAmountService
                ->setOriginalPrice($requestAmount)
                ->setTaxType((int) $data['tax'])
                ->calculateOriginalNontaxPrice()
                ->calculateOriginalTaxPrice();

            // 更新申請單數量、金額
            $createdRequest->update([
                'expected_qty' => $requestQty,
                'expected_amount' => $requestAmount,
                'expected_tax_amount' => $this->moneyAmountService->getOriginalTaxPrice(),
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
            'requestNo' => $miscStockRequest->request_no,
            'warehouseId' => $miscStockRequest->warehouse_id,
            'tax' => $miscStockRequest->tax,
            'remark' => $miscStockRequest->remark,
            'shipToName' => $miscStockRequest->ship_to_name,
            'shipToMobile' => $miscStockRequest->ship_to_mobile,
            'shipToAddress' => $miscStockRequest->ship_to_address,
            'items' => null,
        ];

        if ($miscStockRequest->miscStockRequestDetails->isNotEmpty()) {
            foreach ($miscStockRequest->miscStockRequestDetails as $detail) {
                $tmpDetail = [
                    'id' => $detail->id,
                    'productItemId' => $detail->product_item_id,
                    'productNo' => null,
                    'productName' => null,
                    'itemNo' => null,
                    'spec1Value' => null,
                    'spec2Value' => null,
                    'unitPrice' => null,
                    'stockQty' => null,
                    'expectedQty' => $detail->expected_qty,
                    'supplierId' => null,
                    'supplierName' => null,
                ];

                if (isset($detail->productItem)) {
                    $tmpDetail['itemNo'] = $detail->productItem->item_no;
                    $tmpDetail['spec1Value'] = $detail->productItem->spec_1_value;
                    $tmpDetail['spec2Value'] = $detail->productItem->spec_2_value;

                    if (isset($detail->productItem->product)) {
                        $tmpDetail['productNo'] = $detail->productItem->product->product_no;
                        $tmpDetail['productName'] = $detail->productItem->product->product_name;
                        $tmpDetail['unitPrice'] = $detail->productItem->product->item_cost;

                        if (isset($detail->productItem->product->supplier)) {
                            $tmpDetail['supplierId'] = $detail->productItem->product->supplier->id;
                            $tmpDetail['supplierName'] = $detail->productItem->product->supplier->name;
                        }
                    }

                    $warehouse = $detail->productItem->warehouses->first();
                    if (isset($warehouse)) {
                        $tmpDetail['stockQty'] = $warehouse->pivot->stock_qty;
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
                'warehouse_id' => $data['warehouseId'],
                'expected_qty' => 0,
                'expected_amount' => 0,
                'expected_tax_amount' => 0,
                'total_sup_count' => 0,
                'ship_to_name' => $data['shipToName'] ?? null,
                'ship_to_mobile' => $data['shipToMobile'] ?? null,
                'ship_to_address' => $data['shipToAddress'] ?? null,
                'remark' => $data['remark'] ?? null,
                'updated_by' => $user->id,
            ];

            // 儲存類型
            if ($data['saveType'] == 'draft') {
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
                $supplierGroups = collect($data['items'])->groupBy('supplierId');
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
                    if ($data['saveType'] == 'draft') {
                        $requestSupplierData['status_code'] = 'DRAFTED';
                    } else {
                        $requestSupplierData['status_code'] = 'REVIEWING';
                    }

                    // 建立申請單、供應商的中間表
                    $createdRequestSupplier = MiscStockRequestSupplier::create($requestSupplierData);

                    $requestSupplierQty = 0;
                    $requestSupplierAmount = 0;
                    foreach ($items as $item) {
                        $expectedSubtotal = round($item['unitPrice'] * $item['expectedQty']);
                        // 建立申請單明細
                        $request->miscStockRequestDetails()->create([
                            'misc_stock_request_sup_id' => $createdRequestSupplier->id,
                            'product_item_id' => $item['productItemId'],
                            'unit_price' => $item['unitPrice'],
                            'expected_qty' => $item['expectedQty'],
                            'expected_subtotal' => $expectedSubtotal,
                            'onhand_qty' => $item['stockQty'],
                            'created_by' => $user->id,
                            'updated_by' => $user->id,
                        ]);

                        $requestSupplierQty += $item['expectedQty'];
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

            $this->moneyAmountService
                ->setOriginalPrice($requestAmount)
                ->setTaxType((int) $request->tax)
                ->calculateOriginalNontaxPrice()
                ->calculateOriginalTaxPrice();

            // 更新申請單數量、金額
            $request->update([
                'expected_qty' => $requestQty,
                'expected_amount' => $requestAmount,
                'expected_tax_amount' => $this->moneyAmountService->getOriginalTaxPrice(),
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
            'requestNo' => $miscStockRequest->request_no,
            'warehouseName' => null,
            'expectedQty' => $miscStockRequest->expected_qty,
            'requestDate' => $miscStockRequest->request_date,
            'submittedAt' => $miscStockRequest->submitted_at,
            'expectedDate' => $miscStockRequest->expected_date,
            'tax' => config('uec.options.taxes')[$miscStockRequest->tax] ?? null,
            'expectedTaxAmount' => null,
            'expectedAmount' => null,
            'remark' => $miscStockRequest->remark,
            'shipToName' => $miscStockRequest->ship_to_name,
            'shipToMobile' => $miscStockRequest->ship_to_mobile,
            'shipToAddress' => $miscStockRequest->ship_to_address,
            'actualDate' => $miscStockRequest->actual_date,
            'actualTaxAmount' => round($miscStockRequest->actual_tax_amount),
            'actualAmount' => round($miscStockRequest->actual_amount),
            'items' => null,
        ];

        if (isset($miscStockRequest->warehouse)) {
            $result['warehouseName'] = $miscStockRequest->warehouse->name;
        }

        $requestAmount = 0;
        if ($miscStockRequest->miscStockRequestDetails->isNotEmpty()) {
            foreach ($miscStockRequest->miscStockRequestDetails as $detail) {
                $tmpDetail = [
                    'productNo' => null,
                    'productName' => null,
                    'itemNo' => null,
                    'spec1Value' => null,
                    'spec2Value' => null,
                    'unitPrice' => null,
                    'stockQty' => null,
                    'expectedQty' => $detail->expected_qty,
                    'expectedSubtotal' => 0,
                    'supplierName' => null,
                    'actualQty' => $detail->actual_qty,
                    'actualSubtotal' => round($detail->actual_subtotal),
                ];

                $unitPrice = null;
                $expectedSubtotal = null;
                $stockQty = null;
                if (isset($detail->productItem)) {
                    $tmpDetail['itemNo'] = $detail->productItem->item_no;
                    $tmpDetail['spec1Value'] = $detail->productItem->spec_1_value;
                    $tmpDetail['spec2Value'] = $detail->productItem->spec_2_value;

                    if (isset($detail->productItem->product)) {
                        $tmpDetail['productNo'] = $detail->productItem->product->product_no;
                        $tmpDetail['productName'] = $detail->productItem->product->product_name;
                        $unitPrice = $detail->productItem->product->item_cost * 100 / 100;
                        $expectedSubtotal = round($unitPrice * $tmpDetail['expectedQty']);
                        $requestAmount += $expectedSubtotal;

                        if (isset($detail->productItem->product->supplier)) {
                            $tmpDetail['supplierName'] = $detail->productItem->product->supplier->name;
                        }
                    }

                    $warehouse = $detail->productItem->warehouses->first();
                    if (isset($warehouse)) {
                        $stockQty = $warehouse->pivot->stock_qty;
                    }
                }

                // 草稿需重新計算金額、庫存
                if ($miscStockRequest->request_status == 'DRAFTED') {
                    $tmpDetail['unitPrice'] = $unitPrice;
                    $tmpDetail['stockQty'] = $stockQty;
                    $tmpDetail['expectedSubtotal'] = $expectedSubtotal;
                } else {
                    $tmpDetail['unitPrice'] = $detail->unit_price * 100 / 100;
                    $tmpDetail['stockQty'] = $detail->onhand_qty;
                    $tmpDetail['expectedSubtotal'] = round($detail->expected_subtotal);
                }

                $result['items'][] = $tmpDetail;
            }
        }

        // 草稿需重新計算金額
        if ($miscStockRequest->request_status == 'DRAFTED') {
            $this->moneyAmountService
                ->setOriginalPrice($requestAmount)
                ->setTaxType((int) $miscStockRequest->tax)
                ->calculateOriginalNontaxPrice()
                ->calculateOriginalTaxPrice();

            $result['expectedTaxAmount'] = round($this->moneyAmountService->getOriginalTaxPrice());
            $result['expectedAmount'] = round($requestAmount);
        } else {
            $result['expectedTaxAmount'] = round($miscStockRequest->expected_tax_amount);
            $result['expectedAmount'] = round($miscStockRequest->expected_amount);
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
                'statusCode' => config('uec.options.misc_stock_requests.status_codes.out')[$requestSupplier->status_code] ?? null,
                'expectedQty' => $requestSupplier->expected_qty,
                'expectedAmount' => null,
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

                $tmpRequestSupplier['expectedAmount'] = round($expectedAmount);
            } else {
                $tmpRequestSupplier['expectedAmount'] = round($requestSupplier->expected_amount);
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
            'reviewAt' => $requestSupplier->review_at,
            'reviewerName' => null,
            'reviewResult' => config('uec.options.review_results')[$requestSupplier->review_result] ?? null,
            'reviewRemark' => $requestSupplier->review_remark,
            'items' => null,
        ];

        if (isset($requestSupplier->reviewedBy)) {
            $result['reviewerName'] = $requestSupplier->reviewedBy->user_name;
        }

        if ($requestSupplier->miscStockRequestDetails->isNotEmpty()) {
            foreach ($requestSupplier->miscStockRequestDetails as $detail) {
                $tmpDetail = [
                    'productNo' => null,
                    'productName' => null,
                    'itemNo' => null,
                    'spec1Value' => null,
                    'spec2Value' => null,
                    'unitPrice' => null,
                    'stockQty' => null,
                    'expectedQty' => $detail->expected_qty,
                    'expectedSubtotal' => null,
                ];

                $unitPrice = null;
                $expectedSubtotal = null;
                $stockQty = null;
                if (isset($detail->productItem)) {
                    $tmpDetail['itemNo'] = $detail->productItem->item_no;
                    $tmpDetail['spec1Value'] = $detail->productItem->spec_1_value;
                    $tmpDetail['spec2Value'] = $detail->productItem->spec_2_value;

                    if (isset($detail->productItem->product)) {
                        $tmpDetail['productNo'] = $detail->productItem->product->product_no;
                        $tmpDetail['productName'] = $detail->productItem->product->product_name;
                        $unitPrice = $detail->productItem->product->item_cost * 100 / 100;
                        $expectedSubtotal = round($unitPrice * $tmpDetail['expectedQty']);
                    }

                    $warehouse = $detail->productItem->warehouses->first();
                    if (isset($warehouse)) {
                        $stockQty = $warehouse->pivot->stock_qty;
                    }
                }

                // 草稿需重新計算金額、庫存
                if ($requestSupplier->status_code == 'DRAFTED') {
                    $tmpDetail['unitPrice'] = $unitPrice;
                    $tmpDetail['stockQty'] = $stockQty;
                    $tmpDetail['expectedSubtotal'] = $expectedSubtotal;
                } else {
                    $tmpDetail['unitPrice'] = $detail->unit_price * 100 / 100;
                    $tmpDetail['stockQty'] = $detail->onhand_qty;
                    $tmpDetail['expectedSubtotal'] = round($detail->expected_subtotal);
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
                'expected_date' => $data['expectedDate'] ?? null,
                'ship_to_name' => $data['shipToName'] ?? null,
                'ship_to_mobile' => $data['shipToMobile'] ?? null,
                'ship_to_address' => $data['shipToAddress'] ?? null,
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
            ->when(isset($data['requestNo']), function ($query) use ($data) {
                $query->where('request_no', $data['requestNo']);
            })
        // 送審時間-開始日期
            ->when(isset($data['submittedAtStart']), function ($query) use ($data) {
                $query->whereDate('submitted_at', '>=', $data['submittedAtStart']);
            })
        // 送審時間-結束日期
            ->when(isset($data['submittedAtEnd']), function ($query) use ($data) {
                $query->whereDate('submitted_at', '<=', $data['submittedAtEnd']);
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
                'requestNo' => $request->request_no,
                'submittedAt' => $request->submitted_at,
                'totalSupCount' => $request->total_sup_count,
                'expectedAmount' => round($request->expected_amount),
                'expectedQty' => $request->expected_qty,
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
            'requestNo' => $miscStockRequest->request_no,
            'warehouseName' => null,
            'expectedQty' => $miscStockRequest->expected_qty,
            'requestDate' => $miscStockRequest->request_date,
            'submittedAt' => $miscStockRequest->submitted_at,
            'expectedDate' => $miscStockRequest->expected_date,
            'tax' => config('uec.options.taxes')[$miscStockRequest->tax] ?? null,
            'expectedTaxAmount' => round($miscStockRequest->expected_tax_amount),
            'expectedAmount' => round($miscStockRequest->expected_amount),
            'remark' => $miscStockRequest->remark,
            'suppliers' => null,
        ];

        if (isset($miscStockRequest->warehouse)) {
            $result['warehouseName'] = $miscStockRequest->warehouse->name;
        }

        if ($miscStockRequest->suppliers->isNotEmpty()) {
            foreach ($miscStockRequest->suppliers as $supplier) {
                $tmpSupplier = [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'expectedQty' => $supplier->pivot->expected_qty,
                    'expectedAmount' => round($supplier->pivot->expected_amount),
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
                    'productNo' => null,
                    'productName' => null,
                    'itemNo' => null,
                    'spec1Value' => null,
                    'spec2Value' => null,
                    'unitPrice' => $detail->unit_price * 100 / 100,
                    'stockQty' => $detail->onhand_qty,
                    'expectedQty' => $detail->expected_qty,
                    'expectedSubtotal' => round($detail->expected_subtotal),
                ];

                if (isset($detail->productItem)) {
                    $tmpDetail['itemNo'] = $detail->productItem->item_no;
                    $tmpDetail['spec1Value'] = $detail->productItem->spec_1_value;
                    $tmpDetail['spec2Value'] = $detail->productItem->spec_2_value;

                    if (isset($detail->productItem->product)) {
                        $tmpDetail['productNo'] = $detail->productItem->product->product_no;
                        $tmpDetail['productName'] = $detail->productItem->product->product_name;
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
            $reviewSupplierCount = count($data['supplierIds']);

            $requestSupplierData = [
                'reviewer' => $user->id,
                'review_at' => now(),
                'review_result' => $data['reviewResult'],
                'review_remark' => $data['reviewRemark'],
                'updated_by' => $user->id,
            ];

            $requestData = [
                'updated_by' => $user->id,
            ];

            // 核准
            if ($data['reviewResult'] == 'APPROVE') {
                $requestSupplierData['status_code'] = 'APPROVED';
                $requestData['approved_sup_count'] = $request->approved_sup_count + $reviewSupplierCount;
            }
            // 駁回
            else {
                $requestSupplierData['status_code'] = 'REJECTED';
                $requestData['rejected_sup_count'] = $request->rejected_sup_count + $reviewSupplierCount;
            }

            // 更新申請單、供應商的中間表
            $request->suppliers()->updateExistingPivot($data['supplierIds'], $requestSupplierData);

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
            'isSuccess' => $isSuccess,
            'remainingSupplierCount' => $remainingSupplierCount,
        ];
    }
}
