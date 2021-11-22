@extends('Backend.master')
@section('title', '分類階層內容管理')
@section('content')
    <style>
        .no-pa {
            padding: 0px;
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
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="col-sm-1 no-pa">
                                <label class="control-label">商品圖檔</label>
                            </div>
                            <div class="col-sm-10">
                                {{-- <label for="exampleInputFile">商品圖檔</label> --}}
                                <p class="help-block">最多上傳15張，每張size不可超過1MB，副檔名須為JPG、JPEG、PNG</p>
                                <input type="file" id="exampleInputFile">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group " style="border-width:1px; border-style:solid;">
                        @for ($i = 0; $i < 3; $i++)
                            <div class="col-sm-3 col-md-2">
                                <div class="thumbnail">
                                    <img src="https://testucareupload.s3.ap-northeast-2.amazonaws.com/photo/1/eiOwWLuOsaE6iW9rWgn10tgu4hUPSTnroJx58gZg.jpg"
                                        alt="">
                                </div>
                            </div>
                        @endfor
                    </div>
                    <hr>
                    <div class="" id="SkuComponent">
                        <button @click="testdescartes" type="button">測試Descartes function</button>
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
                                            <th><button class="btn btn-primary btn-sm" type="button"
                                                    @click="AddSpecToSkuList('1')">新增項目</button></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(spec_1, spec_1_key) in SpecList.spec_1">
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
                                                        @click="DelSpecList(spec_1_key)">刪除</button>
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
                                        <tr v-for="(spec_2, spec_2_key) in SpecList.spec_2">
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
                                                        @click="DelSpecList(spec_2_key)">刪除</button>
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
                                    <td>米色</td>
                                    <td>S</td>
                                    <td><input class="form-control" id="keyword" value="" readonly></td>
                                    <td><input class="form-control" id="keyword" value=""></td>
                                    <td><input class="form-control" id="keyword" value=""></td>
                                    <td><input class="form-control" id="keyword" value=""></td>
                                    <td><input class="form-control" id="keyword" value=""></td>
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
                        });
                    } else if (spec_type == '2') {
                        this.SpecList.spec_2.length;
                        this.SpecList.spec_2.push({
                            name: '',
                            sort: this.SpecList.spec_2.length,
                        });
                    }
                },
                DelSpecList(key) {
                    console.log(key);
                },
                testdescartes() {
                    console.log('Test Descartes');
                    let spac_1 = [];
                    let spac_2 = [];
                
                    this.SpecList.spec_1.map(function(value, key) {
                        spac_1.push(value.name) ;
                    });
                    this.SpecList.spec_2.map(function(value, key) {
                        spac_2.push(value.name) ;
                    });
                    let cartesian =  (...a) => a.reduce((a, b) => a.flatMap(d => b.map(e => [d, e].flat())));
                    let output = cartesian(spac_1,spac_2);
                    console.log(output)
                    return output;
                },
            },
            computed: {
                // descartes() { //笛卡兒積演算法
                //     let arr = [];
                //     // //先將optionsData資料整理成陣列
                //     const check = this.optionsData.map((item, index, array) => {
                //         addindex2 = [];
                //         item.value.map((item2, index2, array2) => {
                //             addindex2.push(index2);
                //         });
                //         arr[index] = addindex2;
                //     });
                //     //執行公式
                //     let res = arr.reduce((a, b) =>
                //         a.map(x => b.map(y => x.concat(y)))
                //         .reduce((a, b) => a.concat(b), []), [
                //             []
                //         ]);
                //     return res;
                // },
            }
        })
        new SkuComponent().$mount('#SkuComponent')
    </script>
@endsection
