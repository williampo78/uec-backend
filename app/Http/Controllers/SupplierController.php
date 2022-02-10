<?php

namespace App\Http\Controllers;

use App\Services\ContactService;
use App\Services\SupplierService;
use App\Services\SupplierTypeService;
use Illuminate\Http\Request;

class SupplierController extends Controller
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
        $result['supplier'] = $this->supplierService->getSuppliers();
        return view('backend.supplier.list', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $result['SupplierType'] = $this->supplierTypeService->getSupplierType();
        $result['getPaymentTerms'] = $this->supplierService->getPaymentTerms();
        return view('backend.supplier.input', $result);
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
        $act = 'add';
        $route_name = 'supplier';
        $result = $this->supplierService->addSupplier($input);
        return view('backend.success', compact('route_name', 'act'));
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
        // return view('backend.supplier.input',$supplier);
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
        $result['getPaymentTerms'] = $this->supplierService->getPaymentTerms();

        return view('backend.supplier.input', $result);
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
        $act = 'upd';
        $route_name = 'supplier';
        return view('backend.success', compact('route_name', 'act'));
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
        $in = $request->input() ;
        switch ($in['type']) {
            case 'checkDisplayNumber':
                $result = $this->supplierService->checkDisplayNumber($in['display_number']);
                break;
            default:
                # code...
                break;
        }
        return response()->json([
            'req' => $request->input(),
            'result' =>  $result,
        ]);
    }
}
