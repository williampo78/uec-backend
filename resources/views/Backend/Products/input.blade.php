@extends('Backend.master')
@section('title', '商品主檔 - 新增基本資訊')
@section('content')
    <style>
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
                <form role="form" id="new-form" method="POST" action="{{ route('products.store') }}"
                    enctype="multipart/form-data" novalidaten="ovalidate">
                    @csrf
                    <div id="page-1" class="form-horizontal">

                        <div class="row ">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">庫存類型</label><span class="redtext">*</span>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="stock_type" value="A" checked> 買斷
                                            [A]
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="stock_type" value="B"> 寄售
                                            [B]
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="stock_type" value="T">
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
                                        <input class="form-control" name="product_no" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class=" form-group">

                                    <div class="col-sm-2 ">
                                        <label class="control-label">供應商<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control supplier_id" name="supplier_id">
                                            @foreach ($supplier as $val)
                                                <option value="{{ $val->id }}">{{ $val->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class=" form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">商品名稱<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="product_name" value="">
                                        {{-- <span class="">123</span> --}}
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
                                            <option value="TAXABLE">應稅(5%)</option>
                                            <option value="NON_TAXABLE">免稅</option>
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
                                                <option value="{{ $val->id }}"> {{ $val->name }}</option>
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
                                                <option value="{{ $val->id }}">{{ $val->brand_name }}</option>
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
                                        <input class="form-control" name="model" value="">
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
                                            <input type="radio" name="selling_channel" value="EC" checked> 網路獨賣
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">溫層<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <label class="radio-inline">
                                            <input type="radio" name="lgst_temperature" value="NORMAL" checked> 常溫
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">配送方式<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <label class="radio-inline">
                                            <input type="radio" name="lgst_method" value="HOME" checked> 宅配
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
                                            <input type="radio" name="delivery_type" value="IN_STOCK" checked> 現貨
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2">
                                        <label class="control-label">單位<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="uom">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">最小採購量</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="min_purchase_qty" type="number" min="0"
                                            value="0">
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
                                            <input type="radio" name="has_expiry_date" value="0" checked> 無
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="has_expiry_date" value="1">
                                            有，天數
                                        </label>
                                    </div>
                                    {{-- 效期控管的天數 --}}
                                    <div class="col-sm-3">
                                        <input class="form-control" name="expiry_days" value="0">
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
                                            value="0">
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
                                            <input type="radio" name="product_type" value="N" checked> 一般品
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="product_type" value="G"> 贈品
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="product_type" value="A"> 加購品
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
                                            <input type="radio" name="is_discontinued" id="inlineRadio1" value="1"> 是
                                        </label>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="radio-inline">
                                            <input type="radio" name="is_discontinued" id="inlineRadio3" value="0" checked>
                                            否
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
                                        <input class="form-control" name="length" type="number" min="0" value="0">
                                    </div>
                                    <div class="col-sm-1">
                                        <label class="control-label">寬</label>
                                    </div>
                                    <div class="col-sm-2 ">
                                        <input class="form-control" name="width" type="number" min="0" value="0">
                                    </div>
                                    <div class="col-sm-1">
                                        <label class="control-label">高</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input class="form-control" name="height" type="number" min="0" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">重量(公克)<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-3">
                                        <input class="form-control" name="weight" type="number" min="0" value="0">
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
                                        <input class="form-control" name="list_price" type="number" min="0" value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">售價(含稅)<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="selling_price" type="number" min="0" value="0">
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
                                        <input class="form-control" name="keyword" id="keyword" value="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">建檔時間</label>
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
                                        <label class="control-label">修改人員</label>
                                    </div>
                                    <div class="col-sm-7">
                                        <input class="form-control" name="keyword" id="keyword" value="" readonly>
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
                                        <input class="form-control" name="product_brief_1">
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
                                        <input class="form-control" name="product_brief_2">
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
                                        <input class="form-control" name="product_brief_3">
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
                                        <input class="form-control" name="patent_no" value="">
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
                                            <input type="radio" name="is_with_warranty" value="0" checked> 無
                                        </label>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="radio-inline">
                                            <input type="radio" name="is_with_warranty" value="1">
                                            有保固，天數
                                        </label>
                                    </div>
                                    <div class="col-sm-1 ">
                                        <input class="form-control" name="warranty_days" min="0" value="0">
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
                                        <textarea class="form-control" rows="10" cols="10"
                                            name="warranty_scope"></textarea>
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
                                            <input type="file" @change="fileSelected" multiple accept=".jpg,.jpeg,.png">
                                            <input style="display: none" type="file" :ref="'images_files'" name="filedata[]"
                                                multiple>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2 col-md-2" v-for="(image, key) in images" :key="key">
                                    <div class="thumbnail" @dragstart="drag" @dragover='dragover'
                                        @dragleave='dragleave' @drop="drop" :data-index="key" :data-type="'image'"
                                        draggable="true" style="pointer-events: auto;">
                                        <div class="img-box" style="pointer-events: none;">
                                            <img :ref="'image'">
                                        </div>
                                        <div class="caption" style="pointer-events: none;">
                                            <p>檔案名稱: @{{ image . name }}</p>
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
                                {{-- </div> --}}
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div id="page-2">
                        @include('Backend.Products.inputSpec')
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
            data: function() {
                return {
                    Spec: { // 選擇的規格
                        spec_1: '',
                        spec_2: '',
                    },
                    SpecList: {
                        spec_1: [],
                        spec_2: [],
                    },
                    SkuList: [{
                        id: '',
                        sort_key: 0,
                        sort: 0,
                        spec_1_value: 0,
                        spec_1_only_key: 0,
                        item_no: '',
                        supplier_item_no: 0,
                        ean: 0,
                        pos_item_no: 0,
                        safty_qty: 0,
                        is_additional_purchase: 0,
                        status: 0,
                    }],
                    products: {
                        spec_dimension: 0,
                    },
                    safty_qty_all: 0,
                }
            },
            methods: {
                change_safty_qty_all() {
                    let change_num = this.safty_qty_all;
                    this.SkuList.map(function(value, key) {
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
                                this.SkuList = [{
                                    id: '',
                                    sort_key: 0,
                                    sort: 0,
                                    spec_1_value: 0,
                                    spec_1_only_key: 0,
                                    item_no: 0,
                                    supplier_item_no: 0,
                                    ean: 0,
                                    pos_item_no: 0,
                                    safty_qty: 0,
                                    is_additional_purchase: 0,
                                    status: 0,
                                }];
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
            data: function() {
                return {
                    images: [],
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
                    this.images.map(function(value, key) {
                        value.sizeConvert = vm.formatBytes(value.size);
                    });
                    e.target.value = '';
                },
                delImages(index) 
                {
                    this.$delete(this.images, index);
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
                    console.log('eve.index:' + eve.target.dataset.index);
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
                        reader.onload = (e) => {
                            this.$refs.image[i].src = reader.result;
                        };
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
            // 驗證表單
            // product_name
            $(document).on("click", "#save_data", function() {
                $(".safty_qty_va").each(function(){
                    console.log('safty_qty_va') ;
                    $(this).rules("add", {
                        required: true,
                        digits: true,
                    });
                })
                $(".spec_1_va").each(function(){
                    console.log('spec_1_va') ;
                    $(this).rules("add", {
                        required: true,
                    });
                })
                $(".spec_2_va").each(function(){
                    console.log('spec_2_va') ;
                    $(this).rules("add", {
                        required: true,
                    });
                })
                $( "#new-form" ).submit();
            })

            $("#new-form").validate({
                // debug: true,
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
                // unhighlight: function(element, errorClass, validClass) {
                //     $(element).closest(".form-group").removeClass("has-error");
                // },
                success: function(label, element) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
            });

        });
    </script>
@endsection
