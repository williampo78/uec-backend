@extends('backend.master')

@section('title', '商品主檔 -商城資訊管理')

@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-cube"></i> 商品主檔 - 商城資訊管理</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <form id="search-form" class="form-horizontal" method="GET" action="{{ route('product_small') }}">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <label class="control-label">庫存類型</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="stock_type" id="stock_type">
                                            <option value="">全部</option>
                                            <option value="A"
                                                {{ request()->input('stock_type') == 'A' ? 'selected' : '' }}>買斷
                                            </option>
                                            <option value="B"
                                                {{ request()->input('stock_type') == 'B' ? 'selected' : '' }}>寄售</option>
                                            <option value="T"
                                                {{ request()->input('stock_type') == 'T' ? 'selected' : '' }}>轉單
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <label class="control-label">商品序號</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="product_no" id="product_no"
                                            value="{{ request()->input('product_no') }}">
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <label class="control-label">供應商</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="supplier_id" id="supplier_id">
                                            <option value="">全部</option>
                                            @foreach ($supplier as $val)
                                                <option value="{{ $val->id }}"
                                                    {{ request()->input('supplier_id') == $val->id ? 'selected' : '' }}>
                                                    {{ $val->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <label class="control-label">商品通路</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="selling_channel" id="selling_channel">
                                            <option value="">全部</option>
                                            <option value="EC"
                                                {{ request()->input('selling_channel') == 'EC' ? 'selected' : '' }}>網路獨賣
                                            </option>
                                            <option value="STORE"
                                                {{ request()->input('selling_channel') == 'STORE' ? 'selected' : '' }}>
                                                門市限定
                                            </option>
                                            <option value="WHOLE"
                                                {{ request()->input('selling_channel') == 'WHOLE' ? 'selected' : '' }}>
                                                全通路
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <label class="control-label">商品名稱</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="product_name" id="product_name"
                                            value="{{ request()->input('product_name') }}">
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <label class="control-label">前台分類</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="category_id" id="category_id">
                                            <option value="">全部</option>
                                            @foreach ($pos as $val)
                                                <option value="{{ $val->id }}"
                                                    {{ request()->input('category_id') == $val->id ? 'selected' : '' }}>
                                                    {{ $val->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <label class="control-label">配送方式</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="lgst_method" id="lgst_method">
                                            <option value="">全部</option>
                                            <option value="HOME"
                                                {{ request()->input('lgst_method') == 'HOME' ? 'selected' : '' }}>宅配
                                            </option>
                                            <option value="FAMILY"
                                                {{ request()->input('lgst_method') == 'FAMILY' ? 'selected' : '' }}>全家取貨
                                            </option>
                                            <option value="Store"
                                                {{ request()->input('lgst_method') == 'STORE' ? 'selected' : '' }}>門市取貨
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <label class="control-label">商品類型</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="product_type" id="product_type">
                                            <option value="">全部</option>
                                            <option value="N"
                                                {{ request()->input('product_type') == 'N' ? 'selected' : '' }}>一般品
                                            </option>
                                            <option value="G"
                                                {{ request()->input('product_type') == 'G' ? 'selected' : '' }}>贈品
                                            </option>
                                            <option value="A"
                                                {{ request()->input('product_type') == 'A' ? 'selected' : '' }}>加購品
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <label class="control-label">上架狀態</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="approval_status" id="approval_status">
                                            <option value="">全部</option>
                                            <option value="NA"
                                                {{ request()->input('approval_status') == 'NA' ? 'selected' : '' }}>
                                                未設定
                                            </option>
                                            <option value="REVIEWING"
                                                {{ request()->input('approval_status') == 'REVIEWING' ? 'selected' : '' }}>
                                                上架申請
                                            </option>
                                            <option value="APPROVED_STATUS_ON"
                                                {{ request()->input('approval_status') == 'APPROVED_STATUS_ON' ? 'selected' : '' }}>
                                                商品上架
                                            </option>
                                            <option value="REJECTED"
                                                {{ request()->input('approval_status') == 'REJECTED' ? 'selected' : '' }}>
                                                上架駁回
                                            </option>
                                            <option value="APPROVED_STATUS_OFF"
                                                {{ request()->input('approval_status') == 'APPROVED_STATUS_OFF' ? 'selected' : '' }}>
                                                商品下架
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <label class="control-label">上架時間</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class='input-group date' id='datetimepicker'>
                                                <input type="text" class="form-control" name="start_launched_at_start"
                                                    id="start_launched_at_start"
                                                    value="{{ request()->input('start_launched_at_start') }}" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1 text-center">
                                        <label class="control-label">～</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class='input-group date' id='datetimepicker2'>
                                                <input type="text" class="form-control" name="start_launched_at_end"
                                                    id="start_launched_at_end"
                                                    value="{{ request()->input('start_launched_at_end') }}" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <label class="control-label">查詢筆數上限</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" name="limit" id="limit " readonly
                                            value="500">
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9 text-right">
                                        @if ($share_role_auth['auth_query'])
                                            <button class="btn btn-warning">
                                                <i class="fa fa-search"></i> 查詢
                                            </button>

                                            <button type="button" class="btn btn-danger" id="btn-reset">
                                                <i class="fa fa-eraser"></i> 清除
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="row">
                        </div>
                        {{-- <hr> --}}
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
                                            <a class="btn btn-info btn-sm"
                                                href="{{ route('product_small.show', $val->id) }}">
                                                <i class="fa fa-search"></i></a>
                                            <a class="btn btn-info btn-sm"
                                                href="{{ route('product_small.edit', $val->id) }}">編輯</a>
                                        </td>
                                        <td>{{ $key += 1 }}</td>
                                        <td>{{ $val->supplier_name }}</td>
                                        <td>{{ $val->product_no }}</td>
                                        <td>{{ $val->product_name }}</td>
                                        <td>{{ $val->selling_price }}</td>
                                        <td>{{ $val->item_cost }}</td>
                                        <td>{{ $val->gross_margin }}</td>
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
                                            {{ $val->launched_status }}
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
        $(document).ready(function() {
            $("#stock_type").select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇"
            });
            $("#selling_channel").select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇"
            });
            $("#lgst_method").select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇"
            });
            $("#product_type").select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇"
            });
            $("#supplier_id").select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇"
            });
            $("#category_id").select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇"
            });
            $('#approval_status').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇"
            });
            $('#datetimepicker').datetimepicker({
                format: 'YYYY-MM-DD',
            });
            $('#datetimepicker2').datetimepicker({
                format: 'YYYY-MM-DD',
            });

            // 重置搜尋表單
            $('#btn-reset').on('click', function() {
                $('#search-form').find(':text:not("#limit"), select').val('');
                $('#stock_type, #selling_channel, #lgst_method, #product_type, #supplier_id, #category_id, #approval_status')
                    .trigger('change');
            });
        });
    </script>
@endsection
