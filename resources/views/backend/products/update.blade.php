@extends('backend.layouts.master')
@section('title', '商品主檔 - 新增基本資訊')
@section('content')
    <style>
        .no-pa {
            padding: 0px;
        }

        .ondragover {
            background: #b7e0fb !important;
            transition: background-color 0.5s;
            /* background: #ce1f59 !important; */
        }

        .elements-box>tr>td>* {
            pointer-events: none;
        }

        .img-box {
            height: 160px;
            /*can be anything*/
            width: 160px;
            /*can be anything*/
            position: relative;
            background-color: rgb(90, 86, 86);
            border: 1px solid black;
        }

        .img-box img {
            max-height: 100%;
            max-width: 100%;
            width: auto;
            height: auto;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }

        .theme-color {
            color: #138cde;
        }

        .sysinfo-title {
            margin-bottom: 10px;
            font-weight: bold;
        }

        .sysinfo {
            position: fixed;
            top: 15vh;
            right: 20px;
            z-index: 20;
        }

        .sysinfo ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .sysinfo-content {
            margin: 0;
        }

        .sysinfo-li {
            padding: 10px 15px;
            border-left: solid 4px #aaaaaa;
            color: #aaaaaa;
            line-height: 1.3;
            cursor: pointer;
        }

        .sysinfo-activie {
            color: #138cde;
            border-left: solid 4px #138cde;
        }

        .text-overflow-p {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .form-group {
            margin: 0 0 15px 0 !important;
        }
    </style>
    <div class="sysinfo">
        <div class="sysinfo-title theme-color">基本檔</div>
        <div class="sysinfo-content">
            <ul>
                <a href="#page-1">
                    <li class="sysinfo-li sysinfo-activie" id="click-page-1">
                        基本資料
                    </li>
                </a>
                <a href="#page-2">
                    <li class="sysinfo-li" id="click-page-2">
                        規格
                    </li>
                </a>
                {{-- <li></li> --}}
            </ul>
        </div>
    </div>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-list"></i> 商品主檔 - 編輯基本資訊</h1>
            </div>
        </div>
        <div class="panel panel-default">
            <form class="form-horizontal" role="form" id="new-form" method="POST"
                action="{{ route('products.update', $products->id) }}" enctype="multipart/form-data"
                novalidaten="ovalidate">
                @csrf
                @method('PUT')
                <div class="panel-heading">請輸入下列欄位資料</div>
                <div class="panel-body" id="category_hierarchy_content_input">
                    <div id="page-1">
                        <input name="id" value="{{ $products->id }}" style="display: none">
                        <input type="hidden" id="edit_readonly" value="{{ $products->edit_readonly }}">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">供應商<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control supplier_id" name="supplier_id"
                                            {{ $products->edit_readonly == '1' ? 'disabled' : '' }}>
                                            <option value=""></option>
                                            @foreach ($supplier as $val)
                                                <option value="{{ $val->id }}"
                                                    {{ $products->supplier_id == $val->id ? 'selected' : '' }}>
                                                    {{ $val->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label ">商品序號</label><span class="text-red">*</span>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="product_no" value="{{ $products->product_no }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">庫存類型</label><span class="text-red">*</span>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="stock_type" value="A"
                                                {{ $products->stock_type == 'A' ? 'checked' : 'disabled' }}>
                                            買斷
                                            [A]
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="stock_type" value="B"
                                                {{ $products->stock_type == 'B' ? 'checked' : 'disabled' }}>
                                            寄售
                                            [B]
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="stock_type" value="T"
                                                {{ $products->stock_type == 'T' ? 'checked' : 'disabled' }}>
                                            轉單[T]
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">商品名稱<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="product_name" name="product_name"
                                            value="{{ $products->product_name }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">課稅別<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        @if ($products->tax_type == 'TAXABLE')
                                            <input class="form-control" value="應稅(5%)" readonly>
                                        @elseif($products->tax_type == 'NON_TAXABLE')
                                            <input class="form-control" value="免稅" readonly>
                                        @endif
                                        <input type="hidden" class="form-control" id="tax_type" name="tax_type"
                                            value="{{ $products->tax_type }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">POS分類<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control category_id" name="category_id"
                                            {{ $products->edit_readonly == '1' ? 'disabled' : '' }}>
                                            <option value=""></option>
                                            @foreach ($pos as $key => $val)
                                                <option value="{{ $val->id }}"
                                                    {{ $products->category_id == $val->id ? 'selected' : '' }}>
                                                    {{ $val->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">品牌<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control brand_id" name="brand_id" id="brand_id">
                                            <option value=""></option>
                                            @foreach ($brands as $val)
                                                <option value="{{ $val->id }}"
                                                    {{ $products->brand_id == $val->id ? 'selected' : '' }}>
                                                    {{ $val->brand_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">商品型號</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="model" value=" {{ $products->model }}"
                                            {{ $products->edit_readonly == '1' ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">商品通路<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="radio-inline">
                                            <input type="radio" name="selling_channel" value="EC"
                                                {{ $products->selling_channel == 'EC' ? 'checked' : '' }}
                                                {{ $products->edit_readonly == '1' && $products->selling_channel !== 'EC' ? 'disabled' : '' }}>
                                            網路獨賣
                                        </label>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="radio-inline">

                                            <input type="radio" name="selling_channel" value="WHOLE"
                                                {{ $products->selling_channel == 'WHOLE' ? 'checked' : '' }}
                                                {{ $products->edit_readonly == '1' && $products->selling_channel !== 'WHOLE' ? 'disabled' : '' }}>
                                            全通路
                                        </label>

                                    </div>
                                    <div class="col-sm-2">
                                        <label class="radio-inline">

                                            <input type="radio" name="selling_channel" value="STORE"
                                                {{ $products->selling_channel == 'STORE' ? 'checked' : '' }}
                                                {{ $products->edit_readonly == '1' && $products->selling_channel !== 'STORE' ? 'disabled' : '' }}>
                                            門市限定
                                        </label>

                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">廠商料號</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="supplier_product_no" value="{{ $products->supplier_product_no }}"
                                            {{ $products->edit_readonly == '1' ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">配送溫層<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="radio-inline">
                                            <input type="radio" name="lgst_temperature" value="NORMAL"
                                                {{ $products->lgst_temperature == 'NORMAL' ? 'checked' : '' }}>
                                            常溫
                                        </label>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="radio-inline">
                                            <input type="radio" name="lgst_temperature" value="CHILLED"
                                                {{ $products->lgst_temperature == 'CHILLED' ? 'checked' : '' }}>
                                            冷藏
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">存放溫層<span class="text-red">*</span></label>
                                    </div>
                                    @if ($products['stock_type'] == 'B' or $products['stock_type'] == 'A')
                                    <div class="col-sm-2">
                                        <input class="form-control" value="{{ $products['StorageTemperatureName'] }}" readonly>
                                    </div>
                                    <input type="hidden" id="storage_temperature" name="storage_temperature" value="{{ $products['storage_temperature'] }}">
                                    @else
                                    <div class="col-sm-2">
                                        <label class="radio-inline">
                                            <input type="radio" name="storage_temperature" value="NORMAL"
                                                {{ $products->storage_temperature == 'NORMAL' ? 'checked' : '' }}>
                                            常溫
                                        </label>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="radio-inline">
                                            <input type="radio" name="storage_temperature" value="AIR"
                                                {{ $products->storage_temperature == 'AIR' ? 'checked' : '' }}>
                                            空調
                                        </label>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="radio-inline">
                                            <input type="radio" name="storage_temperature" value="CHILLED"
                                                {{ $products->storage_temperature == 'CHILLED' ? 'checked' : '' }}>
                                            冷藏
                                        </label>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">配送方式<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <label class="radio-inline">
                                            <input type="radio" name="lgst_method" value="HOME"
                                                {{ $products->lgst_method == 'HOME' ? 'checked' : '' }}
                                                {{ $products->edit_readonly == '1' && $products->lgst_method !== 'HOME' ? 'disabled' : '' }}>
                                            宅配
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">商品交期<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="delivery_type" value="IN_STOCK"
                                                {{ $products->delivery_type == 'IN_STOCK' ? 'checked' : 'disabled' }}
                                                {{ $products->edit_readonly == '1' && $products->delivery_type !== 'IN_STOCK' ? 'disabled' : '' }}>
                                            現貨
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">單位<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="uom" value="{{ $products->uom }}"
                                            {{ $products->edit_readonly == '1' ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">最小採購量</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="min_purchase_qty" type="number"
                                            min="0" value="{{ $products->min_purchase_qty }}"
                                            {{ $products->edit_readonly == '1' ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">效期控管<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="has_expiry_date" value="0"
                                                {{ $products->has_expiry_date == '0' ? 'checked' : 'disabled' }}> 無
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="has_expiry_date" value="1"
                                                {{ $products->has_expiry_date == '1' ? 'checked' : 'disabled' }}>有，天數
                                        </label>
                                    </div>
                                    {{-- 效期控管的天數 --}}
                                    <div class="col-sm-3">
                                        <input class="form-control" name="expiry_days"
                                            value=" {{ $products->expiry_days }}"
                                            {{ $products->has_expiry_date == '1' ? 'checked' : '' }} readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">允收期(天)</label>
                                    </div>
                                    <div class="col-sm-3">
                                        <input type="number" class="form-control" id="expiry_receiving_days"
                                            name="expiry_receiving_days" min="0"
                                            value="{{ $products->expiry_receiving_days }}"
                                            {{ $products->edit_readonly == '1' ? '' : 'readonly' }}>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="control-label">
                                            <a href="#" data-toggle="modal"
                                                data-target="#model_requisitions_log">請採記錄</a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">商品類型<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-3 ">
                                        <label class="radio-inline">
                                            <input type="radio" name="product_type" value="N"
                                                {{ $products->product_type == 'N' ? 'checked' : 'disabled' }}>
                                            一般品
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="product_type" value="G"
                                                {{ $products->product_type == 'G' ? 'checked' : 'disabled' }}>
                                            贈品
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="product_type" value="A"
                                                {{ $products->product_type == 'A' ? 'checked' : 'disabled' }}>
                                            加購品
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">停售<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="radio-inline">
                                            <input type="radio" name="is_discontinued" id="inlineRadio1"
                                                value="1" {{ $products->is_discontinued == '1' ? 'checked' : '' }}
                                                {{ $products->edit_readonly == '1' && $products->is_discontinued != '1' ? 'disabled' : '' }}>
                                            是
                                        </label>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="radio-inline">
                                            <input type="radio" name="is_discontinued" id="inlineRadio3"
                                                value="0" {{ $products->is_discontinued == '0' ? 'checked' : '' }}
                                                {{ $products->edit_readonly == '1' && $products->is_discontinued != '0' ? 'disabled' : '' }}>
                                            否
                                        </label>
                                    </div>
                                    @if ($products->edit_readonly == 1)
                                        <div class="col-sm-6">
                                            <label class="control-label" style="color:red;">商品若要停售，須先下架，不可直接修改</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">材積(公分) <span class="text-red red-star">*</span></label>
                                    </div>
                                    <div class="col-sm-1 no-pa">
                                        <label class="control-label">長</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input class="form-control" name="length" type="number" min="0"
                                            value="{{ $products->length }}"
                                            {{ $products->edit_readonly == '1' ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-sm-1 no-pa">
                                        <label class="control-label">寬</label>
                                    </div>
                                    <div class="col-sm-2 no-pa">
                                        <input class="form-control" name="width" type="number" min="0"
                                            value="{{ $products->width }}"
                                            {{ $products->edit_readonly == '1' ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-sm-1 no-pa">
                                        <label class="control-label">高</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input class="form-control" name="height" type="number" min="0"
                                            value="{{ $products->height }}"
                                            {{ $products->edit_readonly == '1' ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">重量(公克)<span class="text-red red-star">*</span></label>
                                    </div>
                                    <div class="col-sm-3">
                                        <input class="form-control" name="weight" type="number" min="0"
                                            value="{{ $products->weight }}"
                                            {{ $products->edit_readonly == '1' ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">市價(含稅)<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="list_price" type="number" min="0"
                                            value="{{ $products->list_price }}"
                                            {{ $products->edit_readonly == '1' ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">售價(含稅)<span class="text-red">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="selling_price" type="number" min="0"
                                            value="{{ $products->selling_price }}"
                                            {{ $products->edit_readonly == '1' ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">付款方式</label>
                                    </div>
                                    <div class="row">
                                        @foreach ($payment_method_options as $key => $val)
                                            <div class="col-sm-6">
                                                <label class="radio-inline ps-3">
                                                    @if ($payment_method_options_lock[$key])
                                                        <input class="payment_method" type="checkbox" name="payment_method[]" value="{{ $key }}" checked onclick="return false">
                                                        {{ $val }}
                                                    @else
                                                        <input class="payment_method" type="checkbox" name="payment_method[]"
                                                            value="{{ $key }}"
                                                            {{ in_array($key, $payment_method) ? 'checked' : '' }}>
                                                        {{ $val }}
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">成本(含稅)</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" value="{{ $products->item_cost }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">毛利(%)</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input class="form-control" value="{{ $products->gross_margin }}" readonly>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="control-label">
                                            <a href="#" data-toggle="modal"
                                                data-target="#model_promotional">促銷活動</a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">採購人員</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="keyword" id="keyword"
                                            value="{{ $finallyOrderSupplier }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">轉單審核人員</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="keyword" id="keyword" value=""
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">建檔人員</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="keyword" value="{{ $products->created_name }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">建檔時間</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" id="keyword" value="{{ $products->created_at }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">修改人員</label>
                                    </div>
                                    <div class="col-sm-7">
                                        <input class="form-control" id="keyword" value="{{ $products->updated_name }}"
                                            readonly>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="control-label">
                                            {{-- <a href="#">修改紀錄</a> --}}
                                            <a href="#" data-toggle="modal" data-target="#model_category">修改紀錄</a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 no-pa">
                                        <label class="control-label">修改時間</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="keyword" id="keyword"
                                            value="{{ $products->updated_at }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        {{-- 商品描述 START --}}
                        <div class="row">
                            <div class="col-sm-12 ">
                                <div class="form-group">
                                    <div class="col-sm-1 no-pa">
                                        <label class="control-label">商品簡述</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input class="form-control product_brief" name="product_brief_1"
                                            value="{{ $products->product_brief_1 }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1 ">
                                    </div>
                                    <div class="col-sm-10">
                                        <input class="form-control product_brief" name="product_brief_2"
                                            value="{{ $products->product_brief_2 }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1 ">
                                    </div>
                                    <div class="col-sm-10">
                                        <input class="form-control product_brief" name="product_brief_3"
                                            value="{{ $products->product_brief_3 }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- 商品描述 END --}}
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1 no-pa">
                                        <label class="control-label">專利字號</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input class="form-control" name="patent_no" value="{{ $products->patent_no }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1 no-pa">
                                        <label class="control-label">保固期限<span class="text-red red-star">*</span></label>
                                    </div>
                                    <div class="col-sm-1">
                                        <label class="radio-inline">
                                            <input type="radio" name="is_with_warranty" value="0"
                                                {{ $products->is_with_warranty == '0' ? 'checked' : '' }}>
                                            無
                                        </label>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="radio-inline">
                                            <input type="radio" name="is_with_warranty" value="1"
                                                {{ $products->is_with_warranty == '1' ? 'checked' : '' }}>
                                            有保固，天數
                                        </label>
                                    </div>
                                    <div class="col-sm-1 ">
                                        <input class="form-control" id="warranty_days" name="warranty_days" min="0"
                                            value="{{ $products->warranty_days }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1 no-pa">
                                        <label class="control-label">保固範圍</label>
                                    </div>
                                    <div class="col-sm-11">
                                        <textarea class="form-control" rows="10" cols="10" name="warranty_scope">{{ $products->warranty_scope }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="ImageUploadBox">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-1 no-pa">
                                            <label class="control-label">商品圖檔<span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-sm-10">
                                            <p class="help-block">最多上傳15張，每張size不可超過1MB，副檔名須為JPG、JPEG、PNG</p>
                                            <p class="help-block">圖檔比例須為1:1，至少須為480 * 480</p>
                                            <input type="{{$share_type_file}}" style="margin-bottom: 15px;" @change="fileSelected"
                                                multiple>
                                            <input style="display: none" type="{{$share_type_file}}" :ref="'images_files'"
                                                name="filedata[]" multiple>
                                            <div style="display: flex; flex-wrap:wrap; gap:15px">
                                                <div style="max-width:180px;"v-for="(image, key) in images"
                                                    :key="key">
                                                    <div class="thumbnail" @dragstart="drag" @dragover='dragover'
                                                        @dragleave='dragleave' @drop="drop" :data-index="key"
                                                        :data-type="'image'" draggable="true"
                                                        style="pointer-events: auto;">
                                                        <div class="img-box" style="pointer-events: none;">
                                                            <img :ref="'image'">
                                                        </div>
                                                        <div class="caption" style="pointer-events: none;">
                                                            <p class="text-overflow-p">檔案名稱: @{{ image.name }}</p>
                                                            <p>檔案大小:@{{ image.sizeConvert }}</p>
                                                            <p>
                                                                排序: @{{ key + 1 }}
                                                                <button class="btn btn-danger pull-right btn-events-none"
                                                                    type="button" @click="delImages(key)"
                                                                    style="pointer-events: auto;">
                                                                    <i class="fa-solid fa-trash-can"></i>
                                                                </button>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <textarea style="display: none" name="imgJson" id="imgJson" cols="30" rows="10">@{{ images }}</textarea>
                                        <textarea style="display: none" name="readyDeletePhotosJson" id="readyDeletePhotos" cols="30" rows="10">@{{readyDeletePhotos}}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <hr>
                <div id="page-2">
                    <div style="overflow:auto" id="SkuComponent">
                        <textarea style="display: none;" name="SpecListJson" id="" cols="30" rows="10">@{{ SpecList }}</textarea>
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-1">
                                    <label class="control-label">商品規格<span class="text-red">*</span></label>
                                </div>
                                <div class="col-sm-11">
                                    <div class="col-sm-12" style="margin-bottom:20px">
                                        <div class="col-sm-2 ">
                                            <label class="radio-inline">
                                                <input type="radio" name="spec_dimension" value="0"
                                                    v-model="products.spec_dimension"
                                                    :disabled="products.spec_dimension !== 0">
                                                單規格
                                            </label>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="radio-inline">
                                                <input type="radio" name="spec_dimension" value="1"
                                                    v-model="products.spec_dimension"
                                                    :disabled="products.spec_dimension == 2">
                                                一維多規格
                                            </label>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="radio-inline">
                                                <input type="radio" name="spec_dimension" value="2"
                                                    v-model="products.spec_dimension">
                                                二維多規格
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row form-group col-sm-12">
                                        <div class="col-sm-6" v-if="products.spec_dimension >= 1">
                                            <div class="col-sm-2 ">
                                                <label class="control-label">規格一<span class="text-red">*</span></label>
                                            </div>
                                            <div class="col-sm-9">
                                                <div class="form-group">
                                                    <input class="form-control" type="text" name="spec_1"
                                                        id="spec_1" value="{{ $products->spec_1 }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6" v-if="products.spec_dimension == 2">
                                            <div class="col-sm-2 ">
                                                <label class="control-label">規格二<span class="text-red">*</span></label>
                                            </div>
                                            <div class="col-sm-9">
                                                <div class="form-group">
                                                    <input class="form-control" type="text" name="spec_2"
                                                        id="spec_2" value="{{ $products->spec_2 }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- 二維多規格 --}}
                                    <div class="row form-group col-sm-12">
                                        <div class="col-sm-6" v-if="products.spec_dimension >= 1">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <button class="btn btn-primary btn-sm" type="button"
                                                                @click="AddSpecToSkuList('1')">新增項目
                                                            </button>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(spec_1, spec_1_key) in SpecList.spec_1" @dragstart="drag"
                                                        @dragover='dragover' @dragleave='dragleave' @drop="drop"
                                                        draggable="true" :data-index="spec_1_key" :data-type="'spec_1'">
                                                        <td>
                                                            <div class="col-sm-1">
                                                                <label class="control-label">
                                                                    <i class="fa-solid fa-list fa-lg"></i>
                                                                </label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <div v-if="spec_1.old_spec">
                                                                    <div class="form-group">
                                                                        <input class="form-control spec_1_va"
                                                                            :name="'spec_1_va[' + spec_1_key + ']'"
                                                                            v-model="spec_1.name" data-va="spec_1_va">
                                                                    </div>
                                                                </div>
                                                                <div v-else>
                                                                    <div class="form-group">
                                                                        <input class="form-control spec_1_va"
                                                                            :name="'spec_1_va[' + spec_1_key + ']'"
                                                                            v-model="spec_1.name" data-va="spec_1_va">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <div v-if="spec_1.old_spec">
                                                                    <button class="btn btn-danger btn-sm" type="button"
                                                                        @click="DelSpecList(spec_1 ,'spec_1' ,spec_1_key)"
                                                                        disabled>刪除</button>
                                                                </div>
                                                                <div v-else>
                                                                    <button class="btn btn-danger btn-sm" type="button"
                                                                        @click="DelSpecList(spec_1 ,'spec_1' ,spec_1_key)">刪除</button>
                                                                </div>

                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-sm-6" v-if="products.spec_dimension == '2'">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <button class="btn btn-primary btn-sm" type="button"
                                                                @click="AddSpecToSkuList('2')">新增項目
                                                            </button>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(spec_2, spec_2_key) in SpecList.spec_2" @dragstart="drag"
                                                        @dragover='dragover' @dragleave='dragleave' @drop="drop"
                                                        draggable="true" :data-index="spec_2_key" :data-type="'spec_2'">
                                                        <td>
                                                            <div class="col-sm-1">
                                                                <label class="control-label">
                                                                    <i class="fa-solid fa-list fa-lg"></i>
                                                                </label>
                                                            </div>
                                                            <div class="col-sm-9">
                                                                <div v-if="spec_2.old_spec">
                                                                    <div class="form-group">
                                                                        <input class="form-control spec_2_va"
                                                                            :name="'spec_2_va[' + spec_2_key + ']'"
                                                                            v-model="spec_2.name" data-va="spec_2_va">
                                                                    </div>
                                                                </div>
                                                                <div v-else>
                                                                    <div class="form-group">
                                                                        <input class="form-control spec_2_va"
                                                                            :name="'spec_2_va[' + spec_2_key + ']'"
                                                                            v-model="spec_2.name" data-va="spec_2_va">
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <div v-if="spec_2.old_spec">
                                                                    <button class="btn btn-danger btn-sm" type="button"
                                                                        @click="DelSpecList(spec_2 ,'spec_2' ,spec_2_key)"
                                                                        disabled>刪除</button>
                                                                </div>
                                                                <div v-else>
                                                                    <button class="btn btn-danger btn-sm" type="button"
                                                                        @click="DelSpecList(spec_2 ,'spec_2' ,spec_2_key)">刪除</button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row form-group col-sm-12">
                                        <div class="col-sm-6" style="margin-bottom:20px">
                                            <div class="col-sm-2 no-pa">
                                                <label class="control-label">安全庫存量</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <input class="form-control" name="safty_qty_all" id="keyword"
                                                    v-model="safty_qty_all">
                                            </div>
                                            <div class="cola-sm-2">
                                                <button class="btn btn-primary btn-sm" type="button"
                                                    @click="change_safty_qty_all">套用</button>
                                            </div>
                                        </div>
                                    </div>
                                    <textarea style="display: none;" id="SkuListdata" name="SkuListdata" cols="30" rows="10">@{{ SkuList }}</textarea>

                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th v-if="products.spec_dimension >= 1" class="text-nowrap">規格一</th>
                                                <th v-if="products.spec_dimension == 2" class="text-nowrap">規格二</th>
                                                <th class="text-nowrap">Item編號</th>
                                                <th class="text-nowrap">廠商貨號</th>
                                                <th class="text-nowrap">國際條碼</th>
                                                <th class="text-nowrap">POS品號<span
                                                        class="stock_type_list text-red">*</span>
                                                </th>
                                                <th class="text-nowrap">安全庫存量<span class="text-red red-star">*</span></th>
                                                <th class="text-nowrap">是否追加<span class="text-red">*</span></th>
                                                <th class="text-nowrap">狀態<span class="text-red">*</span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(Sku, SkuKey) in SkuList">
                                                <td v-if="products.spec_dimension >= 1">@{{ Sku.spec_1_value }}</td>
                                                <td v-if="products.spec_dimension == 2">@{{ Sku.spec_2_value }}</td>
                                                <td><input style="width:140px" class="form-control" v-model="Sku.item_no"
                                                        readonly></td>
                                                <td><input style="width:140px" class="form-control"
                                                        v-model="Sku.supplier_item_no"
                                                        :disabled="Sku.id !== '' && edit_readonly == 1"></td>
                                                <td>
                                                    <div class="form-group" style="margin-right:0px;margin-left:0px;">
                                                        <input style="width:140px" class="form-control ean_va"
                                                            v-model="Sku.ean" :name="'ean_va[' + SkuKey + ']'"
                                                            :disabled="Sku.id !== '' && edit_readonly == 1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" style="margin-right:0px;margin-left:0px;">
                                                        <input style="width: 140px" class="form-control pos_item_no_va"
                                                            v-model="Sku.pos_item_no"
                                                            :name="'pos_item_no[' + SkuKey + ']'"
                                                            :data-item_no="Sku.item_no" data-va="pos_item_no"
                                                            :disabled="Sku.id !== '' && edit_readonly == 1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group"
                                                        style="margin-right:0px;margin-left:0px;width:75px">
                                                        <input class="form-control safty_qty_va" type="number"
                                                            min="0" v-model="Sku.safty_qty"
                                                            :name="'safty_qty_va[' + SkuKey + ']'"
                                                            :disabled="Sku.id !== '' && edit_readonly == 1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <select class="form-control js-select2"
                                                        v-model="Sku.is_additional_purchase"
                                                        {{ $products->edit_readonly == '1' ? 'disabled' : '' }}>
                                                        <option value="1">是</option>
                                                        <option value="0">否</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control js-select2" v-model="Sku.status"
                                                        @change="checkItemQty($event,Sku,SkuKey)"
                                                        {{ $products->edit_readonly == '1' ? 'disabled' : '' }}>
                                                        <option value="1">啟用</option>
                                                        <option value="0">停用</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                        </div>

                    </div>
                </div>
                {{-- 二維多規格結束 --}}
                <div class="row" style="margin-bottom:15px">
                    <div class="col-sm-12">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-11">

                            <button class="btn btn-large btn-success" type="button" id="save_data">
                                <i class="fa-solid fa-floppy-disk"></i> 儲存
                            </button>
                            <a class="btn btn-danger" href="{{ URL::previous() }}">
                                <i class="fa-solid fa-ban"></i> 取消
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            {{-- 修改紀錄 --}}
            @include('backend.products.models.model_update_log')
            @include('backend.products.models.model_requisitions_log')
            @include('backend.products.models.model_promotional')
        </div>
    </div>
    </div>

@endsection
@section('js')
    <script>
        var SkuComponent = Vue.extend({
            data: function() {
                return {
                    Spec: { // 選擇的規格
                        spec_1: '',
                        spec_2: '',
                    },
                    SpecList: [],
                    SkuList: @json($products_item),
                    products: @json($products),
                    old_spec_dimension: 0, // 0 單規 1一規 2 二規
                    product_spec_info: @json($product_spec_info),
                    safty_qty_all: 0,
                    edit_readonly: null,
                }
            },
            mounted() {
                this.edit_readonly = document.querySelector('#edit_readonly').value;
            },
            created() {
                let spec_value_list = JSON.parse(this.product_spec_info.spec_value_list);
                let item_list = JSON.parse(this.product_spec_info.item_list);

                spec_value_list.spec_1.map(function(value, key) {
                    value.old_spec = 1;
                });
                spec_value_list.spec_2.map(function(value, key) {
                    value.old_spec = 1;
                });
                this.old_spec_dimension = this.products.spec_dimension;

                this.SpecList = spec_value_list;
                this.SkuList = item_list;

            },
            methods: {
                change_safty_qty_all() {
                    let change_num = this.safty_qty_all;
                    let edit_readonly = this.edit_readonly;

                    this.SkuList.map(function(value, key) {
                        if (edit_readonly == 1) {
                            if (value.id == '') {
                                value.safty_qty = change_num;
                            }
                        } else {
                            value.safty_qty = change_num;
                        }
                    });
                },
                AddSpecToSkuList(spec_type) {
                    if (spec_type == '1') {
                        this.SpecList.spec_1.push({
                            name: '',
                            sort: this.SpecList.spec_1.length,
                            only_key: parseFloat('0.' + crypto.getRandomValues(new Uint32Array(1))[0]).toString(36).substring(8),
                        });
                    } else if (spec_type == '2') {
                        this.SpecList.spec_2.length;
                        this.SpecList.spec_2.push({
                            name: '',
                            sort: this.SpecList.spec_2.length,
                            only_key: parseFloat('0.' + crypto.getRandomValues(new Uint32Array(1))[0]).toString(36).substring(8),
                        });
                    }
                },
                DelSpecList(obj, type, index) { //刪除規格
                    if (type == 'spec_1') {
                        //這邊要檢查對應到要刪除的 sku list 是否包含item編號
                        this.SpecList.spec_1.splice(index, 1);
                        let new_SkuList = this.SkuList.filter(data => data.spec_1_only_key !== obj.only_key);
                        this.SkuList = new_SkuList;
                    } else if (type == 'spec_2') {
                        this.SpecList.spec_2.splice(index, 1);
                        let new_SkuList = this.SkuList.filter(data => data.spec_2_only_key !== obj.only_key);
                        this.SkuList = new_SkuList;
                    }
                },
                AddSkuList() { //新增規格
                    var skuList = this.SkuList;
                    var specList = this.SpecList;
                    if (this.products.spec_dimension == 1) {
                        specList.spec_1.map(function(value, key) {
                            let only_key_isset = skuList.filter(data => data.spec_1_only_key === value
                                .only_key);
                            if (only_key_isset.length == 0) {
                                skuList.push({
                                    id: '',
                                    sort_key: key,
                                    sort: skuList.length,
                                    spec_1_value: value.name,
                                    spec_1_only_key: value.only_key,
                                    item_no: '',
                                    supplier_item_no: '',
                                    ean: '',
                                    pos_item_no: '',
                                    safty_qty: 0,
                                    is_additional_purchase: 1,
                                    status: 1,
                                })
                            } else {
                                only_key_isset[0].spec_1_value = value.name;
                                only_key_isset[0].spec_1_only_key = value.only_key;
                                only_key_isset[0].sort_key = key;
                            }
                        })


                    } else if (this.products.spec_dimension == 2) {
                        let spac_1 = [];
                        let spac_2 = [];


                        specList.spec_1.map(function(value, key) {
                            spac_1.push(key);
                        });
                        specList.spec_2.map(function(value, key) {
                            spac_2.push(key);
                        });

                        let cartesian = (...a) => a.reduce((a, b) => a.flatMap(d => b.map(e => [d, e].flat())));
                        let output = cartesian(spac_1, spac_2);
                        output.map(function(value, key) {
                            spac_1_key = value[0];
                            spac_2_key = value[1];
                            let find_spac_obj_1 = specList.spec_1[spac_1_key];
                            let find_spac_obj_2 = specList.spec_2[spac_2_key];
                            //檢查原先是否有存在該筆規格
                            let only_key_isset = skuList.filter(data => data.spec_1_only_key ===
                                find_spac_obj_1
                                .only_key && data.spec_2_only_key === find_spac_obj_2.only_key);
                            if (only_key_isset.length == 0) {
                                skuList.push({
                                    id: '',
                                    sort_key: spac_1_key + '' + spac_2_key,
                                    sort: skuList.length,
                                    spec_1_value: find_spac_obj_1.name,
                                    spec_2_value: find_spac_obj_2.name,
                                    spec_1_only_key: find_spac_obj_1.only_key,
                                    spec_2_only_key: find_spac_obj_2.only_key,
                                    item_no: '',
                                    supplier_item_no: '',
                                    ean: '',
                                    pos_item_no: '',
                                    safty_qty: 0,
                                    is_additional_purchase: 1,
                                    status: 1,
                                })
                            } else {
                                only_key_isset[0].spec_1_value = find_spac_obj_1.name;
                                only_key_isset[0].spec_2_value = find_spac_obj_2.name;
                                only_key_isset[0].spec_1_only_key = find_spac_obj_1.only_key;
                                only_key_isset[0].spec_2_only_key = find_spac_obj_2.only_key;
                                only_key_isset[0].sort_key = spac_1_key + '' + spac_2_key;
                            }

                        });
                        skuList.sort((a, b) => a.sort_key - b.sort_key); //重新排序
                        return this.SkuList;
                    }

                },
                drag(eve) {
                    $('tbody').addClass('elements-box')
                    eve.dataTransfer.setData("text/index", eve.target.dataset.index);
                    eve.dataTransfer.setData("text/type", eve.target.dataset.type);
                    $('tbody').addClass('elements-box')
                },
                dragover(eve) {
                    eve.preventDefault();
                    eve.target.parentNode.classList.add('ondragover');
                    $('tbody').addClass('elements-box');

                },
                dragleave(eve) {
                    eve.preventDefault();
                    eve.target.parentNode.classList.remove('ondragover');
                    $('tbody').removeClass('elements-box');
                },
                drop(eve) {
                    eve.target.parentNode.classList.remove('ondragover');
                    $('tbody').removeClass('elements-box');
                    var index = eve.dataTransfer.getData("text/index");
                    var type = eve.dataTransfer.getData("text/type");
                    let targetIndex = eve.target.parentNode.dataset.index;
                    let targetType = eve.target.parentNode.dataset.type;
                    if (targetType !== type) {
                        console.log('不能跨類別');
                    } else {
                        switch (targetType) {
                            case 'spec_1':
                                var item = this.SpecList.spec_1[index];
                                this.SpecList.spec_1.splice(index, 1);
                                this.SpecList.spec_1.splice(targetIndex, 0, item);
                                break;
                            case 'spec_2':
                                var item = this.SpecList.spec_2[index];
                                this.SpecList.spec_2.splice(index, 1)
                                this.SpecList.spec_2.splice(targetIndex, 0, item)
                                break;
                            default:
                                break;
                        }
                    }
                },
                change_spec_dimension(changeVal) {
                    if (this.old_spec_dimension == 0) {
                        let add_spec_1_only_key = parseFloat('0.' + crypto.getRandomValues(new Uint32Array(1))[0]).toString(36).substring(8);
                        let add_spec_2_only_key = parseFloat('0.' + crypto.getRandomValues(new Uint32Array(1))[0]).toString(36).substring(8);
                        this.old_spec_dimension = changeVal;
                        this.SkuList.forEach((person, i, array) => {
                            this.SkuList[i].spec_1_only_key = add_spec_1_only_key;
                            if (changeVal == 2) {
                                this.SkuList[i].spec_2_only_key = add_spec_2_only_key;
                            }
                        })
                        this.SpecList.spec_1.push({
                            name: '',
                            sort: 0,
                            only_key: add_spec_1_only_key,
                            old_spec: 1,
                        });
                        if (changeVal == 2) {
                            this.SpecList.spec_2.push({
                                name: '',
                                sort: 0,
                                only_key: add_spec_2_only_key,
                                old_spec: 1,
                            });
                        }
                    }
                    if (this.old_spec_dimension == 1) {
                        if (changeVal == 2) {
                            let add_spec_2_only_key = parseFloat('0.' + crypto.getRandomValues(new Uint32Array(1))[0]).toString(36).substring(8);
                            this.SkuList.forEach((person, i, array) => {
                                this.SkuList[i].spec_2_only_key = add_spec_2_only_key;
                            })
                            this.SpecList.spec_2.push({
                                name: '',
                                sort: 0,
                                only_key: add_spec_2_only_key,
                                old_spec: 1,
                            });
                        }
                    }
                },
                checkItemQty(event, item, key) {
                    var vm = this;
                    if (event.target.value == 0) {
                        axios.post('/backend/products/ajax', {
                                item_id: item.id,
                                type: 'checkItemQty',
                            })
                            .then(function(response) {
                                if (!response.data.result) {
                                    vm.SkuList[key].status = 1;
                                    alert('Item編號' + item.item_no + '仍有庫存，不允許停用')
                                }
                            })
                            .catch(function(error) {
                                console.log(error);
                            });
                    }
                }

            },
            watch: {
                SpecList: {
                    handler(val) {
                        this.AddSkuList();
                        return this.SkuList;
                    },
                    deep: true
                },
                "products.spec_dimension": {
                    handler(val) {
                        return this.change_spec_dimension(val);
                    },
                    deep: true
                },
            },
        });
        new SkuComponent().$mount('#SkuComponent');
        var ImageUpload = Vue.extend({
            data: function() {
                return {
                    images: [],
                    old_imges: @json($product_photos),
                    file_cdn: @json(config('filesystems.disks.s3.url')),
                    readyDeletePhotos:[],
                }
            },
            mounted() {
                this.adjustTheDisplay();
            },
            created() {
                vm = this;
                for (let i = 0; i < this.old_imges.length; i++) {
                    var data = this.file_cdn + this.old_imges[i].photo_name;
                    let metadata = {
                        type: 'image/jpeg',
                    };
                    var filename = this.old_imges[i].photo_name.split('/');
                    let file = new File([data], filename[2], metadata);
                    file.src = data;
                    file.id = this.old_imges[i].id;
                    file.size = this.old_imges[i].photo_size;
                    file.sizeConvert = vm.formatBytes(this.old_imges[i].photo_size)
                    this.images.push(file);
                }
            },
            methods: {
                fileSelected(e) {
                    let vm = this;
                    var selectedFiles = e.target.files;
                    if (selectedFiles.length + this.images.length > 15) {
                        alert('不能超過15張照片');
                        e.target.value = '';
                        return false;
                    }
                    for (let i = 0; i < selectedFiles.length; i++) {
                        var img;
                        var file = selectedFiles[i];
                        var setStr = 0;
                        var objectUrl = URL.createObjectURL(selectedFiles[i]);
                        this.getImage(objectUrl, file, function(callback) {
                            if (callback.file.size > 1048576) {
                                alert('照片名稱:' + callback.file.name + '已經超出大小');
                            } else if (callback.file.type !== 'image/jpeg' && callback.file.type !==
                                'image/png') {
                                alert('照片名稱:' + file.name + '格式錯誤');
                            } else if (callback.width < 480 && callback.height < 480) {
                                alert('照片名稱:' + callback.file.name + '照片尺寸必須為480*480以上');
                            } else if (callback.width !== callback.height) {
                                console.log(callback.width, callback.height);
                                alert('照片名稱:' + callback.file.name + '照片比例必須為1:1');
                            } else {
                                vm.images.push(callback.file);
                                vm.adjustTheDisplay();
                                vm.images.map(function(value, key) {
                                    value.sizeConvert = vm.formatBytes(value.size);
                                });
                            }
                        });

                    }
                    e.target.value = '';
                },
                getImage(fileurl, file, callback) {
                    vm = this;
                    img = new Image();
                    img.src = fileurl;
                    img.onload = function() {
                        result = {
                            width: this.width,
                            height: this.height,
                            file: file
                        };
                        callback(result);
                    };
                },
                delImages(index) {
                    var yes = confirm('你確定要刪除嗎？');
                    if (yes) {
                        if (this.images[index].id) {
                            this.readyDeletePhotos.push(this.images[index]);
                            this.$delete(this.images, index);
                        } else {
                            this.$delete(this.images, index);
                        }
                    }
                    this.adjustTheDisplay();
                },
                imagesCheck() {
                    console.log('-----------------------');
                    console.log(this.images);
                },
                formatBytes(bytes, decimals = 2) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const dm = decimals < 0 ? 0 : decimals;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
                },
                drag(eve) {
                    eve.dataTransfer.setData("text/index", eve.target.dataset.index);
                    eve.dataTransfer.setData("text/type", eve.target.dataset.type);
                    $('.btn-events-none').css('pointer-events', 'none');
                },
                dragover(eve) {
                    eve.preventDefault();
                    eve.target.parentNode.classList.add('ondragover');
                    $('.btn-events-none').css('pointer-events', 'auto');

                },
                dragleave(eve) {
                    eve.target.parentNode.classList.remove('ondragover');
                    $('.btn-events-none').css('pointer-events', 'auto');
                    eve.preventDefault();
                },
                drop(eve) {
                    let vm = this;
                    $('.btn-events-none').css('pointer-events', 'auto');
                    eve.target.parentNode.classList.remove('ondragover');
                    var index = eve.dataTransfer.getData("text/index");
                    var type = eve.dataTransfer.getData("text/type");
                    let targetIndex = eve.target.dataset.index;
                    let targetType = eve.target.dataset.type;
                    var item = this.images[index];
                    this.images.splice(index, 1);
                    this.images.splice(targetIndex, 0, item);
                    this.adjustTheDisplay();
                },
                adjustTheDisplay() {
                    let list = new DataTransfer();
                    for (let i = 0; i < this.images.length; i++) {
                        list.items.add(this.images[i]);
                        let reader = new FileReader();
                        if (!this.images[i].id) {
                            reader.onload = (e) => {
                                this.$refs.image[i].src = reader.result;
                            };
                        } else {
                            this.$refs.image[i].src = this.images[i].src;
                        }
                        reader.readAsDataURL(this.images[i]);
                    }
                    this.$refs.images_files.files = list.files;
                },
            },
            computed: {},

            watch: {},
        })
        new ImageUpload().$mount('#ImageUploadBox');
        // 捲動功能
        window.onscroll = function() {
            var page_1 = document.getElementById("page-1"); //獲取到導航欄id
            var page_2 = document.getElementById("page-2"); //獲取到導航欄id
            var page_1_btn = document.getElementById("click-page-1");
            var page_2_btn = document.getElementById("click-page-2");

            //使用JS原生物件，獲取元素的Class樣式列表
            var titleClientRect_1 = page_1.getBoundingClientRect();
            var titleClientRect_2 = page_2.getBoundingClientRect();

            if ((titleClientRect_1.top - titleClientRect_1.height) < 0) {
                var page_1_status = true;
            } else {
                var page_1_status = false;
            }
            // console.log(titleClientRect_2.top - titleClientRect_2.height) ;
            if ((titleClientRect_2.top - titleClientRect_2.height) < 300) {
                var page_2_status = true;
            } else {
                var page_2_status = false;
            }
            if (page_1_status && page_2_status == false) {
                if (!page_1_btn.classList.contains('sysinfo-activie')) {
                    page_1_btn.classList.add("sysinfo-activie")
                }
                if (page_2_btn.classList.contains('sysinfo-activie')) {
                    page_2_btn.classList.remove("sysinfo-activie")
                }
            } else {
                if (!page_2_btn.classList.contains('sysinfo-activie')) {
                    page_2_btn.classList.add("sysinfo-activie")
                }
                if (page_1_btn.classList.contains('sysinfo-activie')) {
                    page_1_btn.classList.remove("sysinfo-activie")
                }
            }
        }
        $(document).ready(function() {
            let stockType = '{{ $products->stock_type }}';
            if (stockType == 'T') {
                $('.red-star').hide();
            } else {
                $('.red-star').show();
            }

            let errorMessage = '{{ $error_message }}';
            if (errorMessage != '') {
                $('#save_data').prop('disabled', true);
                alert(errorMessage);
            } else {
                $(".supplier_id").change(function() {
                    $('#save_data').prop('disabled', false);
                    let supplier_id = $(this).val();

                    axios({
                        method: "post",
                        url: "/backend/products/ajax",
                        params: {
                            type: 'getSupplierStockType',
                            supplier_id: supplier_id,
                            stock_type: stockType,
                        },
                    })
                    .then((response) => {
                        let result = response.data.result;

                        if (result.error_message != '') {
                            $('#save_data').prop('disabled', true);
                            alert(result.error_message);
                        } else {
                            $('#save_data').prop('disabled', false);
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                });
            }

            isWithWarranty();
            $('input[type=radio][name=is_with_warranty]').change(function() {
                if(this.value == '0'){
                    $("#warranty_days").val(0);
                }
                isWithWarranty() ;
            });
            function isWithWarranty(){
                let isWithWarranty = $("input[name=is_with_warranty]:checked").val();
                console.log(isWithWarranty) ;
                if(isWithWarranty == '1'){
                    $("#warranty_days").prop("readonly", false);
                }else if (!isWithWarranty || isWithWarranty == '0'){
                    $("#warranty_days").prop("readonly", true);
                }
            }

            $('input[type=radio][name=stock_type]').change(function() {
                if ($(this).val() == 'T') {
                    $('.stock_type_list').hide();
                } else {
                    $('.stock_type_list').show();
                }
            });
            $(".supplier_id").select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇",
            });
            $(".tax_type").select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇"
            });
            $(".brand_id").select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇"
            });
            $(".category_id").select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇",
            });
            $(document).on("click", "#save_data", function() {
                $(".ean_va").each(function() {
                    var text = $(this).val();
                    $(this).rules("add", {
                        notChinese: {
                            param: function() {
                                let obj = {
                                    text: text,
                                }
                                return obj;
                            },
                            depends: function(element) {
                                return true;
                            },
                        },
                    });
                })

                $(".safty_qty_va").each(function() {
                    $(this).rules("add", {
                        required: function() {
                            return $("input[name=stock_type]:checked").val() != 'T';
                        },
                        digits: function() {
                            return $("input[name=stock_type]:checked").val() != 'T';
                        },
                        notOnlyZero: function() {
                            return $("input[name=stock_type]:checked").val() != 'T';
                        },
                        messages: {
                            digits: '請輸入正整數'
                        },
                    });
                })
                $(".spec_1_va").each(function() {
                    $(this).rules("add", {
                        required: true,
                        notRepeating: true,
                    });
                })
                $(".spec_2_va").each(function() {
                    $(this).rules("add", {
                        required: true,
                        notRepeating: true,
                    });
                })
                $(".pos_item_no_va").each(function() {
                    $(this).rules("add", {
                        required: {
                            depends: function(element) {
                                return $("input[name='stock_type']:checked").val() !==
                                    'T';
                            }
                        },
                        remote: {
                            param: function(element) {
                                return {
                                    url: "/backend/products/ajax",
                                    type: "post",
                                    dataType: "json",
                                    cache: false,
                                    data: {
                                        pos_item_no: $(element).val(),
                                        item_no: $(element).data('item_no'),
                                        type: 'checkPosItemNo',
                                    },
                                    dataFilter: function(data) {
                                        data = JSON.parse(data)
                                        if (data.result) {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    },
                                }
                            },
                        },
                        notRepeating: true,
                        messages: {
                            remote: 'POS品號重複'
                        },

                    });
                });
                $("#new-form").submit()
            })

            $("#new-form").validate({
                //   debug: true,
                submitHandler: function(form) {
                    var item_num = JSON.parse($('#SkuListdata').val()).length;
                    if (item_num <= 0) {
                        alert('至少輸入一個品項')
                        return false;
                    }
                    var imgJson = JSON.parse($('#imgJson').val()).length;
                    if (imgJson <= 0) {
                        alert('至少上傳一張圖片')
                        return false;
                    }
                    if ($("input[name=product_type]:checked").val() == 'G' && $("input[name=selling_price]").val() != 0) {
                        alert('商品類型為贈品則售價須為0');
                        return false;
                    }
                    $('#save_data').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    product_name: {
                        required: true,
                    },
                    supplier_id: {
                        required: true,
                    },
                    tax_type: {
                        required: true,
                    },
                    category_id: {
                        required: true,
                    },
                    brand_id: {
                        required: true,
                    },
                    selling_channel:{
                        required: true,
                    },
                    is_with_warranty: {
                        required: function() {
                            return $("input[name=stock_type]:checked").val() != 'T';
                        },
                    },
                    uom: {
                        required: true,
                    },
                    min_purchase_qty: {
                        digits: true,
                        min: function() {
                            return $("input[name=stock_type]:checked").val() != 'T' ? 1 : 0;
                        },
                    },
                    //長
                    length: {
                        required: function() {
                            return $("input[name=stock_type]:checked").val() != 'T';
                        },
                        digits: function() {
                            return $("input[name=stock_type]:checked").val() != 'T';
                        },
                    },
                    width: {
                        required: function() {
                            return $("input[name=stock_type]:checked").val() != 'T';
                        },
                        digits: function() {
                            return $("input[name=stock_type]:checked").val() != 'T';
                        },
                    },
                    height: {
                        required: function() {
                            return $("input[name=stock_type]:checked").val() != 'T';
                        },
                        digits: function() {
                            return $("input[name=stock_type]:checked").val() != 'T';
                        },
                    },
                    list_price: {
                        required: true,
                        digits: true,
                        betweenValues: {
                            param: function(element) {
                                return {
                                    valueMin: 1,
                                    signMin: ">=",
                                    valueMax: 999999,
                                    signMax: "<=",
                                };
                            },
                        },
                    },
                    selling_price: {
                        required: true,
                        digits: true,
                        notOnlyZero: function() {
                            if ($('input[name=product_type]:checked').val() == 'N') {
                                return true;
                            } else {
                                return false;
                            }
                        },
                        needZero: function() {
                            if ($('input[name=product_type]:checked').val() == 'G') {
                                return true;
                            } else {
                                return false;
                            }
                        },
                        betweenValues: {
                            param: function(element) {
                                return {
                                    valueMin: 1,
                                    signMin: ">=",
                                    valueMax: 999999,
                                    signMax: "<=",
                                };
                            },
                            depends: function(element) {
                                return $('input[name=product_type]:checked').val() == 'N';
                            },
                        },
                    }, //重量
                    weight: {
                        required: function() {
                            return $("input[name=stock_type]:checked").val() != 'T';
                        },
                        digits: function() {
                            return $("input[name=stock_type]:checked").val() != 'T';
                        },
                    },
                    product_brief_1: {
                        maxlength: 60,
                    },
                    spec_1: {
                        required: true,
                        maxlength: 20,
                    },
                    spec_2: {
                        required: true,
                        maxlength: 20,
                    },
                    product_brief_2: {
                        maxlength: 60,
                    },
                    product_brief_3: {
                        maxlength: 60,
                    },
                    warranty_days: {
                        required: function() {
                            return $("input[name=is_with_warranty]:checked").val() == '1' && $("input[name=stock_type]:checked").val() != 'T';
                        },
                        digits: function() {
                            return $("input[name=is_with_warranty]:checked").val() == '1' && $("input[name=stock_type]:checked").val() != 'T';
                        },
                        notOnlyZero: function() {
                            if($("input[name=is_with_warranty]:checked").val() == '1'){
                                return true ;
                            }else{
                                return false ;
                            }
                        },
                    },
                },
                messages: {
                    min_purchase_qty: {
                        digits: function() {
                            return $("input[name=stock_type]:checked").val() != 'T' ? "只可輸入正整數" : "只可輸入0或正整數";
                        },
                        min: function() {
                            return $("input[name=stock_type]:checked").val() != 'T' ? "只可輸入正整數" : "只可輸入0或正整數";
                        },
                    },
                    //市價
                    list_price: {
                        betweenValues: '請輸入正確的數值'
                    },
                    //售價
                    selling_price: {
                        betweenValues: '請輸入正確的數值'
					},
                    warranty_days: {
                        digits: "只可輸入正整數",
                        min: function() {
                            if ($("input[name=has_expiry_date]:checked").val() == '1' && $("input[name=stock_type]:checked").val() != 'T') {
                                return '只可輸入正整數';
                            }
                        },
                    },
                },
                errorClass: "help-block",
                errorElement: "span",
                errorPlacement: function(error, element) {
                    if (element.parent('.input-group').length || element.is(':radio')) {
                        error.insertAfter(element.parent());
                        return;
                    }

                    if (element.is('select')) {
                        element.parent().append(error);
                        return;
                    }

                    error.insertAfter(element);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).closest(".form-group").addClass("has-error");
                },
                success: function(label, element) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
            });
        });
    </script>
@endsection
