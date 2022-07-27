<?php

namespace App\Http\Controllers;

use App\Http\Resources\WebCategoryHierarchy\WebCategoryHierarchyResource;
use App\Services\WebCategoryHierarchyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebCategoryHierarchyController extends Controller
{
    private $webCategoryHierarchyService;

    public function __construct(WebCategoryHierarchyService $webCategoryHierarchyService)
    {
        $this->webCategoryHierarchyService = $webCategoryHierarchyService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = $this->webCategoryHierarchyService->getSiblingCategories();

        $responsePayload = [
            'max_level' => config('uec.web_category_hierarchy_levels'),
            'categories' => WebCategoryHierarchyResource::collection($categories),
            'auth' => $request->share_role_auth,
        ];

        return view('backend.web_category_hierarchy.list', [
            'payload' => $responsePayload,
        ]);
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
        $requestPayload = $request->only([
            'category_name',
            'category_level',
            'parent_id',
            'gross_margin_threshold',
            'category_short_name',
            'icon_name',
        ]);

        $createResult = $this->webCategoryHierarchyService->createCategory($requestPayload);

        if (!$createResult['is_success']) {
            return response()->json([
                'message' => '新增失敗',
            ], 500);
        }

        return response()->json([
            'message' => '新增成功',
            'payload' => WebCategoryHierarchyResource::make($createResult['category']),
        ]);
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
        $requestPayload = $request->only([
            'category_name',
            'gross_margin_threshold',
            'category_short_name',
            'icon_name',
            'isIconDeleted',
        ]);

        $updateResult = $this->webCategoryHierarchyService->updateCategory($id, $requestPayload);

        if (!$updateResult['is_success']) {
            return response()->json([
                'message' => '更新失敗',
            ], 500);
        }

        return response()->json([
            'message' => '更新成功',
            'payload' => WebCategoryHierarchyResource::make($updateResult['category']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->webCategoryHierarchyService->hasChildCategories($id)) {
            return response()->json([
                'message' => '請將該分類底下的子分類清空，才能執行該操作',
                'code' => 'E100',
            ], 400);
        }

        if ($this->webCategoryHierarchyService->hasProducts($id)) {
            return response()->json([
                'message' => '請將該分類有使用到的商品清空，才能執行該操作',
                'code' => 'E101',
            ], 400);
        }

        $deleteResult = $this->webCategoryHierarchyService->deleteCategory($id);

        if (!$deleteResult['is_success']) {
            return response()->json([
                'message' => '刪除失敗',
            ], 500);
        }

        return response()->noContent();
    }

    /**
     * 取得多筆分類
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCategories(Request $request): JsonResponse
    {
        $requestPayload = $request->only([
            'parent_id',
        ]);

        $categories = $this->webCategoryHierarchyService->getSiblingCategories($requestPayload['parent_id']);

        return response()->json([
            'payload' => [
                'title' => $this->webCategoryHierarchyService->getAncestorsAndSelfName($requestPayload['parent_id']),
                'categories' => WebCategoryHierarchyResource::collection($categories),
            ],
        ]);
    }

    public function ajax(Request $request)
    {
        $in = $request->input();
        $file = $request->file();
        switch ($in['type']) {
            case 'SortCategory':
                $result = $this->webCategoryHierarchyService->sort_Category_Hierarchy($in);
                break;
        }

        return response()->json([
            'status' => true,
            'in' => $request->input(),
            'result' => $result,
        ]);
    }
}
