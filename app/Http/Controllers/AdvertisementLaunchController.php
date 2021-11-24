<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\AdvertisementService;
use App\Services\WebCategoryHierarchyService;

class AdvertisementLaunchController extends Controller
{
    private $advertisementService;
    private $webCategoryHierarchyService;

    public function __construct(
        AdvertisementService $advertisementService,
        WebCategoryHierarchyService $webCategoryHierarchyService
    ) {
        $this->advertisementService = $advertisementService;
        $this->webCategoryHierarchyService = $webCategoryHierarchyService;
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
        $ad_slots = [];

        $query_data = $request->only(['block', 'launch_status', 'start_at', 'end_at']);

        $ad_slots = $this->advertisementService->getSlots();
        $ad_slot_contents = $this->advertisementService->getSlotContents($query_data);

        $ad_slot_contents = $ad_slot_contents->map(function ($obj, $key) {
            /*
                上下架狀態
                當前時間在上架時間內，且廣告上架內容的狀態為啟用，列為上架
                其他為下架
            */
            $obj->launch_status = (Carbon::now()->between($obj->start_at, $obj->end_at) && $obj->slot_content_active == 1) ? '上架' : '下架';

            return $obj;
        });

        $result['ad_slots'] = $ad_slots;
        $result['ad_slot_contents'] = $ad_slot_contents;
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

        $ad_slots = $this->advertisementService->getSlots();
        $product_category = $this->webCategoryHierarchyService->category_hierarchy_content([
            'active' => 1
        ]);

        $result['ad_slots'] = $ad_slots;
        $result['product_category'] = $product_category;

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
        $input_data = $request->except('_token');

        $this->advertisementService->addSlotContents($input_data);

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
