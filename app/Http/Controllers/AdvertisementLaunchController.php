<?php

namespace App\Http\Controllers;

use App\Http\Resources\Advertisement\Launch\WebCategoryHierarchyResource;
use App\Services\AdvertisementService;
use App\Services\ProductService;
use App\Services\WebCategoryHierarchyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdvertisementLaunchController extends Controller
{
    private $advertisementService;
    private $webCategoryHierarchyService;
    private $productService;

    public function __construct(
        AdvertisementService $advertisementService,
        WebCategoryHierarchyService $webCategoryHierarchyService,
        ProductService $productService
    ) {
        $this->advertisementService = $advertisementService;
        $this->webCategoryHierarchyService = $webCategoryHierarchyService;
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $queryData = $request->only([
            'slot_id',
            'launch_status',
            'start_at_start',
            'start_at_end',
            'slot_title',
        ]);

        $adSlots = $this->advertisementService->getSlots();
        $adSlotContents = $this->advertisementService->getSlotContents($queryData);
        $this->advertisementService->restructureAdSlotContents($adSlotContents);

        return view('backend.advertisement.launch.list', [
            'ad_slots' => $adSlots,
            'ad_slot_contents' => $adSlotContents,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categoryTree = $this->webCategoryHierarchyService->getDescendantsUntilMaxLevel();

        return view('backend.advertisement.launch.create', [
            'payload' => [
                'ad_slots' => $this->advertisementService->getSlots(),
                'product_category' => $this->webCategoryHierarchyService->getMaxLevelCategories(),
                'products' => $this->productService->getProducts([
                    'product_type' => 'N',
                ]),
                'category_tree' => WebCategoryHierarchyResource::collection($categoryTree),
                'max_level' => config('uec.web_category_hierarchy_levels'),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputData = $request->except('_token');
        // 驗證檔案格式
        $validatedData = $request->validate([
            'slot_icon_name' => 'mimes:jpeg,png,jpg',
        ]);
        dd($validatedData);
        if (!$this->advertisementService->addSlotContents($inputData)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $route_name = 'advertisemsement_launch';
        $act = 'add';

        return view('backend.success', compact('route_name', 'act'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // 取得商品
        $products = $this->productService->getProducts();
        $products_format = [];
        foreach ($products as $value) {
            $products_format[$value['id']] = "{$value['product_no']} {$value['product_name']}";
        }

        $ad_slot_content = $this->advertisementService->getSlotContentById($id);

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
            $obj->product_category = isset($obj->web_category_hierarchy_id) ? $this->webCategoryHierarchyService->getAncestorsAndSelfName($obj->web_category_hierarchy_id) : null;

            switch ($obj->image_action) {
                // URL
                case 'U':
                    $obj->link_content = $obj->target_url;
                    break;
                // 商品分類
                case 'C':
                    $obj->link_content = isset($obj->target_cate_hierarchy_id) ? $this->webCategoryHierarchyService->getAncestorsAndSelfName($obj->target_cate_hierarchy_id) : null;
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categoryTree = $this->webCategoryHierarchyService->getDescendantsUntilMaxLevel();
        $adSlotContent = $this->advertisementService->getSlotContentById($id);

        $adSlotContent['content']->slot_icon_name_url = !empty($adSlotContent['content']->slot_icon_name) ? config('filesystems.disks.s3.url') . $adSlotContent['content']->slot_icon_name : null;
        // 整理給前端的資料
        $adSlotContent['content'] = $adSlotContent['content']->only([
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
            'contents_remark',
            'photo_width',
            'photo_height',
            'see_more_action',
            'see_more_url',
            'see_more_cate_hierarchy_id',
            'see_more_target_blank',
            'slot_title_color',
        ]);
        foreach ($adSlotContent['details'] as $key => $obj) {
            $obj->image_name_url = !empty($obj->image_name) ? config('filesystems.disks.s3.url') . $obj->image_name : null;
            // 整理給前端的資料
            $adSlotContent['details'][$key] = $obj->only([
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
                'campaign_brief',
                'target_campaign_id',
            ]);
        }

        return view('backend.advertisement.launch.edit', [
            'payload' => [
                'ad_slot_content' => $adSlotContent,
                'product_category' => $this->webCategoryHierarchyService->getMaxLevelCategories(),
                'products' => $this->productService->getProducts([
                    'product_type' => 'N',
                ]),
                'category_tree' => WebCategoryHierarchyResource::collection($categoryTree),
                'max_level' => config('uec.web_category_hierarchy_levels'),
            ],
        ]);
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
        $inputData = $request->except('_token', '_method');
        $inputData['slot_content_id'] = $slot_content_id;

        $validatorFile = Validator::make($request->all(), [
            'slot_icon_name' => 'mimes:jpeg,png,jpg',
        ]);

        if ($validatorFile->fails()) {
            return back()->withErrors(['message' => '檔案格式錯誤']);
        }

        if (!$this->advertisementService->updateSlotContents($inputData)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $route_name = 'advertisemsement_launch';
        $act = 'upd';

        return view('backend.success', compact('route_name', 'act'));
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

        if ($this->advertisementService->canSlotContentActive($slot_id, $start_at, $end_at, $slot_content_id)) {
            return response()->json([
                'status' => true,
            ]);
        }

        return response()->json([
            'status' => false,
        ]);
    }

    /**
     * 取得活動賣場
     *
     * @param Request $request
     * @return json
     */
    public function searchPromotionCampaign(Request $request)
    {
        $in = $request->input();
        $data = $this->advertisementService->searchPromotionCampaign($in);

        return response()->json([
            'status' => true,
            'data' => $data,
            'in' => $in,
        ]);
    }
}
