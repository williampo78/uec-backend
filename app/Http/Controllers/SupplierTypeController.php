<?php

namespace App\Http\Controllers;

use App\Services\SupplierTypeService;
use Illuminate\Http\Request;

class SupplierTypeController extends Controller
{
    protected $supplierTypeService;

    public function __construct(SupplierTypeService $supplierTypeService)
    {
        $this->supplierTypeService = $supplierTypeService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $result = [];
        $result['SupplierTypeService'] = $this->supplierTypeService->getSupplierType($request);
        return view('backend.supplier_type.list', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.supplier_type.input');
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
        $result = $this->supplierTypeService->Add($inputData);
        $result['route_name'] = 'supplier_type';
        if ($result['status']) {
            $result['act'] = 'add';
            return view('backend.success', $result);
        } else {
            $result['message'] = '新增時發生未預期的錯誤';
            return view('backend.error', $result);
        }
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
        $result['ShowData'] = $this->supplierTypeService->Get($id);
        return view('backend.supplier_type.input', $result);
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

        $result = $this->supplierTypeService->Update($inputData, $id);
        $result['route_name'] = 'supplier_type';
        if ($result['status']) {
            $result['act'] = 'upd';
            return view('backend.success', $result);
        } else {
            $result['message'] = '新增時發生未預期的錯誤';
            return view('backend.error', $result);
        }
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
