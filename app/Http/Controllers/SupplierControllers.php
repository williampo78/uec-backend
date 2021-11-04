<?php

namespace App\Http\Controllers;

use App\Services\ContactService;
use App\Services\SupplierService;
use App\Services\SupplierTypeService;
use Illuminate\Http\Request;

class SupplierControllers extends Controller
{
    private $supplierService;
    private $supplierTypeService;
    private $contactService ;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(
        SupplierService $supplierService,
        SupplierTypeService $supplierTypeService,
        ContactService $contactService) {
        $this->supplierService = $supplierService;
        $this->supplierTypeService = $supplierTypeService;
        $this->contactService = $contactService;
    }
    public function index()
    {
        $result = [];
        $result['supplier'] = $this->supplierService->getSupplier();
        return view('Backend.Supplier.list', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $result['SupplierType'] = $this->supplierTypeService->getSupplierType();
        return view('Backend.Supplier.input', $result);
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

        $result = $this->supplierService->addSupplier($input);
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
        // echo $id ;//
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
        $result = [];
        $result['Supplier'] = $this->supplierService->showSupplier($id);
        $result['SupplierType'] = $this->supplierTypeService->getSupplierType();
        $result['Contact'] = $this->contactService->getContact('Supplier',$id);
        return view('Backend.Supplier.input', $result);
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
        $contact_json = $request->input('contact_json') ; 
        $input = $request->input();
        unset($input['contact_json']) ;
        $this->contactService->createContact('tablename' , $contact_json) ; 
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
