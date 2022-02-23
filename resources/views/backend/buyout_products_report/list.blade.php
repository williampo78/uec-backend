@extends('backend.master')
@section('title', '買斷商品對帳單')
@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-arrow-right-to-bracket"></i> 買斷商品對帳單</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <form role="form" class="form-horizontal" id="select-form" method="GET" action=""
                              enctype="multipart/form-data">
                            <br>
                            <div class="row">

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3"><label class="control-label">進貨日期</label></div>
                                        <div class="col-sm-4">
                                            <div class="form-group">

                                                <div class="input-group date" id="trade_date_start_box">
                                                    <input type="text" class="form-control" name="trade_date_start"
                                                           id="trade_date_start"
                                                           value="{{ request()->input('trade_date_start') }}">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            <div class="form-group">
                                                <label class="control-label">　～</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <div class="input-group date" id="trade_date_end_box">
                                                    <input type="text" class="form-control" name="trade_date_end"
                                                           id="trade_date_end"
                                                           value="{{ request()->input('trade_date_end') }}">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- row 1 start --}}
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3"><label class="control-label"> 供應商 </label></div>
                                        <div class="col-sm-9">
                                            <div class='input-group' id='supplier_deliver_date_dp'>
                                                <select class="form-control js-select2-department" name="supplier"
                                                        id="supplier" value="{{ request()->input('company_number') }}">
                                                    <option value=""></option>
                                                    @foreach ($supplier as $v)
                                                        <option value='{{ $v['id'] }}'
                                                            {{ request()->input('supplier') && $v['id'] == request()->input('supplier') ? 'selected' : '' }}>
                                                            {{ $v['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-4"><label class="control-label">採購單號</label></div>
                                        <div class="col-sm-8">
                                            <div class='input-group'>
                                                <input class="form-control" name="order_supplier_number"
                                                       id="order_supplier_number"
                                                       value="{{ request()->input('company_number') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- row 1 end --}}
                            {{-- row 2 start --}}
                            <div class="row">

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3"><label class="control-label">POS品號</label></div>
                                        <div class="col-sm-4">
                                            <div class="form-group">

                                                <input class="form-control" name="POS_start_number"
                                                       id="POS_start_number"
                                                       value="{{ request()->input('POS_start_number') }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            <div class="form-group">
                                                <label class="control-label">　～</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <input class="form-control" name="POS_end_number" id="POS_end_number"
                                                       value="{{ request()->input('POS_end_number') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-4"><label class="control-label">商品名稱</label></div>
                                        <div class="col-sm-8">
                                            <div class='input-group'>
                                                <input class="form-control" name="product_name" id="product_name"
                                                       value="{{ request()->input('product_name') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 text-right">
                                    <div class="col-sm-9">
                                        <a class="btn btn-info" target="_blank" href='{{ $excel_url  ?? ''}} '
                                            {{ $excel_url ?? 'disabled' }}>
                                            <i class="fa-solid fa-file-excel"></i>
                                            匯出EXCEL</a>
                                    </div>
                                    <div class="col-sm-3">
                                        <button class="btn btn-warning"><i class="fa-solid fa-magnifying-glass"></i> 查詢</button>
                                    </div>
                                </div>
                                {{-- row 2 end --}}
                            </div>

                        </form>
                    </div>

                </div>
                <!-- Table list -->
                <div style="overflow-x: scroll;">
                    <div class="" style="width: 120%">

                        <table class="table table-striped table-bordered table-hover" style="width:100%"
                               id="table_list">
                            <thead>
                            <tr>
                                <th>項次</th>
                                <th>供應商</th>
                                <th>進貨日期</th>
                                <th>進貨單號</th>
                                <th>採購單稅別</th>
                                <th>Item編號</th>
                                <th>POS品號</th>
                                <th>商品名稱</th>
                                <th>規格一</th>
                                <th>規格二</th>
                                <th>單價</th>
                                <th>數量</th>
                                <th>未稅金額</th>
                                <th>稅額</th>
                                <th>含稅金額</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (isset($buy_out_products))
                                @foreach ($buy_out_products as $key => $obj)
                                    <tr>
                                        <td>{{ $key += 1 }}</td>
                                        <td>{{ $obj->supplier_name }}</td>
                                        <td>{{ $obj->trade_date }}</td>
                                        <td>{{ $obj->number }}</td>
                                        <td>
                                            @if (isset(config('uec.tax_option')[$obj->order_supplier_tax]))
                                                {{ config('uec.tax_option')[$obj->order_supplier_tax] }}
                                            @else
                                                error
                                            @endif
                                        </td>
                                        <td>{{ $obj->item_no }}</td>
                                        <td>{{ $obj->pos_item_no }}</td>
                                        <td>{{ $obj->product_name }}</td>
                                        <td>{{ $obj->spec_1_value }}</td>
                                        <td>{{ $obj->spec_2_value }}</td>
                                        <td>{{ $obj->item_price }}</td>
                                        <td>{{ $obj->item_qty }}</td>
                                        <td>{{ $obj->detail_subtotal_nontax_price }}</td>
                                        <td>{{ $obj->detail_subtotal_tax_price }}</td>
                                        <td>{{ $obj->detail_original_subtotal_price }}</td>
                                    </tr>
                                @endforeach

                            @endif
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
        $(document).ready(function () {

            $('#supplier').select2();
            $('#trade_date_start_box').datetimepicker({
                format: 'YYYY-MM-DD',
            });
            $('#trade_date_end_box').datetimepicker({
                format: 'YYYY-MM-DD',
            });
            $('#invoice_date_box').datetimepicker({
                format: 'YYYY-MM-DD',
            });
            $("#select-form").validate({
                // debug: true,
                submitHandler: function (form) {
                    form.submit();
                },
                rules: {
                    trade_date_start: {
                        required: true,
                        monthIntervalVerify: {
                            param: function () {
                                let obj = {
                                    start_time: $('#trade_date_start').val(),
                                    end_time: $('#trade_date_end').val(),
                                    month_num: 3,
                                }
                                return obj;
                            },
                            depends: function (element) {
                                return $('#trade_date_start').val() && $('#trade_date_end').val();
                            },
                        },
                    },
                    trade_date_end: {
                        required: true,
                        greaterSameThan: function () {
                            return $('#trade_date_start').val();
                        },
                    },

                },
                messages: {
                    trade_date_end: {
                        greaterSameThan: "進貨結束時間必須大於等於進貨開始時間",
                    },
                },
                errorClass: "help-block",
                errorElement: "span",
                errorPlacement: function (error, element) {
                    if (element.parent('.input-group').length || element.is(':radio')) {
                        error.insertAfter(element.parent());
                        return;
                    }
                    if (element.is('select')) {
                        element.parent().append(error);
                        return;
                    }

                    error.insertAfter(element);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).closest(".form-group").addClass("has-error");
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
                success: function (label, element) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
            });
        });
    </script>
@endsection
