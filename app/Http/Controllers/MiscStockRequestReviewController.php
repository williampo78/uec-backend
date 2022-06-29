<?php

namespace App\Http\Controllers;

use App\Services\MiscStockRequestService;
use Illuminate\Http\Request;

class MiscStockRequestReviewController extends Controller
{
    private $miscStockRequestService;

    public function __construct(
        MiscStockRequestService $miscStockRequestService
    ) {
        $this->miscStockRequestService = $miscStockRequestService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $requestPayload = $request->only([
            'requestNo',
            'submittedAtStart',
            'submittedAtEnd',
        ]);

        $responsePayload = [
            'auth' => $request->share_role_auth,
        ];

        // 有編輯權限
        if ($request->share_role_auth['auth_update']) {
            // 進貨退出單
            $responsePayload['miscStockRequests'] = $this->miscStockRequestService->getStockReviewTableList($requestPayload);
            $responsePayload['miscStockRequests'] = $this->miscStockRequestService->formatStockReviewTableList($responsePayload['miscStockRequests']);
        }

        $response['payload'] = $responsePayload;

        return view('backend.misc_stock_request_review.list', $response);
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
}
