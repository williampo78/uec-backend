<?php

namespace App\Http\Controllers;

use App\Models\PrimaryCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrimaryCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {


    }
    public function index()
    {
        $data = PrimaryCategory::all();

        return view('Backend.PrimaryCategory.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Backend.PrimaryCategory.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $route_name = 'primary_category';
        $act = 'add';
        $data = $request->except('_token');
        $data['agent_id'] = Auth::user()->agent_id;
        $data['created_by'] = Auth::user()->id;
        $data['created_at'] = Carbon::now();

        $rs = PrimaryCategory::insert($data);

        return view('backend.success' , compact('route_name','act'));
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
        $data = PrimaryCategory::find($id);

        return view('Backend.PrimaryCategory.upd', compact('data'));
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
        $data = $request->except('_token' , '_method');
        $data['updated_by'] = Auth::user()->id;

        PrimaryCategory::where('id' ,$id)->update($data);
        $route_name = 'primary_category';
        $act = 'upd';
        return view('backend.success', compact('route_name' , 'act'));
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
