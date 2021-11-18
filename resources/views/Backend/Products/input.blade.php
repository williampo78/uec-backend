@extends('Backend.master')
@section('title', '分類階層內容管理')
@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i>分類階層內容編輯</h1>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">請輸入下列欄位資料</div>
            <div class="panel-body" id="category_hierarchy_content_input">
                <form class="form-horizontal" role="form" id="new-form" method="POST"
                    action="{{ route('products.store') }}" enctype="multipart/form-data" novalidate="novalidate">
                    @csrf
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <div class="col-sm-2">
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
                            <div class="col-sm-2">
                                <label class="control-label">商品序號</label><span class="redtext">*</span>
                            </div>
                            <div class="col-sm-9">
                                <input class="form-control" name="keyword" id="keyword" value="" readonly>
                            </div>

                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <div class="col-sm-2">
                                <label class="control-label">供應商<span class="redtext">*</span></label>
                            </div>
                            <div class="col-sm-9">
                                <select class="form-control js-select2" name="active" id="active">
                                    <option value="">無</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-2">
                                <label class="control-label">商品名稱<span class="redtext">*</span></label>
                            </div>
                            <div class="col-sm-9">
                                <input class="form-control" name="keyword" id="keyword" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <div class="col-sm-2">
                                <label class="control-label">課稅別<span class="redtext">*</span></label>
                            </div>
                            <div class="col-sm-9">
                                <select class="form-control js-select2" name="active" id="active">
                                    <option value="">無</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-2">
                                <label class="control-label">POS分類<span class="redtext">*</span></label>
                            </div>
                            <div class="col-sm-9">
                                <input class="form-control" name="keyword" id="keyword" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <div class="col-sm-2">
                                <label class="control-label">品牌<span class="redtext">*</span></label>
                            </div>
                            <div class="col-sm-9">
                                <select class="form-control js-select2" name="active" id="active">
                                    <option value="">無</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-2">
                                <label class="control-label">商品型號</label>
                            </div>
                            <div class="col-sm-9">
                                <input class="form-control" name="keyword" id="keyword" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <div class="col-sm-2">
                                <label class="control-label">商品通路<span class="redtext">*</span></label>
                            </div>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio4" value="option4"> 宅配
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-2">
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
                            <div class="col-sm-2">
                                <label class="control-label">單位<span class="redtext">*</span></label>
                            </div>
                            <div class="col-sm-9">
                                <input class="form-control" name="keyword" id="keyword" value="">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-2">
                                <label class="control-label">最小採購量</label>
                            </div>
                            <div class="col-sm-9">
                                <input class="form-control" name="keyword" id="keyword" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <div class="col-sm-2">
                                <label class="control-label">效期控管<span class="redtext">*</span></label>
                            </div>
                            <div class="col-sm-2">
                                <label class="radio-inline">
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1"> 無
                                </label>
                            </div>
                            <div class="col-sm-3">
                                <label class="radio-inline">
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"> 有，天數
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <input class="form-control" name="keyword" id="keyword" value="">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-2">
                                <label class="control-label">允收期(天)</label>
                            </div>
                            <div class="col-sm-3">
                                <input class="form-control" name="keyword" id="keyword" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <div class="col-sm-2">
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
                            <div class="col-sm-2">
                                <label class="control-label">重量(公克)<span class="redtext">*</span></label>
                            </div>
                            <div class="col-sm-3">
                                <input class="form-control" name="keyword" id="keyword" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <div class="col-sm-2">
                                <label class="control-label">成本(含稅)<span class="redtext">*</span></label>
                            </div>
                            <div class="col-sm-9">
                                <input class="form-control" name="keyword" id="keyword" value="" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-2">
                                <label class="control-label">毛利(%)<span class="redtext">*</span></label>
                            </div>
                            <div class="col-sm-9">
                                <input class="form-control" name="keyword" id="keyword" value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <div class="col-sm-2">
                                <label class="control-label">採購人員<span class="redtext">*</span></label>
                            </div>
                            <div class="col-sm-9">
                                <input class="form-control" name="keyword" id="keyword" value="" readonly>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-2">
                                <label class="control-label">轉單審核人員<span class="redtext">*</span></label>
                            </div>
                            <div class="col-sm-9">
                                <input class="form-control" name="keyword" id="keyword" value="" readonly>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script>
        // var products = Vue.extend({
        //     data: function() {
        //         return {}
        //     },
        //     methods: {},
        //     mounted: function() {},
        //     computed: {},

        // });

        // new products().$mount('#category_hierarchy_content_input');
    </script>
@endsection
