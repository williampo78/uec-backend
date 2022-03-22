<?php

namespace App\Http\Controllers;

use App\Services\BrandsService;
use App\Services\ProductsService;
use App\Services\QuotationService;
use App\Services\RequisitionsPurchaseService;
use App\Services\SupplierService;
use App\Services\UniversalService;
use App\Services\WarehouseService;
use Illuminate\Http\Request;

class RequisitionsPurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $requisitionsPurchaseService;
    private $warehouseService;
    private $supplierService;
    private $universalService;
    private $quotationService;
    private $productsService;

    public function __construct(
        RequisitionsPurchaseService $requisitionsPurchaseService,
        WarehouseService $warehouseService,
        SupplierService $supplierService,
        ProductsService $productsService,
        UniversalService $universalService,
        QuotationService $quotationService,
        BrandsService $brandsService
    ) {
        $this->requisitionsPurchaseService = $requisitionsPurchaseService; //請購單
        $this->warehouseService = $warehouseService; // 倉庫
        $this->supplierService = $supplierService; //供應商
        $this->universalService = $universalService; // 共用服務
        $this->quotationService = $quotationService; //報價單服務
        $this->productsService = $productsService;
        $this->brandsService = $brandsService;
    }

    public function index(Request $request)
    {
        $params['active'] = 0;
        $input = $request->input();
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        if (count($input) !== 0) {
            $result['requisitionsPurchase'] = $this->requisitionsPurchaseService->getRequisitionsPurchase($input);
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
        $result = $this->requisitionsPurchaseService->createRequisitionsPurchase($request->input()); //創建請購單
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
        // dd($requisitionsPurchaseDetail) ;
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

        $result['itemOptions'] = $this->productsService->getItemsAndProduct([
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
        // dump($request->input());
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
                $products_item = $this->productsService->getItemsAndProduct(['supplier_id' => $rs['supplier_id']])->transform(function ($obj, $key) {
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
