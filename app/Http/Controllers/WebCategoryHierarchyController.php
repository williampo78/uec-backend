<?php

namespace App\Http\Controllers;

use App\Services\WebCategoryHierarchyService;
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
        $responsePayload = [
            'max_level' => config('uec.web_category_hierarchy_levels'),
            'level_1_categories' => $this->webCategoryHierarchyService->getSiblingCategoriesByLevel(1),
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
            'payload' => [
                'id' => $createResult['category']->id,
                'category_name' => $createResult['category']->category_name,
            ],
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
    public function GetCategory()
    {

    }
    public function ajax(Request $request)
    {
        $in = $request->input();
        $file = $request->file();
        switch ($in['type']) {
            case 'GetCategory': // 取得子分類
                $result = $this->webCategoryHierarchyService->web_Category_Hierarchy_Bylevel($in['id']);
                break;
            case 'AddCategory':
                $result = $this->webCategoryHierarchyService->add_Category_Hierarchy($in, $file);
                break;
            case 'EditCategory':
                $result = $this->webCategoryHierarchyService->edit_Category_Hierarchy($in, $file);
                break;
            case 'DelCategory':
                $result = $this->webCategoryHierarchyService->del_Category_Hierarchy($in);
                break;
            case 'SortCategory':
                $result = $this->webCategoryHierarchyService->sort_Category_Hierarchy($in);
                break;
            case 'DelIconPhoto':
                $result = $this->webCategoryHierarchyService->del_icon_photo($in['id']);
                break;
        }

        return response()->json([
            'status' => true,
            'in' => $request->input(),
            'result' => $result,
        ]);
    }
}
