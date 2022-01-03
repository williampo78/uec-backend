<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Services\PurchaseService;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BuyoutProductsReportController extends Controller
{
    public function __construct(SupplierService $supplierService,
        PurchaseService $purchaseService) {
        $this->supplierService = $supplierService;
        $this->purchaseService = $purchaseService;
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
        $result['supplier'] = $this->supplierService->getSuppliers(); //供應商
        if (count($in) > 0) { //是否開始搜尋
            $result['buy_out_products'] = $this->purchaseService->getBuyOutProducts($in)->get();
            $result['excel_url'] = $request->fullUrl() . '&export=true';

            if (isset($in['export'])) { //匯出報表
               return $this->export($result['buy_out_products']);
            }
        }

        return view('Backend.BuyoutProductsReport.list', $result);
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
    public function export($data)
    {
        $title = [
            "index" => "項次",
            "supplier_name" => "供應商",
            "trade_date" => "進貨日期",
            "number" => "進貨單號",
            "order_supplier_tax" => "採購單號",
            "order_supplier_tax" => "採購單稅別",
            "item_no" => "Item編號",
            "pos_item_no" => "POS品號",
            "product_name" => "商品名稱",
            "spec_1_value" => "規格一",
            "spec_2_value" => "規格二",
            "item_price" => "單價",
            "item_qty" => "數量",
            "detail_original_subtotal_price" => "未稅金額",
            "detail_subtotal_tax_price" => "稅額",
            "detail_subtotal_nontax_price" => "含稅金額",
        ];
        $data = $data->toArray();
        $export = new ReportExport($title, $data);
        return Excel::download($export, '買斷商品對帳單'.date('Y-m-d').'.xlsx');
    }
}
