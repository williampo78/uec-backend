@extends('backend.master')
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
            </ul>
        </div>
    </div>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i>商品主檔 - 新增基本資訊</h1>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">請輸入下列欄位資料</div>
            <div class="panel-body" id="category_hierarchy_content_input">
                <form class="form-horizontal" role="form" id="new-form" method="POST" enctype="multipart/form-data"
                    novalidaten="ovalidate">
                    <div id="page-1">

                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">庫存類型</label><span class="redtext">*</span>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="stock_type" value="A"
                                            {{ $products->stock_type == 'A' ? 'checked' : 'disabled' }}> 買斷
                                        [A]
                                    </label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="stock_type" value="B"
                                            {{ $products->stock_type == 'B' ? 'checked' : 'disabled' }}> 寄售
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
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label ">商品序號</label><span class="redtext">*</span>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="product_no" value="{{ $products->product_no }}"
                                        readonly>
                                </div>

                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">供應商<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control supplier_id" name="supplier_id" disabled>
                                        @foreach ($supplier as $val)
                                            <option value="{{ $val->id }}"
                                                {{ $products->supplier_id == $val->id ? 'selected' : '' }}>
                                                {{ $val->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">商品名稱<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="product_name" disabled
                                        value="{{ $products->product_name }}">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">課稅別<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control tax_type" name="tax_type" disabled>
                                        <option value="TAXABLE" {{ $products->tax_type == 'TAXABLE' ? 'selected' : '' }}>
                                            應稅(5%)</option>
                                        <option value="NON_TAXABLE"
                                            {{ $products->tax_type == 'NON_TAXABLE' ? 'selected' : '' }}>免稅</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">POS分類<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control category_id" name="category_id" disabled>
                                        @foreach ($pos as $key => $val)
                                            <option value="{{ $val->id }}"
                                                {{ $products->category_id == $val->id ? 'selected' : '' }}>
                                                {{ $val->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">品牌<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control brand_id" name="brand_id" id="brand_id" disabled>
                                        @foreach ($brands as $val)
                                            <option value="{{ $val->id }}"
                                                {{ $products->brand_id == $val->id ? 'selected' : '' }}>
                                                {{ $val->brand_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">商品型號</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="model" value=" {{ $products->model }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">商品通路<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <label class="radio-inline">
                                        <input type="radio" name="selling_channel" value="EC"
                                            {{ $products->selling_channel == 'EC' ? 'checked' : 'disabled' }}> 網路獨賣
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">溫層<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <label class="radio-inline">
                                        <input type="radio" name="lgst_temperature" value="NORMAL"
                                            {{ $products->lgst_temperature == 'NORMAL' ? 'checked' : 'disabled' }}> 常溫
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">配送方式<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <label class="radio-inline">
                                        <input type="radio" name="lgst_method" value="HOME"
                                            {{ $products->lgst_method == 'HOME' ? 'checked' : 'disabled' }}> 宅配
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">商品交期<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <label class="radio-inline">
                                        <input type="radio" name="delivery_type" value="IN_STOCK"
                                            {{ $products->delivery_type == 'IN_STOCK' ? 'checked' : 'disabled' }}> 現貨
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">單位<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="uom" value="{{ $products->uom }}" disabled>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">最小採購量</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="min_purchase_qty" type="number" min="0"
                                        value="{{ $products->min_purchase_qty }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">效期控管<span class="redtext">*</span></label>
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
                                            {{ $products->has_expiry_date == '1' ? 'checked' : 'disabled' }}>
                                        有，天數
                                    </label>
                                </div>
                                {{-- 效期控管的天數 --}}
                                <div class="col-sm-3">
                                    <input class="form-control" name="expiry_days"
                                        value=" {{ $products->expiry_days }}" disabled>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">允收期(天)</label>
                                </div>
                                <div class="col-sm-3">
                                    <input type="number" class="form-control" name="expiry_receiving_days" min="0"
                                        value="{{ $products->expiry_receiving_days }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">商品類型<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-3 ">
                                    <label class="radio-inline">
                                        <input type="radio" name="product_type" value="N"
                                            {{ $products->product_type == 'N' ? 'checked' : 'disabled' }}> 一般品
                                    </label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="product_type" value="G"
                                            {{ $products->product_type == 'G' ? 'checked' : 'disabled' }}> 贈品
                                    </label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="product_type" value="A"
                                            {{ $products->product_type == 'A' ? 'checked' : 'disabled' }}> 加購品
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">停售<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-2">
                                    <label class="radio-inline">
                                        <input type="radio" name="is_discontinued" id="inlineRadio1" value="1"
                                            {{ $products->is_discontinued == '1' ? 'checked' : 'disabled' }}> 是
                                    </label>
                                </div>
                                <div class="col-sm-2">
                                    <label class="radio-inline">
                                        <input type="radio" name="is_discontinued" id="inlineRadio3" value="0"
                                            {{ $products->is_discontinued == '0' ? 'checked' : 'disabled' }}> 否
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">材積(公分) <span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-1">
                                    <label class="control-label">長</label>
                                </div>
                                <div class="col-sm-2">
                                    <input class="form-control" name="length" type="number" min="0"
                                        value="{{ $products->length }}" disabled>
                                </div>
                                <div class="col-sm-1">
                                    <label class="control-label">寬</label>
                                </div>
                                <div class="col-sm-2 ">
                                    <input class="form-control" name="width" type="number" min="0"
                                        value="{{ $products->width }}" disabled>
                                </div>
                                <div class="col-sm-1">
                                    <label class="control-label">高</label>
                                </div>
                                <div class="col-sm-2">
                                    <input class="form-control" name="height" type="number" min="0"
                                        value="{{ $products->height }}" disabled>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">重量(公克)<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-3">
                                    <input class="form-control" name="weight" type="number" min="0"
                                        value="{{ $products->weight }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">市價(含稅)<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="list_price" type="number" min="0"
                                        value="{{ $products->list_price }}" disabled>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">售價(含稅)<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="selling_price" type="number" min="0"
                                        value="{{ $products->selling_price }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">成本(含稅)</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" value="{{ $products->item_cost }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">毛利(%)</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" value="{{ $products->gross_margin }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">採購人員</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">轉單審核人員</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">建檔人員</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" id="keyword" value="{{ $products->created_name }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">建檔時間</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" id="keyword" value="{{ $products->created_at }}"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">修改人員</label>
                                </div>
                                <div class="col-sm-7">
                                    <input class="form-control" id="keyword" value="{{ $products->updated_name }}"
                                        readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label class="control-label">
                                        <a href="#" data-toggle="modal" data-target="#model_category">修改紀錄</a>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">修改時間</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword"
                                        value="{{ $products->updated_at }}" readonly>
                                </div>
                            </div>
                        </div>
                        <hr>
                        {{-- 商品描述 START --}}
                        <div class="row form-group">
                            <div class="col-sm-12 ">
                                <div class="col-sm-1 no-pa">
                                    <label class="control-label">商品簡述</label>
                                </div>
                                <div class="col-sm-10">
                                    <input class="form-control" name="product_brief_1"
                                        value="{{ $products->product_brief_1 }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-1 no-pa">
                                </div>
                                <div class="col-sm-10">
                                    <input class="form-control" name="product_brief_2"
                                        value="{{ $products->product_brief_2 }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-1 no-pa">
                                </div>
                                <div class="col-sm-10">
                                    <input class="form-control" name="product_brief_3"
                                        value="{{ $products->product_brief_3 }}" disabled>
                                </div>
                            </div>
                        </div>
                        {{-- 商品描述 END --}}
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-1 no-pa">
                                    <label class="control-label">專利字號</label>
                                </div>
                                <div class="col-sm-10">
                                    <input class="form-control" name="patent_no" value="{{ $products->patent_no }}"
                                        disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-1 no-pa">
                                    <label class="control-label">保固期限<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-1">
                                    <label class="radio-inline">
                                        <input type="radio" name="is_with_warranty" value="0"
                                            {{ $products->is_with_warranty == '0' ? 'checked' : 'disabled' }}> 無
                                    </label>
                                </div>
                                <div class="col-sm-2">
                                    <label class="radio-inline">
                                        <input type="radio" name="is_with_warranty" value="1"
                                            {{ $products->is_with_warranty == '1' ? 'checked' : 'disabled' }}>
                                        有保固，天數
                                    </label>
                                </div>
                                <div class="col-sm-1 no-pa">
                                    <input class="form-control" name="warranty_days"
                                        value="{{ $products->warranty_days }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-1 no-pa">
                                    <label class="control-label">保固範圍</label>
                                </div>
                                <div class="col-sm-11">
                                    <textarea class="form-control" rows="10" cols="10" name="warranty_scope" disabled>
                                                                    {{ $products->warranty_scope }}
                                                                </textarea>
                                </div>
                            </div>
                        </div>
                        <div id="ImageUploadBox">
                            <div class="row form-group">
                                <div class="col-sm-12">
                                    <div class="col-sm-1 no-pa">
                                        <label class="control-label">商品圖檔</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <p class="help-block">最多上傳15張，每張size不可超過1MB，副檔名須為JPG、JPEG、PNG</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                @foreach ($product_photos as $key => $val)
                                    <div class="col-sm-2 col-md-2">
                                        <div class="thumbnail">
                                            <div class="img-box" style="pointer-events: none;">
                                                <img src="{{ config('filesystems.disks.s3.url') . $val->photo_name }}">
                                            </div>
                                            <div class="caption" style="pointer-events: none;">
                                                <p>
                                                    排序: {{ $key + 1 }}
                                                    {{-- <button class="btn btn-danger pull-right btn-events-none" type="button"
                                                        @click="delImages(key)" style="pointer-events: auto;">
                                                        <i class="fa fa-trash"></i>
                                                    </button> --}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <hr>
                        <div id="page-2">
                            <div id="SkuComponent">
                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        <div class="col-sm-2 ">
                                            <label class="radio-inline">
                                                <input type="radio" name="spec_dimension" value="0"
                                                    {{ $products->spec_dimension == '0' ? 'checked' : 'disabled' }}>
                                                單規格
                                            </label>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="radio-inline">
                                                <input type="radio" name="spec_dimension" value="1"
                                                    {{ $products->spec_dimension == '1' ? 'checked' : 'disabled' }}>
                                                一維多規格
                                            </label>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="radio-inline">
                                                <input type="radio" name="spec_dimension" value="2"
                                                    {{ $products->spec_dimension == '2' ? 'checked' : 'disabled' }}>
                                                二維多規格
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    @if ($products->spec_dimension >= '1')
                                        <div class="col-sm-6">
                                            <div class="col-sm-2 no-pa">
                                                <label class="control-label">規格一<span
                                                        class="redtext">*</span></label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input class="form-control" type="text" name="spec_1" id="spec_1"
                                                value="{{ $products->spec_1 }}" disabled>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($products->spec_dimension == '2')
                                        <div class="col-sm-6" v-if="products.spec_dimension == 2">
                                            <div class="col-sm-2 no-pa">
                                                <label class="control-label">規格二<span
                                                        class="redtext">*</span></label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input class="form-control" type="text" name="spec_1" id="spec_1"
                                                value="{{ $products->spec_2 }}" disabled>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                                {{-- 二維多規格 --}}
                                <div class="row form-group">
                                    @if ($products->spec_dimension >= '1')
                                        <div class="col-sm-6">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    {{-- <tr>
                                                    <th>
                                                        <button class="btn btn-primary btn-sm" type="button" @click="AddSpecToSkuList('1')">新增項目
                                                        </button>
                                                    </th>
                                                </tr> --}}
                                                </thead>
                                                <tbody>
                                                    @foreach ($spac_list['spac_1'] as $val)
                                                        <tr v-for="(spec_1, spec_1_key) in SpecList.spec_1"
                                                            @dragstart="drag" @dragover='dragover' @dragleave='dragleave'
                                                            @drop="drop" draggable="true" :data-index="spec_1_key"
                                                            :data-type="'spec_1'">
                                                            <td>
                                                                <div class="col-sm-1">
                                                                    <label class="control-label"><i
                                                                            style="font-size: 20px;"
                                                                            class="fa fa-list"></i></label>
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    <input class="form-control"
                                                                        value="{{ $val->spec_1_value }}" disabled>
                                                                </div>
                                                                <div class="col-sm-2">
                                                                    {{-- <button class="btn btn-danger btn-sm" type="button"
                                                                @click="DelSpecList(spec_1 ,'spec_1' ,spec_1_key)">刪除</button> --}}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                    @if ($products->spec_dimension == '2')
                                        <div class="col-sm-6">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>

                                                </thead>
                                                <tbody>
                                                    @foreach ($spac_list['spac_2'] as $val)
                                                        <tr v-for="(spec_2, spec_2_key) in SpecList.spec_2"
                                                            @dragstart="drag" @dragover='dragover' @dragleave='dragleave'
                                                            @drop="drop" draggable="true" :data-index="spec_2_key"
                                                            :data-type="'spec_2'">
                                                            <td>
                                                                <div class="col-sm-1">
                                                                    <label class="control-label"><i
                                                                            style="font-size: 20px;"
                                                                            class="fa fa-list"></i></label>
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    <input class="form-control"
                                                                        value="{{ $val->spec_1_value }}" disabled>
                                                                </div>
                                                                <div class="col-sm-2">
                                                                    {{-- <button class="btn btn-danger btn-sm" type="button"
                                                                @click="DelSpecList(spec_2 ,'spec_2' ,spec_2_key)">刪除</button> --}}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                                <textarea style="display: none" name="SkuListdata" cols="30"
                                    rows="10">@{{ SkuList }}</textarea>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            @if ($products->spec_dimension >= '1')
                                                <th style="width: 10%">規格一</th>
                                            @endif
                                            @if ($products->spec_dimension == '2')
                                                <th v-if="products.spec_dimension == 2" style="width: 10%">規格二</th>
                                            @endif
                                            <th style="width: 15%">Item編號</th>
                                            <th style="width: 10%">廠商貨號</th>
                                            <th style="width: 10%">國際條碼</th>
                                            <th style="width: 10%">POS品號</th>
                                            <th style="width: 10%">安全庫存量*</th>
                                            <th style="width: 10%">是否追加*</th>
                                            <th style="width: 10%">狀態*</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products_item as $val)
                                            <tr>
                                                @if ($products->spec_dimension >= '1')
                                                    <td>
                                                        {{ $val->spec_1_value }}
                                                    </td>
                                                @endif
                                                @if ($products->spec_dimension == '2')
                                                    <td>
                                                        {{ $val->spec_2_value }}
                                                    </td>
                                                @endif

                                                <td><input class="form-control" value="{{ $val->item_no }}" disabled>
                                                </td>
                                                <td><input class="form-control" value="{{ $val->supplier_item_no }}"
                                                        disabled></td>
                                                <td><input class="form-control" value="{{ $val->ean }}" disabled>
                                                </td>
                                                <td><input class="form-control" value="{{ $val->pos_item_no }}"
                                                        disabled></td>
                                                <td><input class="form-control" value="{{ $val->safty_qty }}"
                                                        disabled>
                                                </td>
                                                <td>
                                                    <select class="form-control js-select2" id="active" disabled>
                                                        <option value="1" {{ $val->active == '1' ? 'selected' : '' }}>是
                                                        </option>
                                                        <option value="0" {{ $val->active == '0' ? 'selected' : '' }}>否
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control js-select2" disabled>
                                                        <option value="1" {{ $val->status == '1' ? 'selected' : '' }}>啟用
                                                        </option>
                                                        <option value="0" {{ $val->status == '0' ? 'selected' : '' }}>停用
                                                        </option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>

                            </div>

                        </div>
                        {{-- 二維多規格結束 --}}
                        {{-- <button class="btn btn-large btn-primary" type="submit">儲存</button> --}}
                </form>
                @include('backend.products.model_update_log')
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
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
            $(".supplier_id").select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇"
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
                placeholder: "請選擇"
            });
        });
    </script>
@endsection
