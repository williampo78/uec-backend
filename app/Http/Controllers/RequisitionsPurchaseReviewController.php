<?php

namespace App\Http\Controllers;

use App\Services\RequisitionsPurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequisitionsPurchaseReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $requisition_uprchase_service;

    public function __construct(RequisitionsPurchaseService $requisitionsPurchaseService)
    {
        $this->requisition_uprchase_service = $requisitionsPurchaseService;
    }

    public function index()
    {
        $data = [];
        $data['user_name'] = Auth::user()->user_name;
        $data['requisition_purchase'] = [];

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
        //
        $data = [];
        $data['id'] = $id;
        $data['requisitions_purchase'] = [];
        $data['requisitions_purchase_detail'] = $this->requisition_uprchase_service->getRequisitionPurchaseDetail($id);
        $data['requisition_purchase_review_log'] = $this->requisition_uprchase_service->getRequisitionPurchaseReviewLog($id);

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
