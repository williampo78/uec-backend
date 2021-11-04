<?php

namespace App\Http\Controllers;

use App\Models\OrderSupplier;
use App\Services\OrderSupplierService;
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
        $data['order_supplier'] = ($getData) ? $this->orderSupplierService->getOrderSupplier($getData) : [];
        $data['status_code'] = $this->universalService->getStatusCode();
        if (!isset($getData['select_start_date']) || !isset($getData['select_end_date'])) {
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
        $supplier = new SupplierService();
        $data['supplier'] = $supplier->getSupplier();
        $data['requisitions_purchase'] = $this->requisitionsPurchaseService->getRequisitionsPurchaseList();
        foreach ($data['requisitions_purchase'] as $key => $val) {
            $data['requisitions_purchase'][$key]->text = $val->number;
        }
//      $data['order_supplier'] = $this->orderSupplierService->getOrderSupplierById($id);
        $data['tax'] = $this->universalService->getTaxList();
        $data['act'] = 'add';
        return view('Backend.OrderSupplier.input', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $route_name = 'order_supplier';
        $act = 'add';
        $data = $request->except('_token');
        if (isset($data['status_code'])) {
            $act = $data['status_code'];
        }
        // dd($data) ;
        $this->orderSupplierService->updateOrderSupplier($data, 'add');
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
        $supplier = new SupplierService();
        $data['supplier'] = $supplier->getSupplier();
        $data['order_supplier'] = $this->orderSupplierService->getOrderSupplierById($id)->toArray();
        $data['order_supplier_detail'] = $this->orderSupplierService->getOrderSupplierDetail($id)->toArray();
        $data['act'] = 'upd';
        $data['id'] = $id;
        return view('Backend.OrderSupplier.update', compact('data'));
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
        $data = $request->except('_token', '_method');
        $data['id'] = $id;

        $this->orderSupplierService->updateOrderSupplier($data, 'upd');
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
        $route_name = 'order_supplier';
        $act = 'del';

        OrderSupplier::destroy($id);

        return view('backend.success', compact('route_name', 'act'));
    }

    public function ajax(Request $request)
    {

        $in = $request->all();
        switch ($in['type']) {
            case 'getRequisitionsPurchase':
                $requisitionsPurchase = $this->requisitionsPurchaseService->getAjaxRequisitionsPurchase($in['id']); //請購單
                $requisitionsPurchaseDetail = $this->requisitionsPurchaseService->getAjaxRequisitionsPurchaseDetail($in['id']); //請購單內的品項
                return response()->json([
                    'status' => true,
                    'reqData' => $in,
                    'requisitionsPurchase' => $requisitionsPurchase , 
                    'requisitionsPurchaseDetail' => $requisitionsPurchaseDetail,
                ]);
                break;
            case 'order_supplier' :
                $data = $this->orderSupplierService->getOrderSupplierById($in['id']);
                return response()->json([
                    'status' => true,
                    'reqData' => $in,
                    'orderSupplier' => $data ,
                ]);
                break ;
            case 'del_order_supplier' :
                $result = $this->orderSupplierService->delOrderSupplierById($in['id']) ;
                return response()->json([
                    'status' => true,
                    'reqData' => $in,
                    'result' =>$result ,
                ]);
                break ; 
            default:
                # code...
                break;
        }
    }

    public function ajaxDelItem($id)
    {

    }

}
