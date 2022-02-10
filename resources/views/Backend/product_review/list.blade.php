@extends('backend.master')

@section('title', '商品主檔 -基本資訊管理')

@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-cube"></i> 商品主檔 - 商品上架審核</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <form role="form" id="select-form" method="GET" action="{{ route('product_review') }}"
                            enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <h5>商品序號</h5>
                                    </div>
                                    <div class="col-sm-8">
                                        <input class="form-control" name="product_no" id="product_no"
                                            value="{{ request()->input('product_no') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="col-sm-3">
                                        <h5>商品名稱</h5>
                                    </div>
                                    <div class="col-sm-8">
                                        <input class="form-control" name="product_name" id="product_name"
                                            value="{{ request()->input('product_name') }}">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="text-right">
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
                            </div>
                        </div>
                        <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                            <thead>
                                <tr>
                                    <th>功能</th>
                                    <th>項次</th>
                                    <th>上架時間</th>
                                    <th>商品序號</th>
                                    <th>商品名稱</th>
                                    <th>庫存類型</th>
                                    <th>供應商</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $key => $val)
                                    <tr>
                                        <td>
                                            {{-- @if ($share_role_auth['auth_update']) --}}
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('product_review.edit', $val->id) }}">審核</a>
                                            {{-- @endif --}}
                                        </td>
                                        <td>{{ $key += 1 }}</td>

                                        <th>{{ $val->start_launched_at }} ~ {{ $val->end_launched_at }}</th>
                                        <td>{{ $val->product_no }}</td>
                                        <td>{{ $val->product_name }}</td>
                                        <td>
                                            @switch($val->stock_type)
                                                @case('A')
                                                    買斷
                                                @break
                                                @case('B')
                                                    寄售
                                                @break
                                                @case('T')
                                                    轉單
                                                @break
                                            @endswitch
                                        </td>
                                        <td>{{$val->supplier_name}}</td>
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

        });
    </script>
@endsection
