<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationDetails;
// use App\Services\ItemService;
use App\Services\BrandsService;
use App\Services\ProductsService;
use App\Services\QuotationService;
use App\Services\SupplierService;
use App\Services\UniversalService;
use App\Services\WarehouseService;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $quotationService;
    private $universalService;
    // private $itemService;
    private $warehouseService;

    public function __construct(QuotationService $quotationService,
        UniversalService $universalService,
        WarehouseService $warehouseService,
        SupplierService $supplierService,
        ProductsService $productsService,
        BrandsService $brandsService) {
        $this->quotationService = $quotationService;
        $this->universalService = $universalService;
        $this->warehouseService = $warehouseService;
        $this->supplierService = $supplierService;
        $this->productsService = $productsService;
        $this->brandsService = $brandsService;
    }
    public function index(Request $request)
    {
        $in = $request->input();
        $result = [];
        $result['supplier'] = $this->supplierService->getSuppliers();
        $result['quotation'] = $this->quotationService->getQuotation($in);
        $result['status_code'] = $this->quotationService->getStatusCode();
        return view('Backend.Quotation.list', $result);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $result['supplier'] = $this->supplierService->getSuppliers();
        $brands = $this->brandsService->getBrands()->keyBy('id')->toArray();
        $result['products_item'] = $this->productsService->getItemsAndProduct()->transform(function ($obj, $key) use ($brands) {
            $obj->brands_name = $brands[$obj->brand_id]['brand_name'] ?? ''; //不做join key find val
            return $obj;
        });
        $result['taxList'] = config('uec.tax_option');
        $result['act'] = 'add';
        return view('Backend.Quotation.add', $result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except('_token');
        $result = $this->quotationService->addQuotation($data);
        $result['route_name'] = 'quotation';
        $result['act'] = 'add';
        
        if ($result['status']) {
            return view('Backend.success', compact('route_name', 'act'));
        } else {
            return view('Backend.error', $result);
        };
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
        $result['supplier'] = $supplier->getSuppliers();
        $result['quotation'] = $this->quotationService->getQuotationById($id);
        $result['quotation_details'] = $this->quotationService->getQuotationDetail($id);
        $brands = $this->brandsService->getBrands()->keyBy('id')->toArray();
        $result['products_item'] = $this->productsService->getItemsAndProduct()->transform(function ($obj, $key) use ($brands) {
            $obj->brands_name = $brands[$obj->brand_id]['brand_name'] ?? ''; //不做join key find val
            return $obj;
        });
        $result['taxList'] = config('uec.tax_option');
        $result['act'] = 'upd';
        $result['id'] = $id;
        return view('Backend.Quotation.add', $result);
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
        $route_name = 'quotation';
        $act = 'upd';
        $data = $request->except('_token', '_method');
        $data['id'] = $id;

        $this->quotationService->updateQuotation($data);

        return view('Backend.success', compact('route_name', 'act'));
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

        return view('Backend.success', compact('route_name', 'act'));
    }

    public function ajax(Request $request)
    {
        $rs = $request->all();
        switch ($rs['get_type']) {
            case 'showQuotation':
                $data = [];
                $data['quotation'] = $this->quotationService->getQuotationById($rs['id']);
                $brands = $this->brandsService->getBrands()->keyBy('id')->toArray();
                $data['quotationDetails'] = $this->quotationService->getQuotationDetail($rs['id'])->transform(function ($obj, $key) use ($brands) {

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
                $data['quotationReviewLog'] = $this->quotationService->getQuotationReviewLog($rs['id']);

                $data['taxlist'] = config('uec.tax_option');
                return view('Backend.Quotation.show', $data);
                break;
            default:
                # code...
                break;
        }
    }

    public function ajaxDelItem(Request $request)
    {
        $data = $request->all();

        QuotationDetails::destroy($data['id']);

        echo json_encode(['ok']);
    }
}
