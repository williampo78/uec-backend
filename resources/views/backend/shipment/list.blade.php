@extends('backend.layouts.master')

@section('title', '出貨單管理')

@section('css')
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
                <h1 class="page-header"><span class="fa-solid fa-list"></span> 出貨單管理</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕 -->
                    <div class="panel-heading p-4">
                        <form id="search-form" method="GET" action="">
                            <div class="d-block d-md-grid custom-outer">
                                <div class="mb-4 custom-title form-group">
                                    <label class="control-label">建單時間</label>
                                    <div class="d-flex align-items-center date-input-custom w-100">
                                        <div class="input-group" id="created_at_start_flatpickr">
                                            <input type="text" class="form-control search-limit-group" name="created_at_start" id="created_at_start" value="{{ request()->input('created_at_start') }}" autocomplete="off" data-input style="z-index: 3;" />
                                            <span class="input-group-btn" data-toggle>
                                                <button class="btn btn-default" type="button">
                                                    <span class="fa-solid fa-calendar-days"></span>
                                                </button>
                                            </span>
                                        </div>
                                        <label>～</label>
                                        <div class="input-group" id="created_at_end_flatpickr">
                                            <input type="text" class="form-control search-limit-group" name="created_at_end" id="created_at_end" value="{{ request()->input('created_at_end') }}" autocomplete="off" data-input style="z-index: 3;"/>
                                            <span class="input-group-btn" data-toggle>
                                                <button class="btn btn-default" type="button">
                                                    <span class="fa-solid fa-calendar-days"></span>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="custom-error column-full"></div>
                                </div>

                                <div class="form-group mx-0 custom-title">
                                    <label class="control-label">出貨單號</label>
                                    <input class="form-control search-limit-group" name="shipment_no"
                                        id="shipment_no" value="{{ request()->input('shipment_no') }}" />
                                </div>

                                <div class="form-group mx-0 custom-title">
                                    <label class="control-label">會員帳號</label>
                                    <input class="form-control search-limit-group" name="member_account"
                                        id="member_account" value="{{ request()->input('member_account') }}" />
                                </div>
                            </div>

                            <div class="d-block d-md-grid custom-outer">
                                <div class="mb-4 custom-title form-group">
                                    <label class="control-label">訂單編號</label>
                                    <input class="form-control search-limit-group" name="order_no" id="order_no"
                                        value="{{ request()->input('order_no') }}" />
                                </div>

                                <div class="mb-4 custom-title">
                                    <label class="control-label">出貨單狀態</label>
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

                                <div class="mb-4 custom-title">
                                    <label class="control-label">付款方式</label>
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

                            <div class="d-block d-md-grid custom-outer">
                                <div class="mb-4 mb-md-0 custom-title">
                                    <label class="control-label">商品序號</label>
                                    <input class="form-control" name="product_no" id="product_no"
                                        value="{{ request()->input('product_no') }}" />
                                </div>

                                <div class="mb-4 mb-md-0 custom-title">
                                    <label class="control-label">商品名稱</label>
                                    <input class="form-control" name="product_name" id="product_name"
                                        value="{{ request()->input('product_name') }}" placeholder="模糊查詢" />
                                </div>

                                <div class="text-right">
                                    @if ($share_role_auth['auth_query'])
                                        <button class="btn btn-warning" id="btn-search">
                                            <i class="fa-solid fa-magnifying-glass"></i> 查詢
                                        </button>
                                    @endif
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
                                        <th class="text-nowrap">託運單號</th>
                                        <th class="text-nowrap">會員帳號</th>
                                        <th class="text-nowrap">訂購人</th>
                                        <th class="text-nowrap">收件者</th>
                                        <th class="text-nowrap">收件手機</th>
                                        <th class="text-nowrap" style="min-width: 250px;">收件地址</th>
                                        <th class="text-nowrap">供應商</th>
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
                                                            <i class="fa-solid fa-magnifying-glass"></i>
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
                                                <td>{{ $shipment['lgst_company'] ?? '' }}</td>
                                                <td>{{ $shipment['package_no'] ?? '' }}</td>
                                                <td>{{ $shipment['member_account'] ?? '' }}</td>
                                                <td>{{ $shipment['buyer_name'] ?? '' }}</td>
                                                <td>{{ $shipment['ship_to_name'] ?? '' }}</td>
                                                <td>{{ $shipment['ship_to_mobile'] ?? '' }}</td>
                                                <td>{{ $shipment['ship_to_address'] ?? '' }}</td>
                                                <td>{{ $shipment['supplier_name'] ?? '' }}</td>
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
        @include('backend.shipment.progress-log')
        <!-- /.modal -->

    </div>
@endsection

@section('js')
    <script>
        $(function() {
            let created_at_start_flatpickr = flatpickr("#created_at_start_flatpickr", {
                dateFormat: "Y-m-d",
                maxDate: $("#created_at_end").val(),
                onChange: function(selectedDates, dateStr, instance) {
                    created_at_end_flatpickr.set('minDate', dateStr);
                },
            });

            let created_at_end_flatpickr = flatpickr("#created_at_end_flatpickr", {
                dateFormat: "Y-m-d",
                minDate: $("#created_at_start").val(),
                onChange: function(selectedDates, dateStr, instance) {
                    created_at_start_flatpickr.set('maxDate', dateStr);
                },
            });

            $('.select2-shipment-status-code').select2();
            $('.select2-shipment-payment-method').select2();

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
                        require_from_group: null,
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
                errorClass: "help-block column-full",
                errorElement: "span",
                errorPlacement: function(error, element) {
                    if(element.parents('.date-input-custom').length  ){
                        // 建單時間欄位
                        element.parents('.custom-title').find('.custom-error').append(error);
                        return
                    }

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
                }
            });

            let shipment_id = 0;
            $(document).on('click', '.shipment_detail', function() {
                shipment_id = $(this).attr("data-shipment");

                axios.get(`/backend/shipment/${shipment_id}`)
                    .then(function(response) {
                        let shipment = response.data;
                        let package_no = shipment.package_no ? shipment.package_no : '';

                        if (shipment.ship_from_whs == 'SELF') {
                            package_no = '<a href="http://query2.e-can.com.tw/%E5%A4%9A%E7%AD%86%E6%9F%A5%E4%BB%B6A.htm" target="_blank">' + package_no + '</a>';
                        }

                        if (shipment.ship_from_whs == 'SUP') {
                            $('#modal-progress-log').show();
                        } else {
                            $('#modal-progress-log').hide();
                        }

                        $('#modal-shipment-no').empty().text(shipment.shipment_no);
                        $('#modal-created-at').empty().text(shipment.created_at_format);
                        $('#modal-status-code').empty().text(shipment.status_code);
                        $('#modal-lgst-method').empty().text(shipment.lgst_method);
                        $('#modal-lgst-company').empty().text(shipment.lgst_company);
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
                                let supplier_product_no = shipment_detail.supplier_product_no ? shipment_detail
                                .supplier_product_no : '';
                                let supplier_item_no = shipment_detail.supplier_item_no ? shipment_detail
                                .supplier_item_no : '';
                                let supplier_name = shipment_detail.supplier_name ? shipment_detail
                                .supplier_name : '';

                                $("#modal-product-table tbody").append(`
                                    <tr>
                                        <td>${shipment_detail.seq}</td>
                                        <td>${item_no}</td>
                                        <td>${shipment_detail.product_name}</td>
                                        <td>${spec_1_value}</td>
                                        <td>${spec_2_value}</td>
                                        <td>${shipment_detail.qty}</td>
                                        <td>${supplier_product_no}</td>
                                        <td>${supplier_item_no}</td>
                                        <td>${supplier_name}</td>
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

            $(document).on('click', '.progress_log_detail', function() {
                let shipment_no = $('#modal-shipment-no').text();

                axios.get(`/backend/shipment/${shipment_id}/progress-logs`)
                    .then(function(response) {
                        let progress_log = response.data;

                        $('#modal-log-shipment-no').empty().text(shipment_no);
                        $("#modal-log-table tbody").empty();

                        if (progress_log.payload) {
                            $.each(progress_log.payload, function(key, log) {
                                let log_memo = log.memo ? log.memo : '';
                                let log_agreed_date = log.agreed_date ? log.agreed_date : '';

                                $("#modal-log-table tbody").append(`
                                    <tr>
                                        <td>${log.logged_at}</td>
                                        <td>${log.progress_code_name}</td>
                                        <td>${log_memo}</td>
                                        <td>${log_agreed_date}</td>
                                        <td>${log.logged_by}</td>
                                    </tr>
                                `);
                            });
                        }

                        $('#progress_log_detail').modal('show');
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });

        });
    </script>
@endsection
