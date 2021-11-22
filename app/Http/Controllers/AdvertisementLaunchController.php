<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\AdvertisementService;

class AdvertisementLaunchController extends Controller
{
    private $advertisementService;

    public function __construct(AdvertisementService $advertisementService) {
        $this->advertisementService = $advertisementService;
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
        $blocks = []; // 版位下拉選單選項
        $ad_slots = [];

        $query_data = $request->only(['block', 'launch_status', 'start_at', 'end_at']);

        $ad_slots = $this->advertisementService->getSlots();
        $ad_slot_contents = $this->advertisementService->getSlotContents($query_data);

        foreach ($ad_slots as $obj) {
            $blocks[$obj->id] = '【' . $obj->slot_code . '】' . $obj->slot_desc;
        }

        $ad_slot_contents = $ad_slot_contents->map(function ($obj, $key) {
            /*
                上下架狀態
                當前時間在上架時間內，且廣告上架內容的狀態為啟用，列為上架
                其他為下架
            */
            $obj->launch_status = (Carbon::now()->between($obj->start_at, $obj->end_at) && $obj->slot_content_active == 1) ? '上架' : '下架';

            return $obj;
        });

        $result['ad_slot_contents'] = $ad_slot_contents;
        $result['blocks'] = $blocks;
        $result['query_data'] = $query_data;

        return view('Backend.Advertisement.Launch.list', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $result = [];
        $blocks = []; // 版位下拉選單選項

        $ad_slots = $this->advertisementService->getSlots();

        foreach ($ad_slots as $obj) {
            $blocks[$obj->id] = '【' . $obj->slot_code . '】' . $obj->slot_desc;
        }

        $result['blocks'] = $blocks;

        return view('Backend.Advertisement.Launch.add', $result);
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
