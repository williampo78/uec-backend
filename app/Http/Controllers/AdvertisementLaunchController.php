<?php

namespace App\Http\Controllers;

use App\Services\AdvertisementService;
use App\Services\ProductsService;
use App\Services\WebCategoryHierarchyService;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $query_datas = [];

        $query_datas = $request->only(['slot_id', 'launch_status', 'start_at', 'end_at']);

        $ad_slots = $this->advertisement_service->getSlots();
        $ad_slot_contents = $this->advertisement_service->getSlotContents($query_datas);

        $ad_slot_contents = $ad_slot_contents->map(function ($obj, $key) {
            /*
             * 上下架狀態
             * 當前時間在上架時間內，且廣告上架內容的狀態為啟用，列為上架
             * 其他為下架
             */
            $obj->launch_status = (Carbon::now()->between($obj->start_at, $obj->end_at) && $obj->slot_content_active == 1) ? '上架' : '下架';

            return $obj;
        });

        return view('Backend.Advertisement.Launch.list', compact('ad_slots', 'ad_slot_contents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $ad_slots = $this->advertisement_service->getSlots();
        $product_category = $this->web_category_hierarchy_service->category_hierarchy_content();
        $products = $this->product_service->getProducts();

        return view('Backend.Advertisement.Launch.add', compact('ad_slots', 'product_category', 'products'));
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

        if (!$this->advertisement_service->addSlotContents($input_data)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $route_name = 'advertisemsement_launch';
        $act = 'add';

        return view('Backend.success', compact('route_name', 'act'));
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
        $ad_slot_content = $this->advertisement_service->getSlotContentById($id);

        $ad_slot_content['content']->slot_icon_name_url = !empty($ad_slot_content['content']->slot_icon_name) ? config('filesystems.disks.s3.url') . $ad_slot_content['content']->slot_icon_name : null;

        // 整理給前端的資料
        $ad_slot_content['content'] = $ad_slot_content['content']->only([
            'slot_code',
            'slot_desc',
            'slot_type',
            'start_at',
            'end_at',
            'slot_color_code',
            'slot_icon_name_url',
            'slot_title',
            'product_assigned_type',
            'slot_content_active',
            'is_user_defined',
            'product_assigned_type',
            'slot_content_id',
            'slot_id',
        ]);

        foreach ($ad_slot_content['details'] as $key => $obj) {
            $obj->image_name_url = !empty($obj->image_name) ? config('filesystems.disks.s3.url') . $obj->image_name : null;

            // 整理給前端的資料
            $ad_slot_content['details'][$key] = $obj->only([
                'id',
                'data_type',
                'sort',
                'texts',
                'image_name_url',
                'image_alt',
                'image_title',
                'image_abstract',
                'image_action',
                'is_target_blank',
                'product_id',
                'web_category_hierarchy_id',
                'target_url',
                'target_cate_hierarchy_id',
            ]);
        }

        $product_category = $this->web_category_hierarchy_service->category_hierarchy_content();
        $products = $this->product_service->getProducts();

        return view('Backend.Advertisement.Launch.update', compact('ad_slot_content', 'product_category', 'products'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slot_content_id)
    {
        $input_data = $request->except('_token', '_method');
        $input_data['slot_content_id'] = $slot_content_id;

        if (!$this->advertisement_service->updateSlotContents($input_data)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $route_name = 'advertisemsement_launch';
        $act = 'upd';

        return view('Backend.success', compact('route_name', 'act'));
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

    public function getDetail(Request $request)
    {
        $slot_content_id = $request->input('slot_content_id');

        // 取得商品分類
        $product_category = $this->web_category_hierarchy_service->category_hierarchy_content();
        $product_category_format = array_column($product_category, 'name', 'id');

        // 取得商品
        $products = $this->product_service->getProducts();
        $products_format = [];
        foreach ($products as $value) {
            if (isset($products_format[$value['id']])) {
                $products_format[$value['id']] = "{$value['product_no']} {$value['product_name']}";
            }
        }

        $ad_slot_content = $this->advertisement_service->getSlotContentById($slot_content_id);

        $ad_slot_content['content']->slot_icon_name_url = !empty($ad_slot_content['content']->slot_icon_name) ? config('filesystems.disks.s3.url') . $ad_slot_content['content']->slot_icon_name : null;

        // 整理給前端的資料
        $ad_slot_content['content'] = $ad_slot_content['content']->only([
            'slot_code',
            'slot_desc',
            'slot_type',
            'start_at',
            'end_at',
            'slot_color_code',
            'slot_icon_name_url',
            'slot_title',
            'product_assigned_type',
            'slot_content_active',
        ]);

        foreach ($ad_slot_content['details'] as $key => $obj) {
            $obj->image_name_url = !empty($obj->image_name) ? config('filesystems.disks.s3.url') . $obj->image_name : null;
            $obj->product = !empty($obj->product_id) ? $products_format[$obj->product_id] ?? null : null;
            $obj->product_category = !empty($obj->web_category_hierarchy_id) ? $product_category_format[$obj->web_category_hierarchy_id] ?? null : null;

            switch ($obj->image_action) {
                // URL
                case 'U':
                    $obj->link_content = $obj->target_url;
                    break;
                // 商品分類
                case 'C':
                    $obj->link_content = $product_category_format[$obj->target_cate_hierarchy_id] ?? null;
                    break;
                default:
                    $obj->link_content = null;
                    break;
            }

            // 整理給前端的資料
            $ad_slot_content['details'][$key] = $obj->only([
                'data_type',
                'sort',
                'texts',
                'image_name_url',
                'image_alt',
                'image_title',
                'image_abstract',
                'image_action',
                'is_target_blank',
                'product',
                'product_category',
                'link_content',
            ]);
        }

        return response()->json($ad_slot_content);
    }

    /**
     * 是否可以通過廣告上架的狀態驗證
     *
     * @param Request $request
     * @return boolean
     */
    public function canPassActiveValidation(Request $request)
    {
        $active = $request->input('active');
        $slot_id = $request->input('slot_id');
        $start_at = $request->input('start_at');
        $end_at = $request->input('end_at');
        $slot_content_id = $request->input('slot_content_id');

        if ($active == 0) {
            return response()->json([
                'status' => true,
            ]);
        }

        if ($this->advertisement_service->canSlotContentActive($slot_id, $start_at, $end_at, $slot_content_id)) {
            return response()->json([
                'status' => true,
            ]);
        }

        return response()->json([
            'status' => false,
        ]);
    }
}
