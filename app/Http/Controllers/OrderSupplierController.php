<?php

namespace App\Http\Controllers;

use App\Models\OrderSupplier;
use App\Services\BrandsService;
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

    public function __construct(UniversalService $universalService,
        OrderSupplierService $orderSupplierService,
        RequisitionsPurchaseService $requisitionsPurchaseService,
        BrandsService $brandsService) {
        $this->universalService = $universalService;
        $this->orderSupplierService = $orderSupplierService;
        $this->requisitionsPurchaseService = $requisitionsPurchaseService;
        $this->brandsService = $brandsService;
    }
    public function index(Request $request)
    {
        $getData = $request->all();

        $data = [];
        $supplier = new SupplierService();
        $data['supplier'] = $this->universalService->idtokey($supplier->getSuppliers());
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
        $data['supplier'] = $supplier->getSuppliers();
        $data['requisitions_purchase'] = $this->requisitionsPurchaseService->getRequisitionsPurchaseList();
        foreach ($data['requisitions_purchase'] as $key => $val) {
            $data['requisitions_purchase'][$key]->text = $val->number;
        }
        $data['tax'] = config('uec.tax_option');
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
        // if (isset($data['status_code'])) {
        //     $act = $data['status_code'];
        // }
        $result = $this->orderSupplierService->updateOrderSupplier($data, 'add');
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
        $result['supplier'] = $supplier->getSuppliers();
        $result['order_supplier'] = $this->orderSupplierService->getOrderSupplierById($id);
        $brands = $this->brandsService->getBrands()->keyBy('id')->toArray();
        $result['order_supplier_detail'] = $this->orderSupplierService->getOrderSupplierDetail($id)->transform(function ($obj, $key) use ($brands) {

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
        $result['act'] = 'upd';
        $result['id'] = $id;
        return view('Backend.OrderSupplier.update', $result);

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
        $route_name = 'order_supplier';
        $act = 'del';

        OrderSupplier::destroy($id);

        return view('Backend.success', compact('route_name', 'act'));
    }

    public function ajax(Request $request)
    {

        $in = $request->all();
        switch ($in['type']) {
            case 'getRequisitionsPurchase':

                $requisitionsPurchase = $this->requisitionsPurchaseService->getAjaxRequisitionsPurchase($in['id']); //請購單
                $brands = $this->brandsService->getBrands()->keyBy('id')->toArray();
                $requisitionsPurchaseDetail = $this->requisitionsPurchaseService->getRequisitionPurchaseDetail($in['id'])->transform(function ($obj, $key) use ($brands) {

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

                }); //請購單內的品項
                return response()->json([
                    'status' => true,
                    'reqData' => $in,
                    'requisitionsPurchase' => $requisitionsPurchase,
                    'requisitionsPurchaseDetail' => $requisitionsPurchaseDetail,
                ]);
                break;
            case 'order_supplier':
                $data = $this->orderSupplierService->getOrderSupplierById($in['id']);
                return response()->json([
                    'status' => true,
                    'reqData' => $in,
                    'orderSupplier' => $data,
                ]);
                break;
            case 'del_order_supplier':
                $result = $this->orderSupplierService->delOrderSupplierById($in['id']);
                return response()->json([
                    'status' => true,
                    'reqData' => $in,
                    'result' => $result,
                ]);
                break;
            case 'supplier_deliver_time':
                $result = $this->orderSupplierService->updateSupplierDeliverTime($in);
                $result = true;
                return response()->json([
                    'status' => true,
                    'reqData' => $in,
                    'result' => $result,
                ]);
                break;
            case 'show_supplier':
                $orderSupplier = $this->orderSupplierService->getOrderSupplierById($in['id']);
                $brands = $this->brandsService->getBrands()->keyBy('id')->toArray();
                $orderSupplierDetail = $this->orderSupplierService->getOrderSupplierDetail($in['id'])->transform(function ($obj, $key) use ($brands) {

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

                return response()->json([
                    'status' => true,
                    'reqData' => $in,
                    'orderSupplier' => $orderSupplier,
                    'orderSupplierDetail' => $orderSupplierDetail,
                ]);
                break;
            default:
                # code...
                break;
        }
    }

    public function ajaxDelItem($id)
    {

    }

}
