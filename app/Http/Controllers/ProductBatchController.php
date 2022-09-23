<?php

namespace App\Http\Controllers;

use App\Services\ProductBatchService;
use App\Services\SupplierService;
use App\Imports\Product\BatchImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

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
        // $batchLogList = $this->batchService->getList();
        $result = [
            'supplier' => $this->supplierService->getSuppliers(['active' => '1']),
            // 'log_list' => BatchResource::collection($batchLogList),
            'log_list' => [],
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
        $productBatchData = $this->productBatchService->getById($id);
        $excelData = Excel::toArray(new BatchImport, $productBatchData->saved_file_1_name, '');

        $products = collect($excelData[0]); // 取得商品
        $productPhoto = collect($excelData[1]); //取得照片

        $zipAbsolutePath = Storage::path($productBatchData->saved_file_2_name);
        $endPath = $this->getFileFolderPath($productBatchData->saved_file_2_name);
        $extract = Storage::disk('s3')->extractTo($endPath, $zipAbsolutePath); //壓縮完後丟到S3


        try {

        } catch (\Exception $e) {
            Log::channel('batch_upload')->warning($e->getMessage());
            $this->updateStatusById($productBatchData->id, 2, [
                'job_completed_log' => '取得Excel內容失敗',
            ]);

            return false;
        }
        $products = collect($excelData[0]); // 取得商品
        $productPhoto = collect($excelData[1]); //取得照片
        // 取得壓縮檔案位置

        Storage::deleteDirectory($endPath);
        if (!$extract) {
            $this->updateStatusById($logData->id, 2, [
                'job_completed_log' => '解壓縮失敗',
            ]);

            return false;
        }
        $products = $this->arrangeProduct($products);
        $verifyProduct = $this->verifyProduct($products); //檢查基本商品
        $verifySkuItem = $this->verifySkuItem($products); //進階檢查規格
        $verifyPhoto = $this->verifyPhoto($endPath, $productPhoto); //檢查照片
        // //驗證未過
        if (!empty($verifyProduct) || !empty($verifySkuItem || !empty($verifyPhoto))) {
            $random = Str::random(40);
            $excelEndPath = "log/SupReqProduct/{$random}.xlsx";
            Excel::store(new ErrorImpoerLogExport($this->exportForm([
                'verifyProduct' => $verifyProduct,
                'verifySkuItem' => $verifySkuItem,
                'verifyPhoto' => $verifyPhoto,
            ])), $excelEndPath, 's3');
            $this->updateStatusById($logData->id, 2, [
                'job_completed_log' => '無申請單產生，請檢查並更正資料後，再重新上傳！',
                'job_log_file' => $excelEndPath,
            ]);

            return false;
        }
        // 如果都沒錯誤 準備寫入新品提報table
        $products = $this->addProductForm($products, $endPath, $productPhoto);
        if ($products) {
            $job_completed_log = "產生《{$products['count']}》張申請單：《{$products['requestNoStart']}》~ 《{$products['requestNoEnd']}》";
            $this->updateStatusById($logData->id, 1, [
                'job_completed_log' => $job_completed_log,
            ]);

            return true;
        } else {
            $this->updateStatusById($logData->id, 2, [
                'job_completed_log' => '建立新品提報時發生未預期的錯誤',
            ]);

            return false;
        }
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
