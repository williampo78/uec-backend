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
                    <hr>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="col-sm-1 no-pa">
                                <label class="control-label"></label>
                            </div>
                            <div class="col-sm-2">
                                <label class="radio-inline">
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1"> 單規格
                                </label>
                            </div>
                            <div class="col-sm-2">
                                <label class="radio-inline">
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"> 一維多規格
                                </label>
                            </div>
                            <div class="col-sm-2">
                                <label class="radio-inline">
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3"> 二維多規格
                                </label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-6">
                                <table class="table table-striped table-bordered table-hover" >
                                    <thead>
                                        <tr>
                                            <th>功能</th>
                                        </tr>
                                    </thead>
                                    <tbody>
        
                                        {{-- {{$category_products_list}} --}}
                                        <tr>
                                            <td>A</td>        
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-6">
                                
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
    </script>
@endsection
