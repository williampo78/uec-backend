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

        img {
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
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <div class="col-sm-2 no-pa">
                                <label class="control-label">庫存類型</label><span class="redtext">*</span>
                            </div>
                            <div class="col-sm-3 ">
                                <label class="radio-inline">
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1"> 買斷 [A]
                                </label>
                            </div>
                            <div class="col-sm-3">
                                <label class="radio-inline">
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"> 寄售 [B]
                                </label>
                            </div>
                            <div class="col-sm-3">
                                <label class="radio-inline">
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3"> 轉單[T]
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
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"> 有，天數
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
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"> 有保固，天數
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
                                <button @click="imagesCheck" type="button">測試按鈕</button>
                            </div>
                        </div>
                        <div class="row form-group">
                            {{-- <div class="col-sm-12"> --}}
                            <div class="col-sm-2 col-md-2" v-for="(image, key) in images" :key="key" >
                                <div class="thumbnail" @dragstart="drag"
                                @dragover='dragover' @dragleave='dragleave' @drop="drop" :data-index="key"
                                :data-type="'image'" draggable="true">
                                    <div class="img-box">
                                        <img :ref="'image'">
                                    </div>
                                    <div class="caption">
                                        <p>檔案名稱: @{{ image . name }}</p>
                                        <p>檔案大小:@{{ image . sizeConvert }}</p>
                                        <p><a href="#" class="btn btn-danger" role="button">刪除</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            {{-- </div> --}}
                        </div>
                    </div>
                    <hr>
                    <div id="SkuComponent">
                        {{-- <button @click="testdescartes" type="button">測試Descartes function</button> --}}
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-2 ">
                                    <label class="radio-inline">
                                        <input type="radio" name="inlineRadioOptions" id="" value="option1"> 單規格
                                    </label>
                                </div>
                                <div class="col-sm-2">
                                    <label class="radio-inline">
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                        一維多規格
                                    </label>
                                </div>
                                <div class="col-sm-2">
                                    <label class="radio-inline">
                                        <input type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3">
                                        二維多規格
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">規格一<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control js-select2" name="active" id="active">
                                        <option value="">顏色</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">規格二<span class="redtext">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control js-select2" name="active" id="active">
                                        <option value="">尺寸</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{-- 二維多規格 --}}
                        <div class="row form-group">
                            <div class="col-sm-6">
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
                                            @dragover='dragover' @dragleave='dragleave' @drop="drop()" draggable="true"
                                            :data-index="spec_1_key" :data-type="'spec_1'">
                                            <td>
                                                <div class="col-sm-1">
                                                    <label class="control-label"><i style="font-size: 20px;"
                                                            class="fa fa-list"></i></label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input class="form-control" v-model="spec_1.name">
                                                </div>
                                                <div class="col-sm-2">
                                                    <button class="btn btn-danger btn-sm" type="button"
                                                        @click="DelSpecList(spec_1 ,'spec_1' , spec_1_key)">刪除</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-6">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>
                                                <button class="btn btn-primary btn-sm" type="button"
                                                    @click="AddSpecToSkuList('2')">新增項目</button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- {{$category_products_list}} --}}
                                        <tr v-for="(spec_2, spec_2_key) in SpecList.spec_2" @dragstart="drag"
                                            @dragover='dragover' @dragleave='dragleave' @drop="drop" draggable="true"
                                            :data-index="spec_2_key" :data-type="'spec_2'">
                                            <td>
                                                <div class="col-sm-1">
                                                    <label class="control-label"><i style="font-size: 20px;"
                                                            class="fa fa-list"></i></label>
                                                </div>
                                                {{-- <div class="col-sm-2">
                                                <h3><i class="fa fa-list"></i></h3>
                                            </div> --}}
                                                <div class="col-sm-9">
                                                    <input class="form-control" v-model="spec_2.name">
                                                </div>
                                                <div class="col-sm-2">
                                                    <button class="btn btn-danger btn-sm" type="button"
                                                        @click="DelSpecList(spec_2 ,'spec_2' ,spec_2_key)">刪除</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <div class="col-sm-2 no-pa">
                                    <label class="control-label">安全庫存量</label>
                                </div>
                                <div class="col-sm-8">
                                    <input class="form-control" name="keyword" id="keyword" value="">
                                </div>
                                <div class="cola-sm-2">
                                    <button class="btn btn-primary btn-sm" type="button">套用</button>
                                </div>
                            </div>
                        </div>
                        {{-- <textarea name="" id="" cols="30" rows="10">@{{ SkuList }}</textarea> --}}
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 10%">規格一</th>
                                    <th style="width: 10%">規格二</th>
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
                                    <td>@{{ Sku . spec_1_value }}</td>
                                    <td>@{{ Sku . spec_2_value }}</td>
                                    <td><input class="form-control" v-model="Sku.item_no" readonly></td>
                                    <td><input class="form-control" v-model="Sku.supplier_item_no"></td>
                                    <td><input class="form-control" v-model="Sku.ean"></td>
                                    <td><input class="form-control" v-model="Sku.pos_item_no"></td>
                                    <td><input class="form-control" v-model="Sku.safty_qty"></td>
                                    <td>
                                        <select class="form-control js-select2" name="active" id="active">
                                            <option value="">是</option>
                                            <option value="">否</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control js-select2" name="active" id="active">
                                            <option value="">啟用</option>
                                            <option value="">停用</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                    SkuList: [],
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
                    for (let i = 0; i < this.images.length; i++) {
                        let reader = new FileReader();
                        reader.onload = (e) => {
                            this.$refs.image[i].src = reader.result;
                        };
                        reader.readAsDataURL(this.images[i]);
                    }
                    this.images.map(function(value, key) {
                        value.sizeConvert = vm.formatBytes(value.size);
                    });
                    e.target.value = '';
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
                    console.log(eve.target.dataset.index) ; 
                },
                dragover(eve) {
                    eve.preventDefault();
                    eve.target.parentNode.classList.add('ondragover');

                },
                dragleave(eve) {
                    eve.target.parentNode.classList.remove('ondragover');
                    eve.preventDefault();
                },
                drop(eve) {
                    let vm = this;
                    eve.target.parentNode.classList.remove('ondragover');
                    var index = eve.dataTransfer.getData("text/index");
                    var type = eve.dataTransfer.getData("text/type");
                    let targetIndex = eve.target.parentNode.dataset.index;
                    let targetType = eve.target.parentNode.dataset.type;
                    var item = this.images[index];
                    this.images.splice(index, 1);
                    this.images.splice(targetIndex, 0, item);
                    for (let i = 0; i < this.images.length; i++) {
                        let reader = new FileReader();
                        reader.onload = (e) => {
                            this.$refs.image[i].src = reader.result;
                        };
                        reader.readAsDataURL(this.images[i]);
                    }
                },
            },
            computed: {},
            watch: {

            },
        })
        new ImageUpload().$mount('#ImageUploadBox');
    </script>
@endsection
