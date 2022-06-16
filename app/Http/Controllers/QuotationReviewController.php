<?php

namespace App\Http\Controllers;

use App\Services\QuotationService;
use App\Services\ReviewService;
use App\Services\SupplierService;
use App\Services\UniversalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuotationReviewController extends Controller
{
    private $universalService;
    private $quotationService;
    private $reviewService;

    public function __construct(
        QuotationService $quotationService,
        UniversalService $universalService,
        ReviewService $reviewService
    ) {
        $this->quotationService = $quotationService;
        $this->universalService = $universalService;
        $this->reviewService = $reviewService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['user_name'] = Auth::user()->user_name;
        $supplier = new SupplierService();
        $data['supplier'] = $this->universalService->idtokey($supplier->getSuppliers());
        $data['status_code'] = $this->quotationService->getStatusCode();
        $data['quotation'] = $this->quotationService->getQuotationReview();
        return view('backend.quotation_review.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

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
        $data['id'] = $id;
        $supplier = new SupplierService();
        $data['supplier'] = $this->universalService->idtokey($supplier->getSuppliers());
        $data['status_code'] = $this->quotationService->getStatusCode();
        $data['taxList'] = config('uec.tax_option');
        $data['quotation'] = $this->quotationService->getQuotationById($id);
        $data['quotation_detail'] = $this->quotationService->getQuotationDetail($id);
        $data['quotation_detail_log'] = $this->quotationService->getQuotationReviewLog($id);
        return view('backend.quotation_review.review', compact('data'));
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
        $route_name = 'quotation_review';
        $act = 'review';

        $data = $request->only(['review_result', 'review_remark']);
        $data['id'] = $id;
        $data['created_by'] = $this->quotationService->getQuotationById($id)->created_by;
        $this->reviewService->updateReview($data, 'QUOTATION');
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
        //

    }

}
