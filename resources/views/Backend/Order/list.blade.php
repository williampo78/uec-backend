@extends('Backend.master')

@section('title', '訂單管理')

@section('style')
    <style>
        .modal-dialog {
            max-width: 100%;
        }

        .modal-dialog .modal-body .panel {
            margin: 0;
            border-radius: 0;
        }

        .modal-dialog .modal-body .panel .panel-heading {
            height: 4rem;
        }

        .no-border-bottom {
            border-bottom: 0;
        }

        .amount-panel .row {
            padding: 1.5rem;
        }

        .tab-content {
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            padding: 30px;
        }

    </style>
@endsection

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i>訂單管理</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕 -->
                    <div class="panel-heading">
                        <form id="search-form" class="form-horizontal" method="GET" action="">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-3 text-right">
                                            <label class="control-label">訂單時間</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <div class='input-group date' id='datetimepicker_ordered_date_start'>
                                                    <input type='text'
                                                        class="form-control datetimepicker-input search-limit-group"
                                                        data-target="#datetimepicker_ordered_date_start"
                                                        name="ordered_date_start" id="ordered_date_start"
                                                        value="{{ request()->input('ordered_date_start') }}"
                                                        autocomplete="off" />
                                                    <span class="input-group-addon"
                                                        data-target="#datetimepicker_ordered_date_start"
                                                        data-toggle="datetimepicker">
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
                                                <div class='input-group date' id='datetimepicker_ordered_date_end'>
                                                    <input type='text'
                                                        class="form-control datetimepicker-input search-limit-group"
                                                        data-target="#datetimepicker_ordered_date_end"
                                                        name="ordered_date_end" id="ordered_date_end"
                                                        value="{{ request()->input('ordered_date_end') }}"
                                                        autocomplete="off" />
                                                    <span class="input-group-addon"
                                                        data-target="#datetimepicker_ordered_date_end"
                                                        data-toggle="datetimepicker">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3 text-right">
                                            <label class="control-label">訂單編號</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input class="form-control search-limit-group" name="order_no" id="order_no"
                                                value="{{ request()->input('order_no') }}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3 text-right">
                                            <label class="control-label">會員帳號</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input class="form-control search-limit-group" name="member_account"
                                                id="member_account" value="{{ request()->input('member_account') }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3 text-right">
                                            <label class="control-label">訂單狀態</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-order-status-code" id="order_status_code"
                                                name="order_status_code">
                                                <option></option>
                                                @if (config()->has('uec.order_status_code_options'))
                                                    @foreach (config('uec.order_status_code_options') as $key => $value)
                                                        <option value='{{ $key }}'
                                                            {{ $key == request()->input('order_status_code') ? 'selected' : '' }}>
                                                            {{ $value }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3 text-right">
                                            <label class="control-label">付款狀態</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-pay-status" id="pay_status"
                                                name="pay_status">
                                                <option></option>
                                                @if (config()->has('uec.order_pay_status_options'))
                                                    @foreach (config('uec.order_pay_status_options') as $key => $value)
                                                        <option value='{{ $key }}'
                                                            {{ $key == request()->input('pay_status') ? 'selected' : '' }}>
                                                            {{ $value }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3 text-right">
                                            <label class="control-label">出貨單狀態</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-shipment-status-code"
                                                id="shipment_status_code" name="shipment_status_code">
                                                <option></option>
                                                @if (config()->has('uec.shipment_status_code_options'))
                                                    @foreach (config('uec.shipment_status_code_options') as $key => $value)
                                                        <option value='{{ $key }}'
                                                            {{ $key == request()->input('shipment_status_code') ? 'selected' : '' }}>
                                                            {{ $value }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3 text-right">
                                            <label class="control-label">商品序號</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input class="form-control" name="product_no" id="product_no"
                                                value="{{ request()->input('product_no') }}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3 text-right">
                                            <label class="control-label">商品名稱</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input class="form-control" name="product_name" id="product_name"
                                                value="{{ request()->input('product_name') }}" placeholder="模糊查詢" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3 text-right">
                                            <label class="control-label">活動名稱</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input class="form-control" name="campaign_name" id="campaign_name"
                                                value="{{ request()->input('campaign_name') }}" placeholder="模糊查詢" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9"></div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9"></div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9 text-right">
                                        {{-- @if ($share_role_auth['auth_export']) --}}
                                        <button class="btn btn-primary"><i class="fa fa-file-excel-o"></i> 匯出EXCEL</button>
                                        {{-- @endif --}}

                                        {{-- @if ($share_role_auth['auth_query']) --}}
                                        <button class="btn btn-warning" id="btn-search"><i class="fa fa-search"></i>
                                            查詢</button>
                                        {{-- @endif --}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                            <table class="table table-striped table-bordered table-hover" style="width:100%"
                                id="table_list">
                                <thead>
                                    <tr role="row">
                                        <th class="col-sm-1 ">功能</th>
                                        <th class="col-sm-1 ">項次</th>
                                        <th class="col-sm-1 ">訂單時間</th>
                                        <th class="col-sm-1 ">訂單編號</th>
                                        <th class="col-sm-1 ">訂單狀態</th>
                                        <th class="col-sm-1 ">付款方式</th>
                                        <th class="col-sm-1 ">物流方式</th>
                                        <th class="col-sm-1 ">出貨單狀態</th>
                                        <th class="col-sm-1 ">結帳金額</th>
                                        <th class="col-sm-1 ">會員帳號</th>
                                        <th class="col-sm-1 ">訂購人</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($orders)
                                        @php
                                            $count = 1;
                                        @endphp
                                        @foreach ($orders as $order)
                                            <tr>
                                                <td>
                                                    {{-- @if ($share_role_auth['auth_query']) --}}
                                                    <button type="button" class="btn btn-info btn-sm order_detail"
                                                        data-order="{{ $order['id'] }}" title="檢視">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                    {{-- @endif --}}
                                                </td>
                                                <td>{{ $count++ }}</td>
                                                <td>{{ $order['ordered_date'] ?? '' }}</td>
                                                <td>{{ $order['order_no'] ?? '' }}</td>
                                                <td>
                                                    @isset(config('uec.order_status_code_options')[$order['status_code']])
                                                        {{ config('uec.order_status_code_options')[$order['status_code']] }}
                                                    @endisset
                                                </td>
                                                <td>
                                                    @isset(config('uec.order_payment_method_options')[$order['payment_method']])
                                                        {{ config('uec.order_payment_method_options')[$order['payment_method']] }}
                                                    @endisset
                                                </td>
                                                <td>
                                                    @isset(config('uec.order_lgst_method_options')[$order['lgst_method']])
                                                        {{ config('uec.order_lgst_method_options')[$order['lgst_method']] }}
                                                    @endisset
                                                </td>
                                                <td>
                                                    @isset(config('uec.shipment_status_code_options')[$order['shipments'][0]['status_code']])
                                                        {{ config('uec.shipment_status_code_options')[$order['shipments'][0]['status_code']] }}
                                                    @endisset
                                                </td>
                                                <td>{{ $order['paid_amount'] ?? '' }}</td>
                                                <td>{{ $order['member_account'] ?? '' }}</td>
                                                <td>{{ $order['buyer_name'] ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    @endisset
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('Backend.Order.detail')
        <!-- /.modal -->

    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('#datetimepicker_ordered_date_start').datetimepicker({
                format: 'YYYY-MM-DD',
                showClear: true,
            });

            $('#datetimepicker_ordered_date_end').datetimepicker({
                format: 'YYYY-MM-DD',
                showClear: true,
            });

            $("#datetimepicker_ordered_date_start").on("dp.change", function(e) {
                if ($('#ordered_date_end').val()) {
                    $('#datetimepicker_ordered_date_end').datetimepicker('minDate', e.date);
                }
            });

            $("#datetimepicker_ordered_date_end").on("dp.change", function(e) {
                if ($('#ordered_date_start').val()) {
                    $('#datetimepicker_ordered_date_start').datetimepicker('maxDate', e.date);
                }
            });

            $('.select2-order-status-code').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '',
            });

            $('.select2-pay-status').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '',
            });

            $('.select2-shipment-status-code').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '',
            });

            // 驗證表單
            $("#search-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#btn-search').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    ordered_date_start: {
                        require_from_group: [1, ".search-limit-group"],
                    },
                    ordered_date_end: {
                        require_from_group: [1, ".search-limit-group"],
                    },
                    order_no: {
                        require_from_group: [1, ".search-limit-group"],
                    },
                    member_account: {
                        require_from_group: [1, ".search-limit-group"],
                    },
                },
                messages: {
                    ordered_date_start: {
                        require_from_group: '須指定﹝訂單時間﹞起訖、或﹝訂單編號﹞、或﹝會員帳號﹞才可執行查詢!',
                    },
                    ordered_date_end: {
                        require_from_group: '須指定﹝訂單時間﹞起訖、或﹝訂單編號﹞、或﹝會員帳號﹞才可執行查詢!',
                    },
                    order_no: {
                        require_from_group: '須指定﹝訂單時間﹞起訖、或﹝訂單編號﹞、或﹝會員帳號﹞才可執行查詢!',
                    },
                    member_account: {
                        require_from_group: '須指定﹝訂單時間﹞起訖、或﹝訂單編號﹞、或﹝會員帳號﹞才可執行查詢!',
                    },
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

            // let slot_type_option_json = @json(config('uec.ad_slot_type_option'));

            $('#table_list tbody').on('click', '.order_detail', function() {
                let order_id = $(this).attr("data-order");

                axios.post('/backend/order/ajax/detail', {
                        order_id: order_id
                    })
                    .then(function(response) {
                        let order = response.data;
                    console.log(order);
                        $('#modal-order-no').empty().text(order.order_no);
                        $('#modal-ordered-date').empty().text(order.ordered_date);
                        $('#modal-order-status-code').empty().text(order.status_code);
                        $('#modal-payment-method').empty().text(order.payment_method);
                        $('#modal-pay-status').empty().text(order.pay_status);
                        $('#modal-shipping-free-threshold').empty().text(order.shipping_free_threshold);
                        $('#modal-member-account').empty().text(order.member_account);
                        $('#modal-buyer-name').empty().text(order.buyer_name);
                        $('#modal-buyer-email').empty().text(order.buyer_email);
                        $('#modal-receiver-name').empty().text(order.receiver_name);
                        $('#modal-receiver-mobile').empty().text(order.receiver_mobile);
                        $('#modal-receiver-address').empty().text(order.receiver_address);
                        $('#modal-lgst-method').empty().text(order.lgst_method);

                        if (order.shipments) {
                            if (order.shipments[0]) {
                                $('#modal-shipment-status-code').empty().text(order.shipments[0]
                                    .status_code);
                            }
                        }

                        $('#modal-total-amount').empty().text(order.total_amount);
                        $('#modal-cart-campaign-discount').empty().text(order.cart_campaign_discount);
                        $('#modal-point-discount').empty().text(order.point_discount);
                        $('#modal-shipping-fee').empty().text(order.shipping_fee);
                        $('#modal-paid-amount').empty().text(order.paid_amount);

                        if (order.order_details) {
                            $("#tab-order-detail tbody").empty();

                            $.each(order.order_details, function (key, order_detail) {
                                $("#tab-order-detail tbody").append(`
                                    <tr>
                                        <td>${order_detail.seq}</td>
                                        <td>${order_detail.item_no}</td>
                                        <td>${order_detail.product_name}</td>
                                        <td>${order_detail.spec_1_value}</td>
                                        <td>${order_detail.spec_2_value}</td>
                                        <td>${order_detail.selling_price}</td>
                                        <td>${order_detail.unit_price}</td>
                                        <td>${order_detail.qty}</td>
                                        <td>${order_detail.campaign_discount}</td>
                                        <td>${order_detail.subtotal}</td>
                                        <td>${order_detail.point_discount}</td>
                                        <td>${order_detail.record_identity}</td>
                                        <td>${order_detail.package_no}</td>
                                        <td>${order_detail.returned_qty}</td>
                                        <td>${order_detail.returned_campaign_discount}</td>
                                        <td>${order_detail.returned_subtotal}</td>
                                        <td>${order_detail.returned_point_discount}</td>
                                    </tr>
                                `);
                            });
                        }

                        $('#order_detail').modal('show');
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
        });
    </script>
@endsection
