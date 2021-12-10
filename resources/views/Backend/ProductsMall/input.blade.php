@extends('Backend.master')
@section('title', '商品主檔 - 新增商城資訊')
@section('content')
    <style>

       

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
                <h1 class="page-header"><i class="fa fa-list"></i>商品主檔 - 新增商城資訊</h1>
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
                                            <option value=""></option>
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
                                            <option value=""></option>
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
                                          <option value=""></option>
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
                    {{-- 二維多規格結束 --}}
                    <button class="btn btn-large btn-primary" type="button" id="save_data">儲存</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
    </script>
@endsection
