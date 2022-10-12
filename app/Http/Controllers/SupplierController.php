<?php

namespace App\Http\Controllers;

use App\Http\Resources\Supplier\Index;
use App\Services\LookupValuesVService;
use App\Services\SupplierService;
use App\Services\SupplierTypeService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    private $supplierService;
    private $supplierTypeService;
    private $lookupValuesVService;

    public function __construct(
        SupplierService $supplierService,
        SupplierTypeService $supplierTypeService,
        LookupValuesVService $lookupValuesVService
    ) {
        $this->supplierService = $supplierService;
        $this->supplierTypeService = $supplierTypeService;
        $this->lookupValuesVService = $lookupValuesVService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $requestPayload = $request->only([
            'supplier_type_id',
            'display_number_or_name',
            'company_number',
            'active',
        ]);

        $suppliers = $this->supplierService->getList($requestPayload);
        $responsePayload = [
            'supplier_types' => $this->supplierTypeService->getSupplierTypes(),
            'auth' => $request->share_role_auth,
            'suppliers' => Index\SupplierResource::collection($suppliers),
        ];

        return view('backend.supplier.list', [
            'payload' => $responsePayload,
        ]);
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
        // 供應商類別
        $result['supplierTypes'] = $this->supplierTypeService->getSupplierTypes();
        // 付款條件
        $result['paymentTerms'] = $this->lookupValuesVService->getLookupValuesVsForBackend([
            'type_code' => 'PAYMENT_TERMS',
        ]);
        // 供應商合約條款
        $result['supplierContractTerms'] = $this->lookupValuesVService->getLookupValuesVsForBackend([
            'type_code' => 'SUPPLIER_CONTRACT_TERMS',
        ]);
        // 供應商
        $result['supplier'] = $this->supplierService->getSupplierById($id);

        return view('backend.supplier.show', $result);
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

        if (!$this->supplierService->updateSupplier($id, $inputData)) {
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
