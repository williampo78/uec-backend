<?php

namespace App\Http\Controllers;

use App\Services\WebCategoryHierarchyService;
use Illuminate\Http\Request;

class WebCategoryHierarchyControllers extends Controller
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
    public function index()
    {
        $result['category_level_1'] = $this->webCategoryHierarchyService->web_Category_Hierarchy_Bylevel();
        return view('Backend.WebCategoryHierarchy.index', $result);
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
    public function GetCategory()
    {

    }
    public function ajax(Request $request)
    {
        $in = $request->input() ;
        
        switch ($in['type']) {
            case 'GetCategory': // 取得子分類
                $result = $this->webCategoryHierarchyService->web_Category_Hierarchy_Bylevel($in['id']);
                break;
            default:
                # code...
                break;
        }
        return response()->json([
            'status' => true,
            'in' => $request->input(),
            'result' => $result 
        ]);
    }
}
