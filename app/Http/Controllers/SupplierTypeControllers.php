<?php

namespace App\Http\Controllers;

use App\Services\SupplierTypeService;
use Illuminate\Http\Request;

class SupplierTypeControllers extends Controller
{
    protected $SupplierTypeService;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(SupplierTypeService $SupplierTypeService)
    {
        $this->SupplierTypeService = $SupplierTypeService;
    }
    public function index(Request $request)
    {
        $result = [];
        $result['SupplierTypeService'] = $this->SupplierTypeService->Get_All($request);
        return view('Backend.SupplierType.list', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Backend.SupplierType.input');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //未來要新增驗證
        $inputData = $request->validate([
            'code' => 'required',
            'name' => 'required',
        ]);
        $this->SupplierTypeService->Add($inputData);

        return redirect(route('supplier_type'));
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
        $result = [];
        $result['ShowData'] = $this->SupplierTypeService->Get($id);
        return view('Backend.SupplierType.input', $result);
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
        $inputData = $request->validate([
            'code' => 'required',
            'name' => 'required',
        ]);

        $result = $this->SupplierTypeService->Update($inputData, $id);

        return redirect(route('supplier_type'));
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
