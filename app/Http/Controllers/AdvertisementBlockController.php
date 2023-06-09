<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdvertisementService;
use App\Services\LookupValuesVService;

class AdvertisementBlockController extends Controller
{
    private $advertisement_service;
    private $lookup_values_v_service;

    public function __construct(
        AdvertisementService $advertisement_service,
        LookupValuesVService $lookup_values_v_service
    ) {
        $this->advertisement_service = $advertisement_service;
        $this->lookup_values_v_service = $lookup_values_v_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query_datas = [];

        $query_datas = $request->only(['applicable_page', 'device', 'active']);

        $ad_slots = $this->advertisement_service->getSlots($query_datas);
        $applicable_pages = $this->lookup_values_v_service->getLookupValuesVs([
            'type_code' => 'APPLICABLE_PAGE',
        ]);

        return view('backend.advertisement.block.list', compact('ad_slots', 'applicable_pages'));
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
        $ad_slot = $this->advertisement_service->getSlotById($id);

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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ad_slot = $this->advertisement_service->getSlotById($id);

        return view('backend.advertisement.block.update', compact('ad_slot'));
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

        if (! $this->advertisement_service->updateSlot($input_data)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $route_name = 'advertisemsement_block';
        $act = 'upd';

        return view(
            'backend.success',
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
}
