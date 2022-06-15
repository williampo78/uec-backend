<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PrimaryCategory;
use App\Services\CategoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $data = Category::all();
        $primary_category_list = $this->categoryService->getPrimaryCategoryForList();

        return view('backend.category.list', compact('data', 'primary_category_list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $agent_id = Auth::user()->agent_id;
        $primary_category = PrimaryCategory::where('agent_id', $agent_id)->get();
        return view('backend.category.add', compact('primary_category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $route_name = 'category';
        $act = 'add';
        $data = $request->only(['number','name','primary_category_id']);
        $data['agent_id'] = Auth::user()->agent_id;
        $data['created_by'] = Auth::user()->id;
        $data['created_at'] = Carbon::now();

        try {
            $rs = Category::insert($data);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->withErrors(['message' => '儲存失敗']);
        }

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

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Category::find($id);
        $primary_category_list = $this->categoryService->getPrimaryCategoryForList();

        return view('backend.category.upd', compact('data', 'primary_category_list'));
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
        $data = $request->only(['number','name','primary_category_id']);
        $data['updated_by'] = Auth::user()->id;
        $data['updated_at'] = Carbon::now();

        try {
            Category::findOrFail($id)->update($data);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->withErrors(['message' => '儲存失敗']);
        }

        $route_name = 'category';
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
}
