<?php

namespace App\Http\Controllers;

use App\Services\QuotationService;
use App\Services\RequisitionsPurchaseService;
use App\Services\ReviewService;
use App\Services\SupplierService;
use App\Services\UniversalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RequisitionsPurchaseReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $requisition_purchase_service;
    private $quotation_service;
    private $review_service;
    private $universal_service;

    public function __construct(RequisitionsPurchaseService $requisitionsPurchaseService, QuotationService $quotationService, ReviewService $reviewService, UniversalService $universalService)
    {
        $this->requisition_purchase_service = $requisitionsPurchaseService;
        $this->quotation_service = $quotationService;
        $this->review_service = $reviewService;
        $this->universal_service = $universalService;
    }

    public function index()
    {
        $data = [];
        $data['user_name'] = Auth::user()->user_name;
        $supplier = new SupplierService();
        $data['supplier'] = $this->universal_service->idtokey($supplier->getSupplier());
        $data['status_code'] = $this->universal_service->getStatusCode();
        $data['requisition_purchase'] = $this->requisition_purchase_service->getRequisitionsPurchaseReview();

        return view('Backend.RequisitionsPurchaseReview.list' , compact('data'));
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
        $data = [];
        $data['id'] = $id;
        $supplier = new SupplierService();
        $data['supplier'] = $this->universal_service->idtokey($supplier->getSupplier());
        $data['status_code'] = $this->quotation_service->getStatusCode();
        $data['requisitions_purchase'] = $this->requisition_purchase_service->getRequisitionPurchaseById($id);
        $data['requisitions_purchase_detail'] = $this->requisition_purchase_service->getRequisitionPurchaseDetail($id);
        $data['requisition_purchase_review_log'] = $this->requisition_purchase_service->getRequisitionPurchaseReviewLog($id);

        return view('Backend.RequisitionsPurchaseReview.review' , compact('data'));
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
        $route_name = 'requisitions_purchase_review';
        $act = 'review';

        $data = $request->except('_token' , '_method');
        $data['id'] = $id;

        $this->review_service->updateReview($data , 'REQUISITION_PUR');

        return view('Backend.success', compact('route_name' , 'act'));
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
