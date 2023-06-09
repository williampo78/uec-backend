<?php

namespace App\Http\Controllers;

use App\Models\TertiaryCategory;
use App\Services\TertiaryCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TertiaryCategoryController extends Controller
{
    private $tertiaryCategoryService;

    public function __construct(
        TertiaryCategoryService $tertiaryCategoryService
    ) {
        $this->tertiaryCategoryService = $tertiaryCategoryService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @Author: Eric
     * @DateTime: 2022/1/19 上午 09:27
     */
    public function index()
    {
        $params = [];
        $params['tertiaryCategories'] = $this->tertiaryCategoryService->getIndex();

        return view('backend.tertiary_category.list', $params);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @Author: Eric
     * @DateTime: 2022/1/19 上午 10:02
     */
    public function create()
    {
        $params = [];
        $params['parentCategories'] = $this->tertiaryCategoryService->getPrimaryCategoryAndCategory();

        return view('backend.tertiary_category.add', $params);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @Author: Eric
     * @DateTime: 2022/1/19 上午 10:02
     */
    public function store(Request $request)
    {
        try {
            TertiaryCategory::create([
                'agent_id' => Auth::user()->agent_id,
                'category_id' => $request->category_id,
                'number' => $request->number,
                'name' => $request->name,
                'active' => 1,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->withErrors(['message' => '儲存失敗']);
        }

        $route_name = 'tertiary_category';
        $act = 'add';

        return view('backend.success', compact('route_name', 'act'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @Author: Eric
     * @DateTime: 2022/1/19 上午 09:24
     */
    public function edit(int $id)
    {
        $tertiaryCategory = TertiaryCategory::findOrFail($id);

        $params = [];
        $params['tertiaryCategory'] = $tertiaryCategory;
        $params['parentCategories'] = $this->tertiaryCategoryService->getPrimaryCategoryAndCategory();

        return view('backend.tertiary_category.edit', $params);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @Author: Eric
     * @DateTime: 2022/1/19 上午 09:26
     */
    public function update(int $id, Request $request)
    {
        try {
            $tertiaryCategory = TertiaryCategory::findOrFail($id);

            $tertiaryCategory->update([
                'category_id' => $request->category_id,
                'number' => $request->number,
                'name' => $request->name,
                'active' => 1,
                'updated_by' => Auth::user()->id,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->withErrors(['message' => '儲存失敗']);
        }

        $route_name = 'tertiary_category';
        $act = 'upd';
        return view('backend.success', compact('route_name', 'act'));
    }
}
