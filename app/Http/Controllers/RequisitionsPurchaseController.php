<?php

namespace App\Http\Controllers;

use App\Services\BrandsService;
use App\Services\ProductService;
use App\Services\QuotationService;
use App\Services\RequisitionsPurchaseService;
use App\Services\SupplierService;
use App\Services\WarehouseService;
use Illuminate\Http\Request;

class RequisitionsPurchaseController extends Controller
{
    private $requisitionsPurchaseService;
    private $warehouseService;
    private $supplierService;
    private $quotationService;
    private $productService;

    public function __construct(
        RequisitionsPurchaseService $requisitionsPurchaseService,
        WarehouseService $warehouseService,
        SupplierService $supplierService,
        ProductService $productService,
        QuotationService $quotationService,
        BrandsService $brandsService
    ) {
        $this->requisitionsPurchaseService = $requisitionsPurchaseService; //請購單
        $this->warehouseService = $warehouseService; // 倉庫
        $this->supplierService = $supplierService; //供應商
        $this->quotationService = $quotationService; //報價單服務
        $this->productService = $productService;
        $this->brandsService = $brandsService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $params['active'] = 0;
        $payload = $request->only([
            'supplier_id',
            'company_number',
            'status',
            'select_start_date',
            'select_end_date',
            'doc_number',
        ]);
        // 供應商
        $result['supplier'] = $this->supplierService->getSuppliers();

        if (!empty($payload)) {
            $result['requisitionsPurchase'] = $this->requisitionsPurchaseService->getRequisitionsPurchase($payload);
        }

        return view('backend.requisitions_purchase.list', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $result = [];
        $result['requisitionsPurchaseDefault'] = [
            'total_tax_price' => 0,
            'total_price' => 0,
            'original_total_tax_price' => 0,
            'original_total_price' => 0,
            'tax' => '',
        ];
        $result['warehouse'] = $this->warehouseService->getWarehouseList(); //取得倉庫
        $result['supplier'] = $this->supplierService->getSuppliers();
        $result['taxList'] = config('uec.tax_option'); //取得稅別列表

        return view('backend.requisitions_purchase.input', $result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //創建請購單
        $result = $this->requisitionsPurchaseService->createRequisitionsPurchase($request->only([
            'id',
            'supplier_id',
            'trade_date',
            'number',
            'warehouse_id',
            'currency_code',
            'currency_price',
            'original_total_tax_price',
            'original_total_price',
            'tax',
            'total_tax_price',
            'total_price',
            'remark',
            'status',
            'requisitions_purchase_detail',
        ]));
        $result['route_name'] = 'requisitions_purchase';
        $result['act'] = 'add';
        if ($result['status']) {
            return view('backend.success', $result);
        } else {
            return view('backend.error', $result);
        };

        $act = 'add';
        $route_name = 'requisitions_purchase';
        return view('backend.success', compact('route_name', 'act'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $requisitionsPurchase = $this->requisitionsPurchaseService->getAjaxRequisitionsPurchase($id); //請購單
        $brands = $this->brandsService->getBrands()->keyBy('id')->toArray();
        $requisitionsPurchaseDetail = $this->requisitionsPurchaseService->getRequisitionPurchaseDetail($id)->transform(function ($obj, $key) use ($brands) {

            $brandsName = isset($brands[$obj->brand_id]['brand_name']) ? $brands[$obj->brand_id]['brand_name'] : '品牌已被刪除';

            $obj->combination_name = $obj->product_items_no . '-' . $brandsName . '-' . $obj->product_name;

            if ($obj->spec_1_value !== '') {
                $obj->combination_name .= '-' . $obj->spec_1_value;
            }

            if ($obj->spec_2_value !== '') {
                $obj->combination_name .= '-' . $obj->spec_2_value;
            }
            if ($obj->product_name == '') {
                $obj->combination_name = false;
            }
            $obj->brands_name = $brandsName; //不做join key find val

            return $obj;
        });

        $getRequisitionPurchaseReviewLog = $this->requisitionsPurchaseService->getRequisitionPurchaseReviewLog($id); //簽核紀錄

        return response()->json([
            'requisitionsPurchase' => json_encode($requisitionsPurchase),
            'requisitionsPurchaseDetail' => json_encode($requisitionsPurchaseDetail),
            'getRequisitionPurchaseReviewLog' => json_encode($getRequisitionPurchaseReviewLog),
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result['requisitionsPurchase'] = $this->requisitionsPurchaseService->getRequisitionPurchaseById($id);
        $result['requisitionsPurchaseDetail'] = $this->requisitionsPurchaseService->getRequisitionPurchaseDetail($id);
        $result['warehouse'] = $this->warehouseService->getWarehouseList(); //取得倉庫
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商

        $result['itemOptions'] = $this->productService->getItemsAndProduct([
            'supplier_id' => $result['requisitionsPurchase']['supplier_id'],
        ])->transform(function ($obj, $key) {
            $obj->text = $obj->item_no . '-' . $obj->brand_name . '-' . $obj->product_name . '-' . $obj->spec_1_value . '-' . $obj->spec_2_value;
            return $obj;
        });

        $result['taxList'] = config('uec.tax_option'); //取德稅別列表
        return view('backend.requisitions_purchase.input', $result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->only([
            'id',
            'supplier_id',
            'old_supplier_id',
            'trade_date',
            'number',
            'warehouse_id',
            'currency_code',
            'currency_price',
            'original_total_tax_price',
            'original_total_price',
            'tax',
            'total_tax_price',
            'total_price',
            'remark',
            'status',
            'requisitions_purchase_detail',
            'item_price',
            'item_qty',
        ]);
        $result = $this->requisitionsPurchaseService->updateRequisitionsPurchase($request->input()); //創建請購單
        $result['route_name'] = 'requisitions_purchase';
        $result['act'] = 'upd';
        if ($result['status']) {
            return view('backend.success', $result);
        } else {
            return view('backend.error', $result);
        };
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $type = $request->input('type');
        if ($type == 'Detail') {
            $this->requisitionsPurchaseService->delRequisitionsPurchaseDetail($id);
        } else {
            $this->requisitionsPurchaseService->delrequisitionsPurchase($id);
        }

        return response()->json([
            'status' => true,
            'find' => $id,
            'type' => $type,
        ]);
    }

    public function ajax(Request $request)
    {
        $rs = $request->all();

        switch ($rs['get_type']) {
            case 'requisitions_purchase':
                $data = $this->requisitionsPurchaseService->getAjaxRequisitionsPurchase($rs['id']);
                echo "OK@@" . json_encode($data);
                break;
            case 'requisitions_purchase_detail':
                $data = $this->requisitionsPurchaseService->getRequisitionPurchaseDetail($rs['id']);
                echo "OK@@" . json_encode($data);
                break;
            case 'getItemOption':
                $products_item = $this->productService->getItemsAndProduct(['supplier_id' => $rs['supplier_id']])->transform(function ($obj, $key) {
                    $obj->text = $obj->item_no . '-' . $obj->brand_name . '-' . $obj->product_name . '-' . $obj->spec_1_value . '-' . $obj->spec_2_value;
                    return $obj;
                });
                return response()->json(['rs' => $rs, 'products_item' => $products_item]);
                break;
            default:
                # code...
                break;
        }
    }

    public function ajaxDelPurchaseDetail(Request $request)
    {
        $data = json_encode($request->input());

        return response()->json([
            'data' => $data,
        ]);

    }

    //用請購單ID 帶出 請購單內的品項以及 簽核紀錄
    public function getItemLastPrice(Request $request)
    {
        $in = $request->input();
        $getItemLastPrice = $this->quotationService->getItemLastPrice($in)->first();
        if ($getItemLastPrice == null) {
            return response()->json([
                'item_price' => null,
            ]);
        } else {
            $tem_price = $getItemLastPrice->original_unit_nontax_price + $getItemLastPrice->original_unit_tax_price;
            return response()->json([
                'item_price' => $tem_price,
            ]);
        };

    }
}
