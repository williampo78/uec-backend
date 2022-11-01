<?php

namespace App\Http\Controllers;

use App\Services\BrandsService;
use App\Services\PurchaseService;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(
        SupplierService $supplierService,
        PurchaseService $purchaseService,
        BrandsService $brandsService
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
            $result['purchase'] = $this->purchaseService->getPurchase($payload);
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
            case 'showPurchase':
                $data = [];
                $data['purchase'] = $this->purchaseService->getPurchase($req);
                $brands = $this->brandsService->getBrands()->keyBy('id')->toArray();
                $data['purchase_detail'] = $this->purchaseService->getPurchaseDetail(['purchase_id' => $req['id']])->transform(function ($obj, $key) use ($brands) {

                    $brandsName = isset($brands[$obj->brand_id]['brand_name']) ? $brands[$obj->brand_id]['brand_name'] : '品牌已被刪除';

                    $obj->combination_name = $brandsName . '-' . $obj->product_name;

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
                return view('backend.purchase.show', $data);
                break;

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
