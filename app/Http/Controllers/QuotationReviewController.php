<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class QuotationReviewController extends Controller
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
        dd(Auth::user());
        $data['user_name'] = Auth::user()->name;
        return view('Backend.QuotationReview.list' , compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $agent_id = Auth::user()->agent_id;
        $primary_category = PrimaryCategory::where('agent_id' , $agent_id)->get();

        return view('Backend.Category.add' , compact('primary_category'));
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
        $data = $request->except('_token');
        $data['agent_id'] = Auth::user()->agent_id;
        $data['created_by'] = Auth::user()->id;
        $data['created_at'] = Carbon::now();

        $rs = Category::insert($data);

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
        $data = Category::find($id);
        $primary_category_list = $this->categoryService->getPrimaryCategoryForList();

        return view('Backend.PrimaryCategory.upd', compact('data' , 'primary_category_list'));
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
        $data['updated_at'] = Carbon::now();

        Category::where('id' ,$id)->update($data);
        $route_name = 'category';
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
