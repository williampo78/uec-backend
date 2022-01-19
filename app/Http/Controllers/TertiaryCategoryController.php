<?php

namespace App\Http\Controllers;

use App\Models\TertiaryCategories;
use App\Services\RoleService;
use App\Services\TertiaryCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class TertiaryCategoryController extends Controller
{
    private $role_service;
    private $tertiaryCategoryService;

    public function __construct(
        TertiaryCategoryService $tertiaryCategoryService,
        RoleService $role_service
    )
    {
        $this->tertiaryCategoryService = $tertiaryCategoryService;
        $this->role_service = $role_service;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @Author: Eric
     * @DateTime: 2022/1/19 上午 10:02
     */
    public function create()
    {
        //無權限
        if ($this->role_service->getOtherRoles()['auth_create'] == false) {
            App::abort(403);
        }

        $params = [];
        $params['parentCategories'] = $this->tertiaryCategoryService->getPrimaryCategoryAndCategory();

        return view('Backend.TertiaryCategory.add', $params);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @Author: Eric
     * @DateTime: 2022/1/19 上午 10:02
     */
    public function store(Request $request)
    {
        //無權限
        if ($this->role_service->getOtherRoles()['auth_create'] == false) {
            App::abort(403);
        }

        TertiaryCategories::create([
            'agent_id' => Auth::user()->agent_id,
            'category_id' => $request->category_id,
            'number' => $request->number,
            'name' => $request->name,
            'active' => 1,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        $route_name = 'tertiary_category';
        $act = 'add';

        return view('Backend.success', compact('route_name', 'act'));
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

        return view('Backend.TertiaryCategory.list', $params);
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @Author: Eric
     * @DateTime: 2022/1/19 上午 09:24
     */
    public function edit(int $id)
    {
        //無權限
        if ($this->role_service->getOtherRoles()['auth_update'] == false) {
            App::abort(403);
        }

        $tertiaryCategory = TertiaryCategories::findOrFail($id);

        $params = [];
        $params['tertiaryCategory'] = $tertiaryCategory;
        $params['parentCategories'] = $this->tertiaryCategoryService->getPrimaryCategoryAndCategory();

        return view('Backend.TertiaryCategory.edit', $params);
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
        if ($this->role_service->getOtherRoles()['auth_update'] == false) {
            App::abort(403);
        }

        $tertiaryCategory = TertiaryCategories::findOrFail($id);

        $tertiaryCategory->update([
            'category_id' => $request->category_id,
            'number' => $request->number,
            'name' => $request->name,
            'active' => 1,
            'updated_by' => Auth::user()->id,
        ]);

        $route_name = 'tertiary_category';
        $act = 'upd';
        return view('Backend.success', compact('route_name', 'act'));
    }
}
