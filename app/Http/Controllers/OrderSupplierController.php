<?php

namespace App\Http\Controllers;

use App\Models\OrderSupplier;
use App\Models\Quotation;
use App\Services\ItemService;
use App\Services\OrderSupplierService;
use App\Services\QuotationService;
use App\Services\RequisitionsPurchaseService;
use App\Services\SupplierService;
use App\Services\UniversalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class OrderSupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $universalService;
    private $orderSupplierService;
    private $requisitionsPurchaseService;

    public function __construct(UniversalService $universalService, OrderSupplierService $orderSupplierService, RequisitionsPurchaseService $requisitionsPurchaseService)
    {
        $this->universalService = $universalService;
        $this->orderSupplierService = $orderSupplierService;
        $this->requisitionsPurchaseService = $requisitionsPurchaseService;
    }
    public function index(Request $request)
    {
        $getData = $request->all();

        $data = [];
        $supplier = new SupplierService();
        $data['supplier'] = $this->universalService->idtokey($supplier->getSupplier());
        $data['order_supplier'] = ($getData)? $this->orderSupplierService->getOrderSupplier($getData) : [];
        $data['status_code'] = $this->universalService->getStatusCode();
        if (!isset($getData['select_start_date']) || !isset($getData['select_end_date'])){
            $getData['select_start_date'] = Carbon::now()->subMonth()->toDateString();
            $getData['select_end_date'] = Carbon::now()->toDateString();
        }

        $data['getData'] = $getData;
        $data['user_id'] = Auth::user()->id;

        return view('Backend.OrderSupplier.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $route_name = 'quotation';
        $act = 'add';
        $data = $request->except('_token');
        if(isset($data['status_code'])){
            $act = $data['status_code'];
        }

        $this->quotationService->addQuotation($data);

        return view('backend.success' , compact('route_name','act'));
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
        $supplier = new SupplierService();
        $data['supplier'] = $supplier->getSupplier();
        $data['requisitions_purchase'] = $this->requisitionsPurchaseService->getRequisitionsPurchaseList();
        $data['order_supplier'] = $this->orderSupplierService->getOrderSupplierById($id);
        $data['tax'] = $this->universalService->getTaxList();

        $data['act'] = 'upd';
        $data['id'] = $id;

        return view('Backend.OrderSupplier.upd', compact('data'));
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
        $route_name = 'order_supplier';
        $act = 'upd';
        $data = $request->except('_token' , '_method');
        $data['id'] = $id;

        $this->orderSupplierService->updateOrderSupplier($data);

        return view('backend.success', compact('route_name' , 'act'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $route_name = 'order_supplier';
        $act = 'del';

        OrderSupplier::destroy($id);

        return view('backend.success', compact('route_name' , 'act'));
    }

    public function ajax(Request $request){
        $rs = $request->all();

        $data = [];
        if ($rs['get_type'] == 'order_supplier'){
            $data = $this->orderSupplierService->getOrderSupplierById($rs['id']);
        }elseif ($rs['get_type'] == 'order_supplier_detail'){
            $data = $this->orderSupplierService->getOrderSupplierDetail($rs['id']);

        }
        echo "OK@@".json_encode($data);
    }

    public function ajaxDelItem($id){

    }

}
