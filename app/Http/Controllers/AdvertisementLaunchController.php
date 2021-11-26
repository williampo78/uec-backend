<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\ProductsService;
use App\Services\AdvertisementService;
use App\Services\WebCategoryHierarchyService;

class AdvertisementLaunchController extends Controller
{
    private $advertisement_service;
    private $web_category_hierarchy_service;
    private $product_service;

    public function __construct(
        AdvertisementService $advertisement_service,
        WebCategoryHierarchyService $web_category_hierarchy_service,
        ProductsService $product_service
    ) {
        $this->advertisement_service = $advertisement_service;
        $this->web_category_hierarchy_service = $web_category_hierarchy_service;
        $this->product_service = $product_service;
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

        $ad_slots = $this->advertisement_service->getSlots();
        $ad_slot_contents = $this->advertisement_service->getSlotContents($query_data);

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

        $ad_slots = $this->advertisement_service->getSlots();
        $product_category = $this->web_category_hierarchy_service->category_hierarchy_content();
        $products = $this->product_service->getProducts();

        $result['ad_slots'] = $ad_slots;
        $result['product_category'] = $product_category;
        $result['products'] = $products;

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

        $this->advertisement_service->addSlotContents($input_data);

        $route_name = 'advertisemsement_launch';
        $act = 'add';

        return view('Backend.success', compact('route_name' , 'act'));
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

    public function getDetailByAjax(Request $request)
    {
        $slot_content_id = $request->input('slot_content_id');

        $product_category = $this->web_category_hierarchy_service->category_hierarchy_content();
        $product_category_format = array_column($product_category, 'name', 'id');

        $products = $this->product_service->getProducts();
        $products_format = [];
        foreach ($products as $value) {
            $products_format[$value['id']] = "{$value['product_no']} {$value['product_name']}";
        }

        $ad_slot_content = $this->advertisement_service->getSlotContentById($slot_content_id);
        $content = $ad_slot_content['content']->toArray();

        $result = [];
        $result['content'] = array_filter($content, function($key) {
            return in_array($key, [
                'slot_code',
                'slot_desc',
                'is_mobile_applicable',
                'is_desktop_applicable',
                'slot_type',
                'is_user_defined',
                'start_at',
                'end_at',
                'slot_color_code',
                'slot_icon_name',
                'slot_title',
                'product_assigned_type',
                'slot_content_active',
                'description',
            ]);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($ad_slot_content['detail'] as $key => $value) {
            $detail = $value->toArray();

            $result['detail'][$key] = array_filter($detail, function($key) {
                return in_array($key, [
                    'data_type',
                    'sort',
                    'texts',
                    'image_name',
                    'image_alt',
                    'image_title',
                    'image_abstract',
                    'image_action',
                    'is_target_blank',
                ]);
            }, ARRAY_FILTER_USE_KEY);

            $result['detail'][$key]['product'] = null;
            $result['detail'][$key]['product_category'] = null;
            $result['detail'][$key]['link_content'] = null;

            if (isset($detail['product_id'])) {
                $result['detail'][$key]['product'] = $products_format[$detail['product_id']];
            }

            if (isset($detail['web_category_hierarchy_id'])) {
                $result['detail'][$key]['product_category'] = $product_category_format[$detail['web_category_hierarchy_id']];
            }

            if (isset($detail['image_action'])) {
                switch ($detail['image_action']) {
                    case 'U':
                        $result['detail'][$key]['link_content'] = $detail['target_url'];
                        break;

                    case 'C':
                        $result['detail'][$key]['link_content'] = $product_category_format[$detail['target_cate_hierarchy_id']];
                        break;
                }
            }
        }

        return response()->json($result);
    }
}
