<?php
namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\ItemService;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    private $itemService;
    private $categoryService;
    private $supplierService ;

    public function __construct(ItemService $itemService,
        CategoryService $categoryService,
        SupplierService $supplierService) {
        $this->itemService = $itemService;
        $this->categoryService = $categoryService;
        $this->supplierService = $supplierService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $item = $this->itemService->getItem()->get();
        $data['item'] = $item;
        return view('Backend.Item.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = $this->categoryService->getCategory(); //分類
        $supplier = $this->supplierService->getSuppliers(); //供應商
        $result['category'] = $category;
        $result['supplier'] = $supplier;
        return view('Backend.Item.input', $result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $readyInput = $this->InputValidate($request);
        if ($readyInput['status']) {
            $inserStatus = $this->itemService->insertData($readyInput['data']);
            if ($inserStatus['status']) { // 寫入DB成功
                $inserFile = $this->itemService->uploadImage($inserStatus['id'], $request->file(), 'create'); //圖片上傳功能
                return redirect('backend/item');
            }
        } else {
            return redirect('backend/item/create')
                ->withErrors($readyInput['data'])
                ->withInput();
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = $this->categoryService->getCategory(); //分類
        $supplier = $this->supplierService->getSuppliers(); //供應商
        $item = $this->itemService->getItem(1, $id)->first(); //返回array
        $itemPhoto = $this->itemService->getItemPhoto($id);
        return view('Backend.Item.input', compact('item', 'category', 'supplier','itemPhoto'));
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
        // dd($request->file()) ;

        $readyInput = $this->InputValidate($request);
        if ($readyInput['status']) {
            $updataStatus = $this->itemService->update($readyInput['data'], $id);
            // dd($updataStatus) ; exit ;
            if($updataStatus){
                $inserFile = $this->itemService->uploadImage($id, $request->file(), 'update' ); //圖片上傳功能
            }
            return redirect('backend/item');
        } else {
            return redirect('backend/item/create')
                ->withErrors($readyInput['data'])
                ->withInput();
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

    public function InputValidate($request)
    {
        $result = [];
        $validator = Validator::make($request->all(), [
            'category_id' => 'required', //分類
            'supplier_id' => 'required', //供應商a
            'number' => 'required', //編號(主分類編號+子分類編號+品項數)
            'brand' => 'required', //品牌
            'name' => 'required', //名稱
            'name_en' => 'required', //英文名稱
            'buy_price' => 'required', //進價
            'sell_price1' => 'required', //售價
            'large_unit' => 'required', //進貨單位
            'small_unit' => 'required', //出貨單位
            'stock_qty' => '', //當前庫存(以出貨單位計算)
            'spec' => 'required', //規格
            'public_number' => 'required', //衛署字號
            'old_number' => 'required', //舊系統編號
            'active' => 'required', //狀態
            'safe_stock' => 'required', //安全庫存量
            'live_times' => 'required', //有效期限
            'minimum_sales_qty' => 'required', //最低出貨量
            'fda_class' => 'required', //醫療器材分類分級(Class)
            'open_sales' => 'required', //是否可以公開販售(個人)
            'is_fee_item' => 'required', //是否為費用性品項
            'remark' => 'required', //備註
            'description' => 'required', //商品簡介(描述)
            'specification' => 'required', //商品規格
            'features' => 'required', // 商品特色(圖文)
        ]
            , [
                'category_id.required' => '分類不能為空', //分類
                'supplier_id.required' => '供應商不能為空', //供應商a
                'number.required' => '編號不能為空', //編號(主分類編號+子分類編號+品項數)
                'brand.required' => '品牌不能為空', //品牌
                'name.required' => '名稱不能為空', //名稱
                'name_en.required' => '英文名稱不能為空', //英文名稱
                'buy_price.required' => '進價不能為空', //進價
                'sell_price1.required' => '售價不能為空', //售價
                'large_unit.required' => '進貨單位不能為空', //進貨單位
                'small_unit.required' => '出貨單位不能為空', //出貨單位
                'stock_qty.required' => '當前庫存', //當前庫存(以出貨單位計算)
                'spec.required' => '規格不能為空', //規格
                'public_number.required' => '衛署字號不能為空', //衛署字號
                'old_number.required' => '舊系統編號不能為空', //舊系統編號
                'active.required' => '狀態不能為空', //狀態
                'safe_stock.required' => '安全庫存量不能為空', //安全庫存量
                'live_times.required' => '有效期限不能為空', //有效期限
                'fda_class.required' => '醫療器材分類分級不能為空', //醫療器材分類分級(Class)
                'open_sales.required' => '是否可以公開販售不能為空', //是否可以公開販售(個人)
                'minimum_sales_qty.required' => '最地出貨量不能為空',
                'is_fee_item.required' => '是否為費用性品項不能為空', //是否為費用性品項
                'remark.required' => '必填', //備註
                'description.required' => '描述必填', //商品簡介(描述)
                'specification.required' => '商品規格必填', //商品規格
                'features.required' => '商品特色必填', // 商品特色(圖文)
            ]
        );

        if ($validator->fails() == true) { //不符合驗證
            $result['status'] = false;
            $result['data'] = $validator->errors();
        } else { //符合
            $result['status'] = true;
            $result['data'] = $validator->validate();
        }
        return $result;
    }
    public function ajax_del_Item_photo(Request $request)
    {
        $input = [] ;
        $input['type'] = $request->input('num') == 1 ? 'item' : 'item_photo' ;
        $input['num']  = $request->input('num') ;
        $input['id']   = $request->input('id');
        $result = $this->itemService->delImage($input);
        if($result){
            return response()->json(['success'=>'true']);
        }else{
            return response()->json(['error'=>'false']);
        }
    }
}
