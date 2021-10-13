<?php

namespace App\Http\Controllers;

use App\Services\SupplierService;
use App\Services\SupplierTypeService;
use Illuminate\Http\Request;

class SupplierControllers extends Controller
{
    private $supplierService;
    private $supplierTypeService ;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(SupplierService $supplierService,SupplierTypeService $supplierTypeService)
    {
        $this->supplierService = $supplierService;
        $this->supplierTypeService = $supplierTypeService ;
    }
    public function index()
    {
        $reslut = [] ;
        $reslut['supplier'] = $this->supplierService->getSupplier() ;
        return view('Backend.Supplier.list',$reslut);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $reslut['SupplierType'] = $this->supplierTypeService->getSupplierType();
        return view('Backend.Supplier.input',$reslut);
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
        
        $reslut = $this->supplierService->addSupplier($input);
    
        return redirect(route('supplier'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $supplier = $this->supplierService->showSupplier($id)->first();
        // return view('Backend.Supplier.input',$supplier);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $reslut = [] ; 
        $reslut['Supplier'] = $this->supplierService->showSupplier($id);
        $reslut['SupplierType'] = $this->supplierTypeService->getSupplierType();
        return view('Backend.Supplier.input',$reslut);
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
        $input =  $request->input();
        $result = $this->supplierService->updateSupplier($input, $id);

        return redirect(route('supplier'));
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
