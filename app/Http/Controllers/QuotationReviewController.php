<?php

namespace App\Http\Controllers;

use App\Models\QuotationReviewLog;
use App\Services\QuotationService;
use App\Services\SupplierService;
use App\Services\UniversalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class QuotationReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $universalService;
    private $quotationService;

    public function __construct(QuotationService $quotationService, UniversalService $universalService)
    {
        $this->quotationService = $quotationService;
        $this->universalService = $universalService;
    }

    public function index()
    {
        $data['user_name'] = Auth::user()->user_name;
        $supplier = new SupplierService();
        $data['supplier'] = $this->universalService->idtokey($supplier->getSupplier());
        $data['status_code'] = $this->quotationService->getStatusCode();
        $data['quotation'] = $this->quotationService->getQuotationReview();

        return view('Backend.QuotationReview.list' , compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $agent_id = Auth::user()->agent_id;
        $data = [];

        return view('Backend.QuotationReview.review' , compact('data'));
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
        $data['supplier'] = $this->universalService->idtokey($supplier->getSupplier());
        $data['status_code'] = $this->quotationService->getStatusCode();
        $data['taxList'] = $this->quotationService->getTaxList();
        $data['quotation'] = $this->quotationService->getQuotationById($id);
        $data['quotation_detail'] = $this->quotationService->getQuotationDetail($id);
        $data['quotation_detail_log'] = $this->quotationService->getQuotationReviewLog($id);

        return view('Backend.QuotationReview.review' , compact('data'));
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

        $data = $request->except('_token' , '_method');
        $data['id'] = $id;
        $this->quotationService->updateQuotationReview($data);

        return view('backend.success', compact('route_name' , 'act'));
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
