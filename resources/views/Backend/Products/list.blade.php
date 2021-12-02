@extends('Backend.master')

@section('title', '商品主檔 -基本資訊管理')

@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-cube"></i> 商品主檔 - 基本資訊管理</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <form role="form" id="select-form" method="GET" action="{{ route('products') }}"
                            enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-2">
                                        <h5>庫存類型</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="active" id="active">
                                            <option value=''>無</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="col-sm-2">
                                        <h5>商品序號</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="keyword" id="keyword"
                                            value="{{ request()->input('keyword') }}">
                                    </div>

                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-2">
                                        <h5>商品通路</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="active" id="active">
                                            <option value=''>無</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="col-sm-2">
                                        <h5>商品名稱</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="keyword" id="keyword"
                                            value="{{ request()->input('keyword') }}">
                                    </div>

                                </div>

                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-2">
                                        <h5>配送方式</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="active" id="active">
                                            <option value=''>無</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="col-sm-2">
                                        <h5>商品類型</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="active" id="active">
                                            <option value=''>無</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-2">
                                        <h5>上架時間起</h5>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_select_start_date">
                                            <div class='input-group date' id='datetimepicker'>
                                                <input type='text' class="form-control" name="select_start_date"
                                                    id="select_start_date"
                                                    value="{{ $data['getData']['select_start_date'] ?? '' }}" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <h5>～</h5>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_select_end_date">
                                            <div class='input-group date' id='datetimepicker2'>
                                                <input type='text' class="form-control" name="select_end_date"
                                                    id="select_end_date"
                                                    value="{{ $data['getData']['select_end_date'] ?? '' }}" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-6">
                                    <div class="col-sm-2">
                                        <h5>查詢筆數上限</h5>
                                    </div>
                                    <div class="col-sm-2">
                                        <input class="form-control" name="keyword" id="keyword" readonly value="500">
                                    </div>
                                    <div class="col-sm-7 text-right">
                                        <button class="btn btn-warning"><i class="fa fa-search"></i> 查詢</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-2">
                                <a class="btn btn-block btn-warning btn-sm" href="{{ route('products.create') }}"><i
                                        class="fa fa-plus"></i> 創建</a>
                            </div>
                        </div>
                        <hr>
                        <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                            <thead>
                                <tr>
                                    <th>功能</th>
                                    <th>項次</th>
                                    <th>供應商</th>
                                    <th>商品序號</th>
                                    <th>商品名稱</th>
                                    <th>售價(含稅)</th>
                                    <th>成本(含稅)</th>
                                    <th>毛利(%)</th>
                                    <th>商品類型</th>
                                    <th>建檔日期</th>
                                    <th>上架狀態</th>
                                    <th>上架時間起</th>
                                    <th>上架時間訖</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $key => $val)
                                    <tr>
                                        <td>
                                            <button class="btn btn-info btn-sm toggle-show-model" data-toggle="modal"
                                                data-target="#row_detail"><i class="fa fa-search"></i>
                                            </button>
                                            <a class="btn btn-info btn-sm"
                                                href="{{ route('web_category_products.edit', '1') }}">修改</a>
                                        </td>
                                        <td>{{ $key += 1 }}</td>
                                        <td>{{ $val->supplier_id }}</td>
                                        <td>{{ $val->product_no }}</td>
                                        <td>{{ $val->product_name }}</td>
                                        <td>{{ $val->selling_price }}</td>
                                        <td>等待確認</td>
                                        <td>等待確認</td>
                                        <td>
                                            @switch($val->product_type)
                                                @case('N')
                                                    一般品
                                                @break
                                                @case('G')
                                                    贈品
                                                @break
                                                @case('A')
                                                    加購品
                                                @break
                                            @endswitch
                                        </td>
                                        <td>
                                            {{ $val->created_at }}
                                        </td>
                                        <td>
                                            @switch($val->approval_status)
                                                @case('NA')
                                                    無送審記錄
                                                @break
                                                @case('REVIEWING')
                                                    簽核中
                                                @break
                                                @case('APPROVED')
                                                    已核准
                                                @break
                                                @case('REJECTED')
                                                    已駁回
                                                @break
                                            @endswitch
                                        </td>
                                        <td>{{ $val->start_launched_at }}</td>
                                        <td>{{ $val->end_launched_at }}</td>
                                    </tr>
                                @endforeach
                                {{-- {{$category_products_list}} --}}

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
    </script>
@endsection
