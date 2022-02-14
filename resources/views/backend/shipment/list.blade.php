@extends('backend.master')

@section('title', '出貨單管理')

@section('style')
    <style>
        .modal-dialog {
            max-width: 100%;
        }

    </style>
@endsection

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i> 出貨單管理</h1>
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
                                        <div class="col-sm-3">
                                            <label class="control-label">建單時間</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <div class='input-group date' id='datetimepicker_created_at_start'>
                                                    <input type='text'
                                                        class="form-control datetimepicker-input search-limit-group"
                                                        data-target="#datetimepicker_created_at_start"
                                                        name="created_at_start" id="created_at_start"
                                                        value="{{ request()->input('created_at_start') }}"
                                                        autocomplete="off" />
                                                    <span class="input-group-addon"
                                                        data-target="#datetimepicker_created_at_start"
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
                                                <div class='input-group date' id='datetimepicker_created_at_end'>
                                                    <input type='text'
                                                        class="form-control datetimepicker-input search-limit-group"
                                                        data-target="#datetimepicker_created_at_end" name="created_at_end"
                                                        id="created_at_end"
                                                        value="{{ request()->input('created_at_end') }}"
                                                        autocomplete="off" />
                                                    <span class="input-group-addon"
                                                        data-target="#datetimepicker_created_at_end"
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
                                        <div class="col-sm-3">
                                            <label class="control-label">出貨單號</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input class="form-control search-limit-group" name="shipment_no"
                                                id="shipment_no" value="{{ request()->input('shipment_no') }}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
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
                                        <div class="col-sm-3">
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
                                        <div class="col-sm-3">
                                            <label class="control-label">出貨單狀態</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-shipment-status-code" id="status_code"
                                                name="status_code">
                                                <option></option>
                                                @if (config()->has('uec.shipment_status_code_options'))
                                                    @foreach (config('uec.shipment_status_code_options') as $key => $value)
                                                        <option value='{{ $key }}'
                                                            {{ $key == request()->input('status_code') ? 'selected' : '' }}>
                                                            {{ $value }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">付款方式</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-shipment-payment-method" id="payment_method"
                                                name="payment_method">
                                                <option></option>
                                                @if (config()->has('uec.payment_method_options'))
                                                    @foreach (config('uec.payment_method_options') as $key => $value)
                                                        <option value='{{ $key }}'
                                                            {{ $key == request()->input('payment_method') ? 'selected' : '' }}>
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
                                        <div class="col-sm-3">
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
                                        <div class="col-sm-3">
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
                                        <div class="col-sm-3"></div>
                                        <div class="col-sm-9 text-right">
                                            @if ($share_role_auth['auth_query'])
                                                <button class="btn btn-warning" id="btn-search"><i
                                                        class="fa fa-search"></i>
                                                    查詢</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive">
                            <table class="table table-striped table-bordered table-hover" style="width:100%"
                                id="table_list">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">功能</th>
                                        <th class="text-nowrap">項次</th>
                                        <th class="text-nowrap">建單時間</th>
                                        <th class="text-nowrap">出貨單號</th>
                                        <th class="text-nowrap">訂單編號</th>
                                        <th class="text-nowrap">物流方式</th>
                                        <th class="text-nowrap">出貨單狀態</th>
                                        <th class="text-nowrap">出貨時間</th>
                                        <th class="text-nowrap">物流廠商</th>
                                        <th class="text-nowrap">會員帳號</th>
                                        <th class="text-nowrap">訂購人</th>
                                        <th class="text-nowrap">收件者</th>
                                        <th class="text-nowrap">收件手機</th>
                                        <th class="text-nowrap">收件地址</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($shipments)
                                        @php
                                            $count = 1;
                                        @endphp
                                        @foreach ($shipments as $shipment)
                                            <tr>
                                                <td>
                                                    @if ($share_role_auth['auth_query'])
                                                        <button type="button" class="btn btn-info btn-sm shipment_detail"
                                                            data-shipment="{{ $shipment['shipments_id'] }}" title="檢視">
                                                            <i class="fa fa-search"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                                <td>{{ $count++ }}</td>
                                                <td>{{ $shipment['created_at_format'] ?? '' }}</td>
                                                <td>{{ $shipment['shipment_no'] ?? '' }}</td>
                                                <td>{{ $shipment['order_no'] ?? '' }}</td>
                                                <td>{{ $shipment['lgst_method'] ?? '' }}</td>
                                                <td>{{ $shipment['status_code'] ?? '' }}</td>
                                                <td>{{ $shipment['shipped_at'] ?? '' }}</td>
                                                <td>{{ $shipment['lgst_company_code'] ?? '' }}</td>
                                                <td>{{ $shipment['member_account'] ?? '' }}</td>
                                                <td>{{ $shipment['buyer_name'] ?? '' }}</td>
                                                <td>{{ $shipment['ship_to_name'] ?? '' }}</td>
                                                <td>{{ $shipment['ship_to_mobile'] ?? '' }}</td>
                                                <td>{{ $shipment['ship_to_address'] ?? '' }}</td>
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
        @include('backend.shipment.detail')
        <!-- /.modal -->

    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('#datetimepicker_created_at_start').datetimepicker({
                format: 'YYYY-MM-DD',
                showClear: true,
            });

            $('#datetimepicker_created_at_end').datetimepicker({
                format: 'YYYY-MM-DD',
                showClear: true,
            });

            $("#datetimepicker_created_at_start").on("dp.change", function(e) {
                if ($('#created_at_end').val()) {
                    $('#datetimepicker_created_at_end').datetimepicker('minDate', e.date);
                }
            });

            $("#datetimepicker_created_at_end").on("dp.change", function(e) {
                if ($('#created_at_start').val()) {
                    $('#datetimepicker_created_at_start').datetimepicker('maxDate', e.date);
                }
            });

            $('.select2-shipment-status-code').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '',
            });

            $('.select2-shipment-payment-method').select2({
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
                    created_at_start: {
                        require_from_group: [1, ".search-limit-group"],
                    },
                    created_at_end: {
                        require_from_group: [1, ".search-limit-group"],
                    },
                    shipment_no: {
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
                    created_at_start: {
                        require_from_group: '須指定﹝建單時間﹞起訖、或﹝出貨單號﹞、或﹝訂單編號﹞、或﹝會員帳號﹞才可執行查詢！',
                    },
                    created_at_end: {
                        require_from_group: '須指定﹝建單時間﹞起訖、或﹝出貨單號﹞、或﹝訂單編號﹞、或﹝會員帳號﹞才可執行查詢！',
                    },
                    shipment_no: {
                        require_from_group: '須指定﹝建單時間﹞起訖、或﹝出貨單號﹞、或﹝訂單編號﹞、或﹝會員帳號﹞才可執行查詢！',
                    },
                    order_no: {
                        require_from_group: '須指定﹝建單時間﹞起訖、或﹝出貨單號﹞、或﹝訂單編號﹞、或﹝會員帳號﹞才可執行查詢！',
                    },
                    member_account: {
                        require_from_group: '須指定﹝建單時間﹞起訖、或﹝出貨單號﹞、或﹝訂單編號﹞、或﹝會員帳號﹞才可執行查詢！',
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

            $(document).on('click', '.shipment_detail', function() {
                let shipment_id = $(this).attr("data-shipment");

                axios.post('/backend/shipment/ajax/detail', {
                        shipment_id: shipment_id
                    })
                    .then(function(response) {
                        let shipment = response.data;
                        let package_no = shipment.package_no ?
                            `<a href="http://query2.e-can.com.tw/%E5%A4%9A%E7%AD%86%E6%9F%A5%E4%BB%B6A.htm" target="_blank">${shipment.package_no}</a>` :
                            '';

                        $('#modal-shipment-no').empty().text(shipment.shipment_no);
                        $('#modal-created-at').empty().text(shipment.created_at_format);
                        $('#modal-status-code').empty().text(shipment.status_code);
                        $('#modal-lgst-method').empty().text(shipment.lgst_method);
                        $('#modal-lgst-company-code').empty().text(shipment.lgst_company_code);
                        $('#modal-order-no').empty().text(shipment.order_no);
                        $('#modal-ship-to-name').empty().text(shipment.ship_to_name);
                        $('#modal-ship-to-mobile').empty().text(shipment.ship_to_mobile);
                        $('#modal-ship-to-address').empty().text(shipment.ship_to_address);
                        $('#modal-member-account').empty().text(shipment.member_account);
                        $('#modal-edi-exported-at').empty().text(shipment.edi_exported_at);
                        $('#modal-package-no').empty().html(package_no);
                        $('#modal-shipped-at').empty().text(shipment.shipped_at);
                        $('#modal-arrived-store-at').empty().text(shipment.arrived_store_at);
                        $('#modal-cvs-completed-at').empty().text(shipment.cvs_completed_at);
                        $('#modal-home-dilivered-at').empty().text(shipment.home_dilivered_at);
                        $('#modal-overdue-confirmed-at').empty().text(shipment.overdue_confirmed_at);
                        $('#modal-cancelled-voided-at').empty().text(shipment.cancelled_voided_at);

                        // 出貨單明細
                        $("#modal-product-table tbody").empty();

                        if (shipment.shipment_details) {
                            $.each(shipment.shipment_details, function(key, shipment_detail) {
                                let item_no = shipment_detail.item_no ? shipment_detail
                                    .item_no : '';
                                let spec_1_value = shipment_detail.spec_1_value ?
                                    shipment_detail.spec_1_value : '';
                                let spec_2_value = shipment_detail.spec_2_value ?
                                    shipment_detail.spec_2_value : '';

                                $("#modal-product-table tbody").append(`
                                    <tr>
                                        <td>${shipment_detail.seq}</td>
                                        <td>${item_no}</td>
                                        <td>${shipment_detail.product_name}</td>
                                        <td>${spec_1_value}</td>
                                        <td>${spec_2_value}</td>
                                        <td>${shipment_detail.qty}</td>
                                    </tr>
                                `);
                            });
                        }

                        $('#shipment_detail').modal('show');
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
        });
    </script>
@endsection
