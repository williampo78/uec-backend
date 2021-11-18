<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdvertisementService;
use App\Services\LookupValuesVService;

class AdvertisementBlockController extends Controller
{
    private $_advertisementService;
    private $_lookupValuesVService;

    public function __construct(
        AdvertisementService $advertisementService,
        LookupValuesVService $lookupValuesVService
    ) {
        $this->_advertisementService = $advertisementService;
        $this->_lookupValuesVService = $lookupValuesVService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $result = [];
        $query_data = [];

        $query_data['applicable_page'] = $request->query('applicable_page');
        $query_data['device'] = $request->query('device');
        $query_data['status'] = $request->query('status');

        $result['ad_slots'] = $this->_advertisementService->getSlots($query_data);
        $result['applicable_page'] = $this->_lookupValuesVService->getApplicablePage();
        $result['query_data'] = $query_data;
        $result['slot_type_option'] = $this->_advertisementService->getSlotTypeOption();
        $result['active_option'] = $this->_advertisementService->getActiveOption();

        return view('Backend.Advertisement.list', $result);
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
