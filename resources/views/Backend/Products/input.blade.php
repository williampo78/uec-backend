@extends('Backend.master')
@section('title', '分類階層內容管理')
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
    <div class="side-bar">
        <nav class="navigation">
            <ul>
                <li>
                    <a href="#page-1">基本資訊</a>
                    <br>
                    <a href="#page-2">規格</a>
                </li>
            </ul>
        </nav>
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
                    action="{{ route('products.store') }}" enctype="multipart/form-data" novalidaten="ovalidate">
                    @csrf
                    <div id="page-1">

                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">庫存類型</label><span class="redtext">*</span>
                                </div>
                                <div class="col-sm-3 ">
                                    <label class="radio-inline">
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1"> 買斷
                                        [A]
                                    </label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"> 寄售
                                        [B]
                                    </label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3">
                                        轉單[T]
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label ">商品序號</label><span class="redtext">*</span>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="" readonly>
                                </div>

                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">供應商<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control js-select2" name="active" id="active">
                                        <option value="">無</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">商品名稱<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">課稅別<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control js-select2" name="active" id="active">
                                        <option value="">無</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">POS分類<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">品牌<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control js-select2" name="active" id="active">
                                        <option value="">無</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">商品型號</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="">
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
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio4" value="option4"> 宅配
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">溫層</label>
                                </div>
                                <div class="col-sm-9">
                                    <label class="radio-inline">
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio4" value="option4"> 常溫
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
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">最小採購量</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="">
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
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1"> 無
                                    </label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                        有，天數
                                    </label>
                                </div>
                                <div class="col-sm-3">
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">允收期(天)</label>
                                </div>
                                <div class="col-sm-3">
                                    <input class="form-control" name="keyword" id="keyword" value="">
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
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1"> 一般品
                                    </label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"> 贈品
                                    </label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3"> 加購品
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">停售<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-2">
                                    <label class="radio-inline">
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1"> 是
                                    </label>
                                </div>
                                <div class="col-sm-2">
                                    <label class="radio-inline">
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3"> 否
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
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                                <div class="col-sm-1">
                                    <label class="control-label">寬</label>
                                </div>
                                <div class="col-sm-2 ">
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                                <div class="col-sm-1">
                                    <label class="control-label">高</label>
                                </div>
                                <div class="col-sm-2">
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">重量(公克)<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-3">
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">市價(含稅)<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">售價(含稅)<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">成本(含稅)</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">毛利(%)</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="" readonly>
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
                                    <input class="form-control" name="keyword" id="keyword" value="" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">建檔時間</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
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
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">修改時間</label>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="keyword" id="keyword" value="" readonly>
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
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-1 no-pa">
                                </div>
                                <div class="col-sm-10">
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-1 no-pa">
                                </div>
                                <div class="col-sm-10">
                                    <input class="form-control" name="keyword" id="keyword" value="">
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
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-1 no-pa">
                                    <label class="control-label">效期控管<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-1">
                                    <label class="radio-inline">
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1"> 無
                                    </label>
                                </div>
                                <div class="col-sm-2">
                                    <label class="radio-inline">
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                        有保固，天數
                                    </label>
                                </div>
                                <div class="col-sm-1 no-pa">
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-1 no-pa">
                                    <label class="control-label">保固範圍</label>
                                </div>
                                {{-- <textarea name="" id="" cols="30" rows="10"></textarea> --}}
                                <div class="col-sm-11">
                                    <textarea class="form-control" rows="10" cols="10"></textarea>
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
                                        <input type="file" @change="fileSelected" multiple>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                {{-- <div class="col-sm-12"> --}}
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
                    SkuList: [{}],
                    products: {
                        spec_dimension: 0,
                    }
                }
            },
            methods: {
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
                    let spac_1 = [];
                    let spac_2 = [];
                    var skuList = this.SkuList;
                    var specList = this.SpecList;

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
                        let only_key_isset = skuList.filter(data => data.spec_1_only_key === find_spac_obj_1
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
                                is_additional_purchase: 0,
                                status: 0,
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
                        this.SpecList =  {
                            spec_1: [],
                            spec_2: [],
                        }
                        switch (val) {
                            case '0': //單規格
                                this.SkuList = [{}];
                                break;
                            case '1': //一維多規格
                                this.SkuList = [{}];
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
                    for (let i = 0; i < selectedFiles.length; i++) {
                        this.images.push(selectedFiles[i]);
                    }
                    this.adjustTheDisplay();
                    this.images.map(function(value, key) {
                        value.sizeConvert = vm.formatBytes(value.size);
                    });
                    e.target.value = '';
                },
                delImages(index) {
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
                    for (let i = 0; i < this.images.length; i++) {
                        let reader = new FileReader();
                        reader.onload = (e) => {
                            this.$refs.image[i].src = reader.result;
                        };
                        reader.readAsDataURL(this.images[i]);
                    }
                }

            },
            computed: {},
            watch: {},
        })
        new ImageUpload().$mount('#ImageUploadBox');
        window.onscroll = function() {
            var page_1 = document.getElementById("page-1"); //獲取到導航欄id
            var page_2 = document.getElementById("page-2"); //獲取到導航欄id
            //使用JS原生物件，獲取元素的Class樣式列表
            var titleClientRect_1 = page_1.getBoundingClientRect();
            var titleClientRect_2 = page_2.getBoundingClientRect();

            if ((titleClientRect_1.top - titleClientRect_1.height) < 0) {
                // console.log("show 1") ; 
            } else {
                // console.log("hide 1") ; 
            }

            if ((titleClientRect_2.top - titleClientRect_2.height) < 0) {
                // console.log("show 2") ; 
            } else {
                // console.log("hide 2") ;
            }


        }
        // Get all sections that have an ID defined
    </script>
@endsection