<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Services\SummaryStockService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SummaryStockController extends Controller
{
    private $summaryStock;

    public function __construct(SummaryStockService $summaryStock)
    {
        $this->summaryStock = $summaryStock;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $getData = $request->only([
            'smonth',
            'item_id_start',
            'item_id_end',
            'product_name',
            'export'
        ]);
        $data['info'] = ($getData ? $this->summaryStock->getSummaryStock($getData) : collect());

        $data['info']->transform(function ($item) {
            if (in_array($item->stock_type, ['A', 'B'], true)) {
                $item->begin_amount_display = null; //期初金額
                $item->item_cost_display    = null; //單位成本
                $item->end_amount_display   = null; //期末金額
            }
            if ($item->stock_type == 'A') {
                $item->sales_amount_display        = null; //銷貨金額
                $item->sales_return_amount_display = null; //銷退金額
            }
            return $item;
        });

        $data['sum'] = ($getData ? $this->summaryStock->getSummarySum($getData) : []);
        if (count($getData) > 0) {
            $data['excel_url'] = url("/") . $request->getRequestUri() . '&export=true';
        }
        if (isset($getData['export'])) { //匯出報表
            return $this->export($data['info']);
        }
        return view('backend.summary_stock.list', $data);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function export($data)
    {
        $title = [
            "item_no" => "Item編號",
            "product_name" => "商品名稱",
            "spec_1_value" => "規格1",
            "spec_2_value" => "規格2",
            "begin_qty" => "期初數量",
            "begin_amount_display" => "期初金額",
            "item_cost_display" => "單位成本",
            "rcv_qty" => "進貨數量",
            "rcv_amount" => "進貨金額",
            "rtv_qty" => "退貨數量",
            "rtv_amount" => "退貨金額",
            "sales_qty" => "銷貨數量",
            "sales_amount_display" => "銷貨金額",
            "sales_return_qty" => "銷退數量",
            "sales_return_amount_display" => "銷退金額",
            "adj_qty" => "盤差數量",
            "adj_amount" => "盤差金額",
            "shift_qty" => "調撥數量",
            "shift_amount" => "調撥金額",
            "end_qty" => "期末數量",
            "end_amount_display" => "期末金額",
        ];
        $data = $data->toArray();
        $export = new ReportExport($title, $data);
        return Excel::download($export, '進耗存彙總表' . date('Y-m-d') . '.xlsx');
    }

    public function ajaxDetail(Request $request)
    {
        $getData = $request->input();
        $rs = $this->summaryStock->setSummaryCost($getData['smonth']);

        return response()->json([
            'status' => $rs['status'],
            'alert' => $rs['alert'],
            'message' => $rs['message'],
            'results' => $rs['result'],
        ], 200);
    }
}
