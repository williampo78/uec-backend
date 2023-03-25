<?php

namespace App\Http\Controllers;

use App\Services\BrandsService;
use App\Services\PurchaseService;
use App\Services\SupplierService;
use App\Http\Resources\Purchase\PurchaseResource;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    private $supplierService ;
    private $purchaseService ;
    private $brandsService ;

    public function __construct(
        SupplierService $supplierService,
        PurchaseService $purchaseService,
        BrandsService $brandsService,
    ) {
        $this->supplierService = $supplierService;
        $this->purchaseService = $purchaseService;
        $this->brandsService = $brandsService;
    
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $payload = $request->only([
            'supplier',
            'company_number',
            'order_supplier_number',
            'trade_date_start',
            'trade_date_end',
            'number',
        ]);
        $result = [];
        $result['supplier'] = $this->supplierService->getSuppliers();

        if (!empty($payload)) {
            $result['purchase_data'] = PurchaseResource::collection($this->purchaseService->getPurchase($payload));
        }
        return view('backend.purchase.list', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
        $req = $request->input();
        switch ($req['type']) {
            case 'update_invoice':
                $result = $this->purchaseService->updateInvoice($req);
                return response()->json([
                    'in' => $req,
                    'status' => $result,
                ]);
                break;

            default:
                # code...
                break;
        }

    }
}
