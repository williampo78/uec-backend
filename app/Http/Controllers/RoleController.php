<?php

namespace App\Http\Controllers;

use App\Services\RolesPermissionService;
use App\Services\UniversalService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $rolesPermission;
    private $universalService;

    public function __construct(
        RolesPermissionService $rolesPermission,
        UniversalService $universalService
    ) {
        $this->rolesPermission = $rolesPermission;
        $this->universalService = $universalService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $getData = $request->all();
        $data['roles'] = ($getData ? $this->rolesPermission->getRoles($getData) : []);
        $data['user'] = $this->universalService->getUser();

        return view('backend.role.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['permission'] = $this->rolesPermission->getPermission();
        $data['permissionDetail'] = $this->rolesPermission->getPermissionDetail();

        return view('backend.role.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->input();
        unset($input['_token']);
        $act = 'add';
        $route_name = 'roles';
        $this->rolesPermission->addRole($input, $act);

        return view('backend.success', compact('route_name', 'act'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['permission'] = $this->rolesPermission->getPermission();
        $data['permissionDetail'] = $this->rolesPermission->getPermissionDetail();
        $data['role'] = $this->rolesPermission->showRole($id);
        $data['rolePermission'] = $this->rolesPermission->getRolePermission($id);

        return view('backend.role.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['permission'] = $this->rolesPermission->getPermission();
        $data['permissionDetail'] = $this->rolesPermission->getPermissionDetail();
        $data['role'] = $this->rolesPermission->showRole($id);
        $data['rolePermission'] = $this->rolesPermission->getRolePermission($id);

        return view('backend.role.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->input();
        unset($input['_token']);
        $act = 'upd';
        $route_name = 'roles';
        $input['id'] = $id;
        $this->rolesPermission->addRole($input, $act);
        
        return view('backend.success', compact('route_name', 'act'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
