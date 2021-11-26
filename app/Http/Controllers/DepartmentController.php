<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\DepartmentService;

class DepartmentController extends Controller
{
    private $departmentService;
    public function __construct(DepartmentService $DepartmentService)
    {
        $this->departmentService = $DepartmentService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = [];
        $data['department'] = $this->departmentService->getDepartment();
        return view('Backend.Department.list', compact('data'), compact('data'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Backend.Department.input');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->input();
        unset($input['_token']);
        $input['created_by'] = Auth::user()->id;
        $input['created_at'] = Carbon::now();
        $this->departmentService->addDepartment($input);
        $act = 'add';
        $route_name = 'department';
        return view('Backend.success' , compact('route_name','act'));

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
        $data['department'] = $this->departmentService->showDepartment($id);
        return view('Backend.Department.input', $data);
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
        $input = $request->input();
        unset($input['_token']);
        unset($input['_method']);
        $input['updated_by'] = Auth::user()->id;
        $this->departmentService->updateDepartment($input, $id);
        $act = 'upd';
        $route_name = 'department';
        return view('Backend.success' , compact('route_name','act'));


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
