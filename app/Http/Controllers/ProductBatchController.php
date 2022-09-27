<?php

namespace App\Http\Controllers;


use App\Services\ProductBatchService;
use App\Services\SupplierService;
use App\Http\Resources\Product\BatchResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductBatchController extends Controller
{

    private $supplierService;
    private $productBatchService;

    public function __construct(
        SupplierService $supplierService,
        ProductBatchService $productBatchService
    ) {
        $this->supplierService = $supplierService;
        $this->productBatchService = $productBatchService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $batchLogList = $this->productBatchService->batchLogList();
        $result = [
            'supplier' => $this->supplierService->getSuppliers(['active' => '1']),
            'log_list' => BatchResource::collection($batchLogList),
        ];

        return view('backend.products.batch.index', $result);
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
        $result = [] ;
        $excel = $this->productBatchService->saveFile($request->file('excel'));
        $zip = $this->productBatchService->saveFile($request->file('image_zip'));
        $inputLogData = [
            'source_file_1_name' => $excel['originalName'],
            'saved_file_1_name' => $excel['filePath'],
            'source_file_2_name' => $zip['originalName'],
            'saved_file_2_name' => $zip['filePath'],
            'status' => 0, //等待執行
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ];
        try {
            $batchUploadLogId = $this->productBatchService->addBatchUploadLog($inputLogData);
            $addJob = $this->productBatchService->addJob($batchUploadLogId);
            $result['act'] = 'upload_success';
            $result['route_name'] = 'product-batch-upload';
            return view('backend.success', $result);
        } catch (\Exception $e) {
            $result['route_name'] = 'product-batch-upload';
            $result['message'] = '上傳檔案失敗';
            $result['error_code'] = $e->getMessage();

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
}
