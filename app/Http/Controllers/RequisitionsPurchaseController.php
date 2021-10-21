<?php

namespace App\Http\Controllers;

use App\Services\ItemService;
use App\Services\RequisitionsPurchaseService;
use App\Services\SupplierService;
use App\Services\UniversalService;
use App\Services\WarehouseService;
use Carbon\Carbon;
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
        $now = Carbon::now()->subDays()->toArray();
        $params['active'] = 0;
        $data = $this->requisitionsPurchaseService->getRequisitionsPurchase($params);

        return view('Backend.RequisitionsPurchase.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $result = [];
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
        $result = $this->requisitionsPurchaseService->createRequisitionsPurchase($request->input());  //創建請購單
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
        // $data = Category::find($id);
        // $primary_category_list = $this->categoryService->getPrimaryCategoryForList();

        return view('Backend.PrimaryCategory.upd', compact('data', 'primary_category_list'));
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
}
