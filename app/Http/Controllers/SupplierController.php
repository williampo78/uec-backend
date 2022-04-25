<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ContactService;
use App\Services\SupplierService;
use App\Services\SupplierTypeService;
use App\Services\LookupValuesVService;

class SupplierController extends Controller
{
    private $supplierService;
    private $supplierTypeService;
    private $contactService;
    private $lookupValuesVService;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(
        SupplierService $supplierService,
        SupplierTypeService $supplierTypeService,
        ContactService $contactService,
        LookupValuesVService $lookupValuesVService
    ) {
        $this->supplierService = $supplierService;
        $this->supplierTypeService = $supplierTypeService;
        $this->contactService = $contactService;
        $this->lookupValuesVService = $lookupValuesVService;
    }

    public function index(Request $request)
    {
        $queryData = $request->query();

        $result = [];
        // 供應商類別
        $result['supplierTypes'] = $this->supplierTypeService->getSupplierTypes();
        // 狀態
        $result['activeOptions'] = config('uec.active_options');
        $result['suppliers'] = $this->supplierService->getTableList($queryData);

        return view('backend.supplier.index', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 供應商類別
        $result['supplierTypes'] = $this->supplierTypeService->getSupplierTypes();
        // 付款條件
        $result['paymentTerms'] = $this->lookupValuesVService->getLookupValuesVsForBackend([
            'type_code' => 'PAYMENT_TERMS',
        ]);
        // 稅別
        $result['taxTypeOptions'] = config('uec.tax_type_options');
        // 狀態
        $result['activeOptions'] = config('uec.active_options');
        // 合約狀態
        $result['supplierContractStatusCodeOptions'] = config('uec.supplier_contract_status_code_options');
        // 供應商合約條款
        $result['supplierContractTerms'] = $this->lookupValuesVService->getLookupValuesVsForBackend([
            'type_code' => 'SUPPLIER_CONTRACT_TERMS',
        ]);

        return view('backend.supplier.create', $result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputData = $request->input();

        if (!$this->supplierService->createSupplier($inputData)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $result = [
            'route_name' => 'supplier',
            'act' => 'add',
        ];

        return view('backend.success', $result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = [];
        $result['Supplier'] = $this->supplierService->showSupplier($id);
        $result['SupplierType'] = $this->supplierTypeService->getSupplierType();
        $result['Contact'] = $this->contactService->getContact('Supplier', $id);
        $result['getPaymentTerms'] = $this->supplierService->getPaymentTerms();
        $result['readonly'] = 1;

        return view('backend.supplier.input', $result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // 供應商類別
        $result['supplierTypes'] = $this->supplierTypeService->getSupplierTypes();
        // 付款條件
        $result['paymentTerms'] = $this->lookupValuesVService->getLookupValuesVsForBackend([
            'type_code' => 'PAYMENT_TERMS',
        ]);
        // 稅別
        $result['taxTypeOptions'] = config('uec.tax_type_options');
        // 狀態
        $result['activeOptions'] = config('uec.active_options');
        // 合約狀態
        $result['supplierContractStatusCodeOptions'] = config('uec.supplier_contract_status_code_options');
        // 供應商合約條款
        $result['supplierContractTerms'] = $this->lookupValuesVService->getLookupValuesVsForBackend([
            'type_code' => 'SUPPLIER_CONTRACT_TERMS',
        ]);
        // 供應商
        $result['supplier'] = $this->supplierService->getSupplierById($id);

        return view('backend.supplier.edit', $result);
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
        $inputData = $request->input();
        $inputData['id'] = $id;

        if (!$this->supplierService->updateSupplier($inputData)) {
            return back()->withErrors(['message' => '儲存失敗']);
        }

        $result = [
            'route_name' => 'supplier',
            'act' => 'upd',
        ];

        return view('backend.success', $result);
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

    public function displayNumberExists(Request $request)
    {
        if ($this->supplierService->displayNumberExists($request->display_number, $request->supplier_id)) {
            return response()->json([
                'result' => true,
            ]);
        }

        return response()->json([
            'result' => false,
        ]);
    }
}
