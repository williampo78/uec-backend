<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdvertisementService;
use App\Services\LookupValuesVService;

class AdvertisementBlockController extends Controller
{
    private $advertisementService;
    private $lookupValuesVService;

    public function __construct(
        AdvertisementService $advertisementService,
        LookupValuesVService $lookupValuesVService
    ) {
        $this->advertisementService = $advertisementService;
        $this->lookupValuesVService = $lookupValuesVService;
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
        $query_data['active'] = $request->query('active');

        $result['ad_slots'] = $this->advertisementService->getSlots($query_data);
        $result['applicable_page'] = $this->lookupValuesVService->getApplicablePage();
        $result['query_data'] = $query_data;

        return view('Backend.Advertisement.Block.list', $result);
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
        $result = [];

        $result['ad_slot'] = $this->advertisementService->getSlotById($id);

        return view('Backend.Advertisement.Block.update', $result);
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
        $input_data = $request->only(['active', 'remark']);
        $input_data['id'] = $id;

        $this->advertisementService->updateSlot($input_data);

        $route_name = 'advertisemsement_block';
        $act = 'upd';
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

    public function ajax(Request $request)
    {
        $slot_id = $request->input('slot_id');

        $ad_slot = $this->advertisementService->getSlotById($slot_id);

        return response()->json([
            'ad_slot' => $ad_slot,
        ]);
    }
}
