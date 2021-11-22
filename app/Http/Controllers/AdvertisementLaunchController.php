<?php

namespace App\Http\Controllers;

use App\Services\AdvertisementService;
use Illuminate\Http\Request;

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
        $blocks = [];
        $ad_slots = [];

        $ad_slots = $this->advertisementService->getSlots($query_data);

        foreach ($ad_slots as $obj) {
            $blocks[$obj->id] = '【' . $obj->slot_code . '】' . $obj->slot_desc;
        }

        $result['ad_slots'] = $ad_slots;
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
