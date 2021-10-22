<?php

namespace App\Http\Controllers;

use App\Services\ItemService;
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

    public function __construct(
        RequisitionsPurchaseService $requisitionsPurchaseService,
        WarehouseService $warehouseService,
        SupplierService $supplierService,
        ItemService $itemService,
        UniversalService $universalService
    ) {
        $this->requisitionsPurchaseService = $requisitionsPurchaseService; //請購單
        $this->warehouseService = $warehouseService; // 倉庫
        $this->supplierService = $supplierService; //供應商
        $this->itemService = $itemService; //品項
        $this->universalService = $universalService; // 共用服務
    }

    public function index()
    {
        $params['active'] = 0;
        $result['requisitionsPurchase'] = $this->requisitionsPurchaseService->getRequisitionsPurchase($params)->get();
        // dd($data);
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
        return view('backend.success', compact('route_name', 'act'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

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

        // dd($result) ;
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
        dump($request->input());
        exit;
        // $data = $request->except('_token' , '_method');
        // $data['updated_by'] = Auth::user()->id;
        // $data['updated_at'] = Carbon::now();

        // Category::where('id' ,$id)->update($data);
        // $route_name = 'category';
        // $act = 'upd';
        return view('backend.success', compact('route_name', 'act'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

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
}
