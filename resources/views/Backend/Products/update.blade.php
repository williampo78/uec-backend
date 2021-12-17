@extends('Backend.master')
@section('title', '商品主檔 - 新增基本資訊')
@section('content')
<style>
    /* .no-pa {
            padding: 0px;
        } */

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
</style>
<div class="sysinfo">
    <div class="sysinfo-title theme-color">基本檔</div>
    <div class="sysinfo-content">
        <ul>
            <a href="#page-1">
                <li class="sysinfo-li sysinfo-activie" id="click-page-1">
                    前台資料
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
            <h1 class="page-header"><i class="fa fa-list"></i>商品主檔 - 新增基本資訊</h1>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">請輸入下列欄位資料</div>
        <div class="panel-body" id="category_hierarchy_content_input">
            <form class="form-horizontal" role="form" id="new-form" method="POST"
                action="{{ route('products.update', $products->id) }}" enctype="multipart/form-data"
                novalidaten="ovalidate">
                @csrf
                @method('PUT')
                <div id="page-1">
                    <input name="id" value="{{$products->id}}" style="display: none">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">庫存類型</label><span class="redtext">*</span>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="stock_type" value="A" {{ $products->stock_type == 'A'
                                        ? 'checked' : 'disabled' }}> 買斷
                                        [A]
                                    </label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="stock_type" value="B" {{ $products->stock_type == 'B'
                                        ? 'checked' : 'disabled' }}> 寄售
                                        [B]
                                    </label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="stock_type" value="T" {{ $products->stock_type == 'T'
                                        ? 'checked' : 'disabled' }}>
                                        轉單[T]
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label ">商品序號</label><span class="redtext">*</span>
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
                                <div class="col-sm-2 ">
                                    <label class="control-label">供應商<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control supplier_id" name="supplier_id">
                                        @foreach ($supplier as $val)
                                        <option value="{{ $val->id }}" {{ $products->supplier_id == $val->id ?
                                            'selected' : '' }}>
                                            {{ $val->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">商品名稱<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="product_name" value="{{ $products->product_name }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">課稅別<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control tax_type" name="tax_type">
                                        <option value="TAXABLE" {{ $products->tax_type == 'TAXABLE' ? 'selected' : '' }}>
                                            應稅(5%)</option>
                                        <option value="NON_TAXABLE" {{ $products->tax_type == 'NON_TAXABLE' ? 'selected' :
                                            '' }}>免稅</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">POS分類<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control category_id" name="category_id">
                                        @foreach ($pos as $key => $val)
                                        <option value="{{ $val->id }}" {{ $products->category_id == $val->id ? 'selected' :
                                            '' }}>
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
                                <div class="col-sm-2 ">
                                    <label class="control-label">品牌<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control brand_id" name="brand_id" id="brand_id">
                                        @foreach ($brands as $val)
                                        <option value="{{ $val->id }}" {{ $products->brand_id == $val->id ? 'selected' : ''
                                            }}>
                                            {{ $val->brand_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">商品型號</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="model" value=" {{ $products->model }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">商品通路<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <label class="radio-inline">
                                        <input type="radio" name="selling_channel" value="EC" {{ $products->selling_channel
                                        == 'EC' ? 'checked' : '' }}> 網路獨賣
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-2 ">
                                <label class="control-label">溫層<span class="redtext">*</span></label>
                            </div>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="lgst_temperature" value="NORMAL" {{
                                        $products->lgst_temperature == 'NORMAL' ? 'checked' : '' }}> 常溫
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">配送方式<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <label class="radio-inline">
                                        <input type="radio" name="lgst_method" value="HOME" {{ $products->lgst_method ==
                                        'HOME' ? 'checked' : '' }}> 宅配
                                    </label>
                                </div>  
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">商品交期<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <label class="radio-inline">
                                        <input type="radio" name="delivery_type" value="IN_STOCK" {{
                                            $products->delivery_type == 'IN_STOCK' ? 'checked' : 'disabled' }}> 現貨
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">單位<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="uom" value="{{ $products->uom }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">最小採購量</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="min_purchase_qty" type="number" min="0" value="0"
                                        value="{{ $products->min_purchase_qty }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">效期控管<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="has_expiry_date" value="0" {{ $products->has_expiry_date
                                        == '0' ? 'checked' : 'disabled' }}> 無
                                    </label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="has_expiry_date" value="1" {{ $products->has_expiry_date
                                        == '1' ? 'checked' : 'disabled' }}>
                                        有，天數
                                    </label>
                                </div>
                                {{-- 效期控管的天數 --}}
                                <div class="col-sm-3">
                                    <input class="form-control" name="expiry_days" value=" {{ $products->expiry_days }}" {{
                                        $products->has_expiry_date == '1' ? 'checked' : 'readonly' }}>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">允收期(天)</label>
                                </div>
                                <div class="col-sm-3">
                                    <input type="number" class="form-control" name="expiry_receiving_days" min="0"
                                        value="{{ $products->expiry_receiving_days }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">商品類型<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-3 ">
                                    <label class="radio-inline">
                                        <input type="radio" name="product_type" value="N" {{ $products->product_type == 'N'
                                        ? 'checked' : 'disabled' }}> 一般品
                                    </label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="product_type" value="G" {{ $products->product_type == 'G'
                                        ? 'checked' : 'disabled' }}> 贈品
                                    </label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="product_type" value="A" {{ $products->product_type == 'A'
                                        ? 'checked' : 'disabled' }}> 加購品
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">停售<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-2">
                                    <label class="radio-inline">
                                        <input type="radio" name="is_discontinued" id="inlineRadio1" value="1" {{
                                            $products->is_discontinued == '1' ? 'checked' : 'disabled' }}> 是
                                    </label>
                                </div>
                                <div class="col-sm-2">
                                    <label class="radio-inline">
                                        <input type="radio" name="is_discontinued" id="inlineRadio3" value="0" {{
                                            $products->is_discontinued == '0' ? 'checked' : 'disabled' }}> 否
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">材積(公分) <span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-1">
                                    <label class="control-label">長</label>
                                </div>
                                <div class="col-sm-2">
                                    <input class="form-control" name="length" type="number" min="0"
                                        value="{{ $products->length }}">
                                </div>
                                <div class="col-sm-1">
                                    <label class="control-label">寬</label>
                                </div>
                                <div class="col-sm-2 ">
                                    <input class="form-control" name="width" type="number" min="0"
                                        value="{{ $products->width }}">
                                </div>
                                <div class="col-sm-1">
                                    <label class="control-label">高</label>
                                </div>
                                <div class="col-sm-2">
                                    <input class="form-control" name="height" type="number" min="0"
                                        value="{{ $products->height }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">重量(公克)<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-3">
                                    <input class="form-control" name="weight" type="number" min="0"
                                        value="{{ $products->weight }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">市價(含稅)<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="list_price" type="number" min="0"
                                        value="{{ $products->list_price }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">售價(含稅)<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="selling_price" type="number" min="0"
                                        value="{{ $products->selling_price }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">成本(含稅)</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">毛利(%)</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="" value="" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">採購人員</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">轉單審核人員</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">建檔人員</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" id="keyword" value="{{ $products->updated_by_name }}"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">建檔時間</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" id="keyword" value="{{ $products->created_at }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">修改人員</label>
                                </div>
                                <div class="col-sm-7">
                                    <input class="form-control" id="keyword" value="{{ $products->updated_by_name }}"
                                        readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label class="control-label">
                                        <a href="#">修改紀錄</a>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="col-sm-2 ">
                                    <label class="control-label">修改時間</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    {{-- 商品描述 START --}}
                    <div class="row">
                        <div class="col-sm-12 ">
                            <div class="form-group">
                                <div class="col-sm-1 ">
                                    <label class="control-label">商品簡述</label>
                                </div>
                                <div class="col-sm-10">
                                    <input class="form-control" name="product_brief_1"
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
                                    <input class="form-control" name="product_brief_2"
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
                                    <input class="form-control" name="product_brief_3"
                                        value="{{ $products->product_brief_3 }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- 商品描述 END --}}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-1 ">
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
                                <div class="col-sm-1 ">
                                    <label class="control-label">保固期限<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-1">
                                    <label class="radio-inline">
                                        <input type="radio" name="is_with_warranty" value="0" {{ $products->is_with_warranty
                                        == '0' ? 'checked' : '' }}> 無
                                    </label>
                                </div>
                                <div class="col-sm-2">
                                    <label class="radio-inline">
                                        <input type="radio" name="is_with_warranty" value="1" {{ $products->is_with_warranty
                                        == '1' ? 'checked' : '' }}>
                                        有保固，天數
                                    </label>
                                </div>
                                <div class="col-sm-1 ">
                                    <input class="form-control" name="warranty_days" min="0"
                                        value="{{ $products->warranty_days }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="col-sm-1 ">
                                    <label class="control-label">保固範圍</label>
                                </div>
                                <div class="col-sm-11">
                                    <textarea class="form-control" rows="10" cols="10" name="warranty_scope">
                                                {{ $products->warranty_scope }}
                                            </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="ImageUploadBox">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1 ">
                                        <label class="control-label">商品圖檔</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <p class="help-block">最多上傳15張，每張size不可超過1MB，副檔名須為JPG、JPEG、PNG</p>
                                        <input type="file" @change="fileSelected" multiple>
                                        <input style="display: none" type="file" :ref="'images_files'" name="filedata[]"
                                            multiple>
                                    </div>
                                    <textarea style="display: none" name="imgJson" id="" cols="30"
                                        rows="10">@{{images}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-2 col-md-2" v-for="(image, key) in images" :key="key">
                                {{-- @foreach ($product_photos as )

                                @endforeach --}}
                                <div class="thumbnail" @dragstart="drag" @dragover='dragover' @dragleave='dragleave'
                                    @drop="drop" :data-index="key" :data-type="'image'" draggable="true"
                                    style="pointer-events: auto;">
                                    <div class="img-box" style="pointer-events: none;">
                                        <img :ref="'image'">
                                    </div>
                                    <div class="caption" style="pointer-events: none;">
                                        <p class="text-overflow-p">檔案名稱: @{{ image . name }}</p>
                                        <p>檔案大小:@{{ image . sizeConvert }}</p>
                                        <p>
                                            排序: @{{ key + 1 }}
                                            <button class="btn btn-danger pull-right btn-events-none" type="button"
                                                @click="delImages(key)" style="pointer-events: auto;">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            {{--
                        </div> --}}
                    </div>
                </div>
        </div>
        <hr>
        <div id="page-2">
            <div id="SkuComponent">
                <textarea style="display: none" name="SpecListJson" id="" cols="30" rows="10">@{{SpecList}}</textarea>
                <div class="row form-group">
                    <div class="col-sm-12">
                        <div class="col-sm-2 ">
                            <label class="radio-inline">
                                <input type="radio" name="spec_dimension" value="0" {{ $products->spec_dimension == '0'
                                ? 'checked' : 'disabled' }}>
                                單規格
                            </label>
                        </div>
                        <div class="col-sm-2">
                            <label class="radio-inline">
                                <input type="radio" name="spec_dimension" value="1" {{ $products->spec_dimension == '1'
                                ? 'checked' : 'disabled' }}>
                                一維多規格
                            </label>
                        </div>
                        <div class="col-sm-2">
                            <label class="radio-inline">
                                <input type="radio" name="spec_dimension" value="2" {{ $products->spec_dimension == '2'
                                ? 'checked' : 'disabled' }}>
                                二維多規格
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6" v-if="products.spec_dimension >= 1">
                        <div class="col-sm-2 ">
                            <label class="control-label">規格一<span class="redtext">*</span></label>
                        </div>
                        <div class="col-sm-9">
                            <select class="form-control js-select2" name="spec_1" id="spec_1" disabled>
                                <option value="顏色" {{ $products->spec_1 == '顏色' ? 'selected' : '' }}>
                                    顏色</option>
                                <option value="尺寸" {{ $products->spec_1 == '尺寸' ? 'selected' : '' }}>
                                    尺寸</option>
                                <option value="容量" {{ $products->spec_1 == '容量' ? 'selected' : '' }}>
                                    容量</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6" v-if="products.spec_dimension == 2">
                        <div class="col-sm-2 ">
                            <label class="control-label">規格二<span class="redtext">*</span></label>
                        </div>
                        <div class="col-sm-9">
                            <select class="form-control js-select2" name="spec_2" id="spec_2" disabled>
                                <option value="顏色" {{ $products->spec_2 == '顏色' ? 'selected' : '' }}>
                                    顏色</option>
                                <option value="尺寸" {{ $products->spec_2 == '尺寸' ? 'selected' : '' }}>
                                    尺寸</option>
                                <option value="容量" {{ $products->spec_2 == '容量' ? 'selected' : '' }}>
                                    容量</option>
                            </select>
                        </div>
                    </div>
                </div>
                {{-- 二維多規格 --}}
                <div class="row form-group">
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
                                    @dragover='dragover' @dragleave='dragleave' @drop="drop" draggable="true"
                                    :data-index="spec_1_key" :data-type="'spec_1'">
                                    <td>
                                        <div class="col-sm-1">
                                            <label class="control-label"><i style="font-size: 20px;"
                                                    class="fa fa-list"></i></label>
                                        </div>
                                        <div class="col-sm-9">
                                            <div v-if="spec_1.old_spec">
                                                <input class="form-control" v-model="spec_1.name" disabled>
                                            </div>
                                            <div v-else>
                                                <input class="form-control" v-model="spec_1.name">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div v-if="spec_1.old_spec">

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
                                    @dragover='dragover' @dragleave='dragleave' @drop="drop" draggable="true"
                                    :data-index="spec_2_key" :data-type="'spec_2'">
                                    <td>
                                        <div class="col-sm-1">
                                            <label class="control-label"><i style="font-size: 20px;"
                                                    class="fa fa-list"></i></label>
                                        </div>
                                        <div class="col-sm-9">
                                            <div v-if="spec_2.old_spec">
                                                <input class="form-control" v-model="spec_2.name" disabled>
                                            </div>
                                            <div v-else>
                                                <input class="form-control" v-model="spec_2.name">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div v-if="spec_2.old_spec"></div>
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
                <div class="row form-group">
                    <div class="col-sm-6">
                        <div class="col-sm-2 ">
                            <label class="control-label">安全庫存量</label>
                        </div>
                        <div class="col-sm-8">
                            <input class="form-control" name="safty_qty_all" id="keyword" v-model="safty_qty_all">
                        </div>
                        <div class="cola-sm-2">
                            <button class="btn btn-primary btn-sm" type="button"
                                @click="change_safty_qty_all">套用</button>
                        </div>
                    </div>
                </div>
                <textarea style="display: none" style="display: none" name="SkuListdata" cols="30"
                    rows="10">@{{ SkuList }}</textarea>
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th v-if="products.spec_dimension >= 1" style="width: 10%">規格一</th>
                            <th v-if="products.spec_dimension == 2" style="width: 10%">規格二</th>
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
                        <tr v-for="(Sku, SkuKey) in SkuList">
                            <td v-if="products.spec_dimension >= 1">@{{ Sku . spec_1_value }}</td>
                            <td v-if="products.spec_dimension == 2">@{{ Sku . spec_2_value }}</td>
                            <td><input class="form-control" v-model="Sku.item_no" readonly></td>
                            <td><input class="form-control" v-model="Sku.supplier_item_no"></td>
                            <td><input class="form-control" v-model="Sku.ean"></td>
                            <td><input class="form-control" v-model="Sku.pos_item_no"></td>
                            <td><input class="form-control" v-model="Sku.safty_qty"></td>
                            <td>
                                <select class="form-control js-select2" v-model="Sku.is_additional_purchase"
                                    id="active">
                                    <option value="1">是</option>
                                    <option value="0">否</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-control js-select2" v-model="Sku.status">
                                    <option value="1">啟用</option>
                                    <option value="0">停用</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        {{-- 二維多規格結束 --}}
        <button class="btn btn-large btn-success" type="button" id="save_data">
            <i class="fa fa-save"></i>
                儲存
            </button>
        <a class="btn btn-danger" href="{{ url('products') }}"><i class="fa fa-ban"></i> 取消</a>
    </form>
    </div>
</div>
</div>
@endsection
@section('js')
<script>
    var SkuComponent = Vue.extend({
        data: function () {
            return {
                Spec: { // 選擇的規格
                    spec_1: '',
                    spec_2: '',
                },
                SpecList: [],
                SkuList: @json($products_item),
                products: @json($products),
                product_spec_info: @json($product_spec_info),
                safty_qty_all: 0,
                }
    },
        mounted() {
    },
        created() {
        let spec_value_list = JSON.parse(this.product_spec_info.spec_value_list) ;
        let item_list       = JSON.parse(this.product_spec_info.item_list) ;

        spec_value_list.spec_1.map(function (value, key) {
            value.old_spec = 1;
        });
        spec_value_list.spec_2.map(function (value, key) {
            value.old_spec = 1;
        });

        this.SpecList = spec_value_list ;
        this.SkuList = item_list ;

    },
        methods: {
        change_safty_qty_all() {
            let change_num = this.safty_qty_all;
            this.SkuList.map(function (value, key) {
                value.safty_qty = change_num;
            });
        },
        AddSpecToSkuList(spec_type) {
            if (spec_type == '1') {
                this.SpecList.spec_1.push({
                    name: '',
                    sort: this.SpecList.spec_1.length,
                    only_key: Math.random().toString(36).substring(8),
                });
            } else if (spec_type == '2') {
                this.SpecList.spec_2.length;
                this.SpecList.spec_2.push({
                    name: '',
                    sort: this.SpecList.spec_2.length,
                    only_key: Math.random().toString(36).substring(8),
                });
            }
        },
        DelSpecList(obj, type, index) { //刪除規格
            if (type == 'spec_1') {
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
                specList.spec_1.map(function (value, key) {
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


                specList.spec_1.map(function (value, key) {
                    spac_1.push(key);
                });
                specList.spec_2.map(function (value, key) {
                    spac_2.push(key);
                });

                let cartesian = (...a) => a.reduce((a, b) => a.flatMap(d => b.map(e => [d, e].flat())));
                let output = cartesian(spac_1, spac_2);
                output.map(function (value, key) {
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

    },
        computed: {

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
                this.Spec = { // 選擇的規格
                    spec_1: '',
                    spec_2: '',
                };
                this.SpecList = {
                    spec_1: [],
                    spec_2: [],
                }
                switch (val) {
                    case '0': //單規格
                        this.SkuList = [{}];
                        break;
                    case '1': //一維多規格
                        this.SkuList = [];
                        break;
                    case '2': //二維多規格
                        this.SkuList = [];
                        break;
                    default:
                        break;
                }

            },
            deep: true
        }
    },
        })
    new SkuComponent().$mount('#SkuComponent');
    var ImageUpload = Vue.extend({
        data: function () {
            return {
                images: [],
                old_imges: @json($product_photos),
            file_cdn: @json(config('filesystems.disks.s3.url')),
        }
    },
        mounted() {
        this.adjustTheDisplay() ;
    },
        created() {
            vm = this ;
            for(let i = 0; i< this.old_imges.length; i++) {
                var data = this.file_cdn + this.old_imges[i].photo_name;
                    let metadata = {
                        type: 'image/jpeg',
                    };
                    console.log(this.old_imges[i]) ; 
                    var filename = this.old_imges[i].photo_name.split('/');
                    let file = new File([data], filename[2], metadata);
                    file.src = data;
                    file.id = this.old_imges[i].id;
                    file.size = this.old_imges[i].photo_size ;
                    file.sizeConvert = vm.formatBytes(this.old_imges[i].photo_size)
                    this.images.push(file);
                }
            },
    methods: {
        fileSelected(e) {
            let vm = this;
            var selectedFiles = e.target.files;
            if(selectedFiles.length + this.images.length > 15){
                alert('不能超過15張照片')  ;
                e.target.value = '';
                return false  ;
            }
            for (let i = 0; i < selectedFiles.length; i++) {
                let type = selectedFiles[i].type ; 
    
                if(selectedFiles[i].size > 1048576){
                    alert('照片名稱:'+selectedFiles[i].name +'已經超出大小') ;
                }else if(type !== 'image/jpeg' && type!== 'image/png'){
                    alert('照片名稱:'+selectedFiles[i].name +'格式錯誤') ;
                }else{
                    this.images.push(selectedFiles[i]);
                }
            }
            this.adjustTheDisplay();
            this.images.map(function (value, key) {
                if(value.id){

                }else{
                    value.sizeConvert = vm.formatBytes(value.size);
                }
            });
            e.target.value = '';
        },
        delImages(index) {
            var yes = confirm('你確定要刪除嗎？');
            if (yes) {
                console.log(this.images); 
                if(this.images[index].id){
                    axios.post('/backend/del_photos', {
                        id: this.images[index].id,
                        _token: '{{ csrf_token() }}',
                        type:'products',
                    })
                    .then(function(response) {
                    
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
                    this.$delete(this.images, index);
                }else{
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
    computed: { },

    watch: { },
        })
    new ImageUpload().$mount('#ImageUploadBox');
    // 捲動功能
    window.onscroll = function () {
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
    $(document).ready(function () {
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
        $(document).on("click", "#save_data", function() {
                $(".safty_qty_va").each(function(){
                    $(this).rules("add", {
                        required: true,
                        digits: true,
                    });
                })
                $(".spec_1_va").each(function(){
                    $(this).rules("add", {
                        required: true,
                    });
                })
                $(".spec_2_va").each(function(){
                    $(this).rules("add", {
                        required: true,
                    });
                })
                $( "#new-form" ).submit()
        })

      $("#new-form").validate({
        //   debug: true,
          submitHandler: function(form) {
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
              uom: {
                  required: true,
              },
              //長
              length: {
                  required: true,
                  digits: true,
              },
              width: {
                  required: true,
                  digits: true,
              },
              height: {
                  required: true,
                  digits: true,
              },
              list_price: {
                  required: true,
                  digits: true,
              },
              selling_price: {
                  required: true,
                  digits: true,
              }
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