<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use App\Models\QuotationDetail;
use App\Services\BrandsService;
use App\Services\ProductService;
use App\Services\SupplierService;
use App\Services\QuotationService;
use App\Services\UniversalService;
use App\Services\WarehouseService;

class QuotationController extends Controller
{
    private $quotationService;
    private $supplierService;
    private $productService;
    private $brandsService;

    public function __construct(
        QuotationService $quotationService,
        SupplierService $supplierService,
        ProductService $productService,
        BrandsService $brandsService
    ) {
        $this->quotationService = $quotationService;
        $this->supplierService = $supplierService;
        $this->productService = $productService;
        $this->brandsService = $brandsService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $in = $request->input();
        $result = [];
        $result['supplier'] = $this->supplierService->getSuppliers();
        $result['quotation'] = $this->quotationService->getQuotation($in);
        $result['status_code'] = $this->quotationService->getStatusCode();
        return view('backend.quotation.list', $result);

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
        $in = [
            'exclude_selling_channel'=>[
                'STORE'
            ],
        ];
        $result['products_item'] = $this->productService->getItemsAndProduct($in)->transform(function ($obj, $key) use ($brands) {
            $obj->brands_name = $brands[$obj->brand_id]['brand_name'] ?? ''; //不做join key find val
            return $obj;
        });
        $result['taxList'] = config('uec.tax_option');
        $result['act'] = 'add';
        return view('backend.quotation.add', $result);
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
            return view('backend.success', $result);
        } else {
            return view('backend.error', $result);
        }
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
        $result['supplier'] = $this->supplierService->getSuppliers();
        $result['quotation'] = $this->quotationService->getQuotationById($id);
        $result['quotation_details'] = $this->quotationService->getQuotationDetail($id);
        $brands = $this->brandsService->getBrands()->keyBy('id')->toArray();
        $result['products_item'] = $this->productService->getItemsAndProduct(['supplier_id' => $result['quotation']->supplier_id, 'exclude_selling_channel' => ['STORE']]);
        $result['taxList'] = config('uec.tax_option');
        $result['act'] = 'upd';
        $result['id'] = $id;
        return view('backend.quotation.add', $result);
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
        $route_name = 'quotation';
        $act = 'del';

        Quotation::destroy($id);

        return view('backend.success', compact('route_name', 'act'));
    }

    public function ajax(Request $request)
    {
        $in = $request->all();
        switch ($in['get_type']) {
            case 'showQuotation':
                $data = [];
                $data['quotation'] = $this->quotationService->getQuotationById($in['id']);
                $data['quotationDetails'] = $this->quotationService->getQuotationDetail_v2($in['id'])->transform(function ($obj, $key)  {
                    $obj->pos_item_no = $obj->productItem->pos_item_no ?? '';
                    $obj->brands_name = $obj->productItem->product->brand->brand_name ?? '';
                    $obj->item_no = $obj->productItem->item_no ?? '';
                    $obj->product_name = $obj->productItem->product->product_name ?? '' ;
                    if($obj->brands_name && $obj->product_name){
                        $obj->combination_name = "{$obj->brands_name}-{$obj->product_name}";
                    }
                    // dd($obj->spec_1_value) ;
                    if ($obj->spec_1_value) {
                        $obj->combination_name .= '-' . $obj->productItem->spec_1_value;
                    }

                    if ($obj->spec_2_value) {
                        $obj->combination_name .= '-' . $obj->productItem->spec_2_value;
                    }

                    $obj->min_purchase_qty = $obj->productItem->product->min_purchase_qty ?? '';
                    $obj->requisitions_purchase_number = '';
                    $tmp = [];
                    if($obj->productItem && $obj->productItem->requisitionsPurchaseDetails){
                        foreach($obj->productItem->requisitionsPurchaseDetails as $key=>$val){
                            array_push($tmp, $val->requisitionsPurchase->number);
                        }
                        $tmp = array_unique($tmp);
                        $obj->requisitions_purchase_number = implode(',', $tmp);
                    }
                    return $obj;
                });
                $data['quotationReviewLog'] = $this->quotationService->getQuotationReviewLog($in['id']);
                $data['taxlist'] = config('uec.tax_option');
                return view('backend.quotation.show', $data);
                break;
            //供應商取得商品
            case 'supplierGetProducts':
               $products =  $this->productService->getItemsAndProduct([
                    'supplier_id' => $in['supplier_id'],
                    'stock_type'=> 'A',
                    'exclude_selling_channel'=> ['STORE']
                ]);

                return response()->json([
                    'requestData'=>$in,
                    'products' =>$products ,
                ]);
                break;
            case 'check_quotation_items':
                $product_items =  array_unique($in['product_items']) ;
                $result = $this->quotationService->checkQuotationItems($product_items);
                return response()->json([
                    'status' => $result['status'],
                    'in' => $in,
                    'error_msg' => $result['error_msg'],
                ]);
                break;
            default:
                # code...
                break;
        }
    }

    public function ajaxDelItem(Request $request)
    {
        $data = $request->all();

        QuotationDetail::destroy($data['id']);

        echo json_encode(['ok']);
    }
}
