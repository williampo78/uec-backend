<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationDetails;
// use App\Services\ItemService;
use App\Services\BrandsService;
use App\Services\QuotationService;
use App\Services\SupplierService;
use App\Services\UniversalService;
use App\Services\WarehouseService;
use App\Services\ProductsService ; 
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
        $this->productsService = $productsService ; 
        $this->brandsService = $brandsService ;
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
        $brands =  $this->brandsService->getBrands()->keyBy('id')->toArray() ;
        $result['products_item'] = $this->productsService->getItemsAndProduct()->transform(function ($obj, $key) use ($brands) {
            $obj->brands_name = $brands[$obj->brand_id]['brand_name'] ?? ''; //不做join key find val
            return $obj;
        });;
       
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
        $route_name = 'quotation';
        $act = 'add';
        $data = $request->except('_token');
        if (isset($data['status_code'])) {
            $act = $data['status_code'];
        }
        $this->quotationService->addQuotation($data);
        dd($data) ;

        return view('Backend.success', compact('route_name', 'act'));
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
        $data['supplier'] = $supplier->getSuppliers();
        $data['quotation'] = $this->quotationService->getQuotationById($id);
        $data['quotation_detail'] = $this->quotationService->getQuotationDetail($id);
        $data['act'] = 'upd';
        $data['id'] = $id;

        return view('Backend.Quotation.add', compact('data'));
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
            case 'itemlist':
                // $data = $this->itemService->getItemList();
                break;
            case 'iteminfo':
                // $data = $this->itemService->getItemInfo($rs['item_id']);
                break;
            case 'quotation':
                $quotationStatus = $this->quotationService->getStatusCode();
                $taxList = $this->quotationService->getTaxList();
                $supplier = new SupplierService();
                $supplierList = $this->universalService->idtokey($supplier->getSuppliers());
                $data = $this->quotationService->getQuotationById($rs['id']);
                $data['status_code'] = $quotationStatus[$data['status_code']] ?? '';
                $data['supplier_name'] = $supplierList[$data['supplier_id']]->name ?? '';
                $data['tax'] = $taxList[$data['tax']] ?? '';
                break;
            case 'quotation_detail':
                $data = $this->quotationService->getQuotationDetail($rs['id']);
                // if (isset($rs['action']) && $rs['action'] == 'upd') {
                //     $itemList = $this->itemService->getItemList();
                //     echo "OK@@" . json_encode($data) . "@@" . json_encode($itemList);
                //     return false;
                // }
                // break;
            case 'quotation_view_log':
                $data = $this->quotationService->getQuotationReviewLog($rs['id']);
                break;
            default:
                # code...
                break;
        }
        echo "OK@@" . json_encode($data);
    }

    public function ajaxDelItem(Request $request)
    {
        $data = $request->all();

        QuotationDetails::destroy($data['id']);

        echo json_encode(['ok']);
    }
}
