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
                                    <div class="col-sm-2 ">
                                        <label class="control-label ">商品序號</label><span class="redtext">*</span>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="product_no"
                                            value="{{ $products->product_no }}" readonly>
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
                                        <input class="form-control" name="product_no"
                                            value="{{ $products->supplier_name }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class=" form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">商品名稱<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="product_name"
                                            value="{{ $products->product_name }}">
                                        {{-- <span class="">123</span> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">
                                            <a href="{{ route('products.show', $products->id) }}"
                                                target="_blank">查看基本資訊</a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">前台分類<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-11">
                                        <button class="btn btn-large btn-warning" type="button">新增分類</button>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="col-sm-11">名稱</th>
                                                <th class="col-sm-1">功能</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="vertical-align:middle">
                                                    <i class="fa fa-list"></i>
                                                    名稱
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger"
                                                        @click="DelCategory(level_1_obj.id)"
                                                        v-show="RoleAuthJson.auth_delete">刪除</button>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">關聯關鍵字</label>
                                    </div>
                                    <div class="col-sm-11">
                                        <input class="form-control" name="keywords" value="{{ $products->keywords }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">關聯性商品<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-10">
                                        <button class="btn btn-large btn-warning" type="button">新增商品</button>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="col-sm-11">名稱</th>
                                                <th class="col-sm-1">功能</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="vertical-align:middle">
                                                    <i class="fa fa-list"></i>
                                                    名稱
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger"
                                                        @click="DelCategory(level_1_obj.id)"
                                                        v-show="RoleAuthJson.auth_delete">刪除</button>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">每單限購數量<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input class="form-control" name="order_limited_qty"
                                            value="{{ $products->order_limited_qty }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">促銷小標</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input class="form-control" name="promotion_desc"
                                            value="{{ $products->promotion_desc }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">促銷小標</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class='input-group date'>
                                            <input type='text' class="form-control" name="promotion_start_at"
                                                id="promotion_start_at" value="{{ $products->promotion_start_at }}"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-1" style="padding: 0px;width: 2%;">
                                        <label class="control-label">~</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class='input-group date'>
                                            <input type='text' class="form-control" name="promotion_end_at"
                                                id="promotion_end_at" value="{{ $products->promotion_end_at }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            
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
        $(document).ready(function() {
            $('#promotion_start_at').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss',
            });
            $('#promotion_end_at').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss',
            });


        });
    </script>
@endsection
