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
        $query_data = [];

        $query_data = $request->only(['applicable_page', 'device', 'active']);

        $ad_slots = $this->advertisementService->getSlots($query_data);
        $applicable_page = $this->lookupValuesVService->getApplicablePage();

        return view(
            'Backend.Advertisement.Block.list',
            compact('ad_slots', 'applicable_page', 'query_data')
        );
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
        $ad_slot = $this->advertisementService->getSlotById($id);

        return view('Backend.Advertisement.Block.update', compact('ad_slot'));
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

        if (! $this->advertisementService->updateSlot($input_data)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $route_name = 'advertisemsement_block';
        $act = 'upd';

        return view(
            'Backend.success',
            compact('route_name' , 'act')
        );
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

    public function getDetailByAjax(Request $request)
    {
        $slot_id = $request->input('slot_id');

        $ad_slot = $this->advertisementService->getSlotById($slot_id);

        $ad_slot->remark = nl2br($ad_slot->remark);

        // 整理給前端的資料
        $ad_slot = $ad_slot->only([
            'active',
            'description',
            'is_desktop_applicable',
            'is_mobile_applicable',
            'remark',
            'slot_code',
            'slot_desc',
            'slot_type',
        ]);

        return response()->json([
            'ad_slot' => $ad_slot,
        ]);
    }
}
