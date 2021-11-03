<?php

namespace App\Http\Controllers;

use App\Services\ItemService;
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
    private $itemService;
    private $universalService;
    private $quotationService;

    public function __construct(
        RequisitionsPurchaseService $requisitionsPurchaseService,
        WarehouseService $warehouseService,
        SupplierService $supplierService,
        ItemService $itemService,
        UniversalService $universalService,
        QuotationService $quotationService
    ) {
        $this->requisitionsPurchaseService = $requisitionsPurchaseService; //請購單
        $this->warehouseService = $warehouseService; // 倉庫
        $this->supplierService = $supplierService; //供應商
        $this->itemService = $itemService; //品項
        $this->universalService = $universalService; // 共用服務
        $this->quotationService = $quotationService; //報價單服務
    }

    public function index(Request $request)
    {
        $params['active'] = 0;
        $input = $request->input();
        $result['supplier'] = $this->supplierService->getSupplier(); //供應商
        $result['requisitionsPurchase'] = $this->requisitionsPurchaseService->getRequisitionsPurchase($input);
        return view('Backend.RequisitionsPurchase.list', $result);
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
            'tax' => 0,
        ];
        $result['warehouse'] = $this->warehouseService->getWarehouseList(); //取得倉庫
        $result['supplier'] = $this->supplierService->getSupplier(); //供應商
        $result['item'] = $this->itemService->getItem()->get(); //品項
        $result['taxList'] = $this->universalService->getTaxList(); //取德稅別列表
        //select 2 套件需要text 辨別 option name
        foreach ($result['item'] as $key => $val) {
            $result['item'][$key]->text = $val->name;
        }

        return view('Backend.RequisitionsPurchase.input', $result);
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
        $act = 'add';
        $route_name = 'requisitions_purchase';
        return view('Backend.success', compact('route_name', 'act'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $responseType = $request->input('responseType');

        $requisitionsPurchase = $this->requisitionsPurchaseService->getAjaxRequisitionsPurchase($id); //請購單
        $requisitionsPurchaseDetail = $this->requisitionsPurchaseService->getAjaxRequisitionsPurchaseDetail($id); //請購單內的品項
        $getRequisitionPurchaseReviewLog = $this->requisitionsPurchaseService->getRequisitionPurchaseReviewLog($id); //簽核紀錄

        if ($responseType = 'json') {
            return response()->json([
                'requisitionsPurchase' => json_encode($requisitionsPurchase),
                'requisitionsPurchaseDetail' => json_encode($requisitionsPurchaseDetail),
                'getRequisitionPurchaseReviewLog' => json_encode($getRequisitionPurchaseReviewLog),
            ]);
        }

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
        $result['requisitionsPurchaseDetail'] = $this->requisitionsPurchaseService->getAjaxRequisitionsPurchaseDetail($id);
        $result['warehouse'] = $this->warehouseService->getWarehouseList(); //取得倉庫
        $result['supplier'] = $this->supplierService->getSupplier(); //供應商
        $result['item'] = $this->itemService->getItem()->get(); //品項
        foreach ($result['item'] as $key => $val) {
            $result['item'][$key]->text = $val->name;
        }
        $result['taxList'] = $this->universalService->getTaxList(); //取德稅別列表
        return view('Backend.RequisitionsPurchase.input', $result);
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
        $act = 'upd';
        $route_name = 'requisitions_purchase';
        return view('Backend.success', compact('route_name', 'act'));
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

        if ($rs['get_type'] === 'requisitions_purchase') {
            $data = $this->requisitionsPurchaseService->getAjaxRequisitionsPurchase($rs['id']);
            echo "OK@@" . json_encode($data);
        } elseif ($rs['get_type'] === 'requisitions_purchase_detail') {
            $data = $this->requisitionsPurchaseService->getAjaxRequisitionsPurchaseDetail($rs['id']);
            echo "OK@@" . json_encode($data);
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
        $getItemLastPrice = $this->quotationService->getItemLastPrice($in)->toArray();
        $original_unit_price = isset($getItemLastPrice[0]['original_unit_price']) ? $getItemLastPrice[0]['original_unit_price'] : null  ;
        return response()->json([
            'original_unit_price' => $original_unit_price,
        ]);
    }

}
