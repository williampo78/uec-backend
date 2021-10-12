<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Services\ItemService;
use App\Services\QuotationService;
use App\Services\RoleService;
use App\Services\SupplierService;
use App\Services\UniversalService;
use App\Services\WarehouseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $roleService;
    private $quotationService;
    private $universalService;
    private $itemService;
    private $warehouseService;

    public function __construct(RoleService $roleService, QuotationService $quotationService, UniversalService $universalService, ItemService $itemService, WarehouseService $warehouseService)
    {
        $this->roleService = $roleService;
        $this->quotationService = $quotationService;
        $this->universalService = $universalService;
        $this->itemService = $itemService;
        $this->warehouseService = $warehouseService;
    }
    public function index(Request $request)
    {
        $getData = $request->all();

        $data = [];
        $supplier = new SupplierService();
        $data['supplier'] = $this->universalService->idtokey($supplier->getSupplier());
        $data['quotation'] = $this->quotationService->getQuotation();
        $data['status_code'] = $this->quotationService->getStatusCode();
        $data['getData'] = $getData;

        return view('Backend.Quotation.list', compact('data'));
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

        return view('backend.quotation.add', compact('data'));
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
        $data = Warehouse::find($id);

        return view('backend.warehouse.upd', compact('data'));
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
        $data = $request->except('_token' , '_method');
        $data['updated_by'] = Auth::user()->id;

        Warehouse::where('id' ,$id)->update($data);
        $route_name = 'quotation';
        $act = 'upd';
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
        $route_name = 'quotation';
        $act = 'del';

        Quotation::destroy($id);

        return view('backend.success', compact('route_name' , 'act'));
    }

    public function ajax(Request $request){
        $rs = $request->all();

        if ($rs['get_type'] == 'itemlist'){
            $data = $this->itemService->getItemList();
        }elseif ($rs['get_type'] == 'iteminfo'){
            $data = $this->itemService->getItemInfo($rs['item_id']);
        }

        echo "OK@@".json_encode($data);
    }
}
