<?php

namespace App\Http\Controllers;

use App\Services\RequisitionsPurchaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Type;

class RequisitionsPurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $requisitionsPurchaseService;

    public function __construct(RequisitionsPurchaseService $requisitionsPurchaseService)
    {
        $this->requisitionsPurchaseService = $requisitionsPurchaseService;
    }

    public function index()
    {
        $now = Carbon::now()->subDays()->toArray();
        $params['active'] = 0;
        $data = $this->requisitionsPurchaseService->getRequisitionsPurchase($params);

        return view('Backend.RequisitionsPurchase.list' , compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // dd('TEST') ; 
        // $agent_id = Auth::user()->agent_id;
        // $primary_category = PrimaryCategory::where('agent_id' , $agent_id)->get();
        
        return view('Backend.RequisitionsPurchase.input' );
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

    public function ajax(Request $request){
        $rs = $request->all();

        if ($rs['get_type']==='requisitions_purchase'){
            $data = $this->requisitionsPurchaseService->getAjaxRequisitionsPurchase($rs['id']);
            echo "OK@@".json_encode($data);
        }elseif($rs['get_type']==='requisitions_purchase_detail'){
            $data = $this->requisitionsPurchaseService->getAjaxRequisitionsPurchaseDetail($rs['id']);
            echo "OK@@".json_encode($data);
        }
    }
}
