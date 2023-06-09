@extends('backend.layouts.master')

@section('title', '進銷存彙總表')

@section('content')
    <!--列表-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-cubes"></i> 進銷存彙總表</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <form id="search-form" class="form-horizontal" method="GET" action="">
                            <div class="row">
                                <div class="col-sm-1 text-right">
                                    <h5>月份 <span class="text-danger">*</span></h5>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <div class="input-group" id="smonth_flatpickr">
                                            <input type="text" class="form-control" name="smonth" id="smonth"
                                                value="{{ request()->input('smonth') }}" autocomplete="off" data-input />
                                            <span class="input-group-btn" data-toggle>
                                                <button class="btn btn-default" type="button">
                                                    <i class="fa-solid fa-calendar-days"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1 text-right">
                                    <h5>Item編號</h5>
                                </div>
                                <div class="col-sm-4">
                                    <div class='input-group'>
                                        <input class="form-control" name="item_id_start" id="item_id_start"
                                            value="{{ request()->input('item_id_start') }}">
                                        <span class="input-group-addon">~</span>
                                        <input class="form-control" name="item_id_end" id="item_id_end"
                                            value="{{ request()->input('item_id_end') }}">
                                    </div>
                                </div>
                                <div class="col-sm-1 text-right">
                                    <h5>商品名稱</h5>
                                </div>
                                <div class="col-sm-3">
                                    <input class="form-control" name="product_name" id="product_name" placeholder="模糊查詢"
                                        value="{{ request()->input('product_name') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-9"></div>
                                @if ($share_role_auth['auth_create'])
                                    <div class="col-sm-1">
                                        <button data-url="" class="btn btn-danger" id="btn-excute" type="button"
                                            {{ $excel_url ?? 'disabled' }}>
                                            <i class="fa-solid fa-chart-bar"></i>
                                            資料滾算
                                        </button>
                                    </div>
                                @endif
                                @if ($share_role_auth['auth_export'])
                                    <div class="col-sm-1">
                                        <a class="btn btn-primary" target="_blank" href='{{ $excel_url ?? '' }} '
                                            {{ $excel_url ?? 'disabled' }}>
                                            <i class="fa-solid fa-file-excel"></i>
                                            匯出EXCEL
                                        </a>
                                    </div>
                                @endif
                                @if ($share_role_auth['auth_query'])
                                    <div class="col-sm-1">
                                        <button class="btn btn-warning" id="btn-search" type="submit">
                                            <i class="fa-solid fa-magnifying-glass"></i> 查詢
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                    @if (isset($sum[0]))
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-sm-1 text-right">
                                    <h5>月份</h5>
                                </div>
                                <div class="col-sm-1">
                                    <h4>{{ $sum[0]['month'] }}</h4>
                                </div>
                                <div class="col-sm-1 text-right">
                                    <h5>期初數量</h5>
                                </div>
                                <div class="col-sm-1">
                                    <h4>{{ number_format($sum[0]['begin_qty']) }}</h4>
                                </div>
                                <div class="col-sm-1 text-right">
                                    <h5>期末數量</h5>
                                </div>
                                <div class="col-sm-1">
                                    <h4>{{ number_format($sum[0]['end_qty']) }}</h4>
                                </div>
                                <div class="col-sm-6">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="panel-body">
                    <div style="overflow-x: scroll;">
                        <div style="width: 140%;">
                            <table class="table table-striped table-bordered table-hover" style="width:100%"
                                id="table_list">
                                <thead>
                                    <tr>
                                        <th>Item編號</th>
                                        <th>商品名稱</th>
                                        <th>規格1</th>
                                        <th>規格2</th>
                                        <th>期初數量</th>
                                        <th>期初金額</th>
                                        <th>單位成本</th>
                                        <th>進貨數量</th>
                                        <th>進貨金額</th>
                                        <th>退貨數量</th>
                                        <th>退貨金額</th>
                                        <th>銷貨數量</th>
                                        <th>銷貨金額</th>
                                        <th>銷退數量</th>
                                        <th>銷退金額</th>
                                        <th>盤差數量</th>
                                        <th>盤差金額</th>
                                        <th>調撥數量</th>
                                        <th>調撥金額</th>
                                        <th>期末數量</th>
                                        <th>期末金額</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($info as $item)
                                        <tr>
                                            <td align="center">{{ $item->item_no }}</td>
                                            <td nowrap="nowrap">{{ $item->product_name }}</td>
                                            <td nowrap="nowrap">{{ $item->spec_1_value }}</td>
                                            <td nowrap="nowrap">{{ $item->spec_2_value }}</td>
                                            <td align="right">{{ $item->begin_qty }}</td>
                                            {{--期初金額--}}
                                            <td align="right">{{ $item->begin_amount_display }}</td>
                                            {{--單位成本--}}
                                            <td align="right">{{ $item->item_cost_display }}</td>
                                            <td align="right">{{ $item->rcv_qty }}</td>
                                            <td align="right">{{ number_format($item->rcv_amount) }}</td>
                                            <td align="right">{{ $item->rtv_qty }}</td>
                                            <td align="right">{{ number_format($item->rtv_amount) }}</td>
                                            <td align="right">{{ $item->sales_qty }}</td>
                                            {{--銷貨金額--}}
                                            <td align="right">{{ $item->sales_amount_display }}</td>
                                            <td align="right">{{ $item->sales_return_qty }}</td>
                                            {{--銷退金額--}}
                                            <td align="right">{{ $item->sales_return_amount_display }}</td>
                                            <td align="right">{{ $item->adj_qty }}</td>
                                            <td align="right">{{ $item->adj_amount }}</td>
                                            <td align="right">{{ $item->shift_qty }}</td>
                                            <td align="right">{{ $item->shift_amount }}</td>
                                            <td align="right">{{ $item->end_qty }}</td>
                                            {{--期末金額--}}
                                            <td align="right">{{ $item->end_amount_display }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            flatpickr("#smonth_flatpickr", {
                plugins: [
                    new flatpickr.monthSelectPlugin({
                        dateFormat: "Y-m",
                    }),
                ],
            });

            // 驗證表單
            $("#search-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#btn-search').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    smonth: {
                        required: true
                    }
                },
                messages: {
                    smonth: {
                        required: '必填',
                    }
                },
                errorClass: "help-block",
                errorElement: "span",
                errorPlacement: function(error, element) {
                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                        return;
                    }

                    if (element.is('select')) {
                        element.parent().append(error);
                        return;
                    }

                    error.insertAfter(element);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).closest(".form-group").addClass("has-error");
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
                success: function(label, element) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
            });
            //資料滾算
            $('#btn-excute').click(function() {
                var smonth = $("#smonth").val(); //01
                if (smonth == '') {
                    swal('系統訊息', '月份必填', 'error');
                } else {
                    swal({
                        title: "確定要滾算進耗存資料?",
                        text: "年月：" + smonth,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "確認",
                        closeOnConfirm: false,
                        cancelButtonText: "取消",
                        showLoaderOnConfirm: true
                    }, function() {
                        $.ajax({
                            url: "/backend/summary_stock/ajax",
                            type: "POST",
                            data: {
                                "smonth": smonth,
                                _token: '{{ csrf_token() }}'
                            },
                            async: true,
                        }).done(function(data) {
                            if (data.alert) {
                                swal({
                                    title: "已有" + smonth + "的滾算紀錄，確定要重新滾算?",
                                    text: "年月：" + smonth,
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonClass: "btn-danger",
                                    confirmButtonText: "確認",
                                    closeOnConfirm: false,
                                    cancelButtonText: "取消",
                                    showLoaderOnConfirm: true
                                }, function() {
                                    $.ajax({
                                        url: "/backend/summary_stock/ajax",
                                        type: "POST",
                                        data: {
                                            "smonth": smonth,
                                            _token: '{{ csrf_token() }}'
                                        },
                                        async: true,
                                    }).done(function(data) {
                                        swal(data.message, data.results, (
                                            data.status ?
                                            'success' : 'error'));
                                    });
                                });
                            } else {
                                swal(data.message, data.results, (data.status ? 'success' :
                                    'error'));

                                /*
                                $.ajax({
                                    url: "/backend/summary_stock/ajax",
                                    type: "POST",
                                    data: {"smonth": smonth, _token: '{{ csrf_token() }}'},
                                    async: false,
                                }).done(function (data) {
                                    console.log("A3:");
                                    console.log(data);
                                    swal(data.message, data.results, (data.status ? 'success' : 'error'));
                                });
                                */
                            }
                        });
                    });
                }
            });
        })
    </script>
@endsection
