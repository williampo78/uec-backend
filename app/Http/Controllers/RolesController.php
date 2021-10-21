<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RolesPermissionService;
use App\Services\UniversalService;

class RolesController extends Controller
{
    private $rolesPermission;
    private $universalService;

    public function __construct(RolesPermissionService $rolesPermission, UniversalService $universalService)
    {
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
        $data['role'] = ($getData ? $this->rolesPermission->getRoles($getData) : []);
        $data['user'] = $this->universalService->getUser();
        $data['getData'] = $getData;
        return view('Backend.Roles.list', compact('data'));
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
        return view('Backend.Roles.add', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
