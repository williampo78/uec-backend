@extends('backend.layouts.master')

@section('title', '退貨申請單管理')

@section('css')
    <style>
        .modal-dialog {
            max-width: 100%;
        }

        .is-dangerous-stock {
            background-color: #ba3f4e;
            color: white;
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
            padding: 1rem;
        }

        .tab-content {
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            padding: 30px;
        }

        #tab-lgst-info tbody th {
            text-align: right;
        }

        .detail{
            display: none;
        }
        .detail-show{
            display: table-row;
        }
        .refund-item{
            cursor: pointer;
        }
        .refund-item .fa-chevron-down{
            display: none
        }
        .refund-item-active .fa-chevron-down{
            display: inline
        }
        .refund-item-active span{
            display: none
        }

    </style>
@endsection

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-list"></i> 退貨申請單管理</h1>
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
                                            <label class="control-label">退貨申請時間</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <div class="input-group" id="order_refund_date_start_flatpickr">
                                                    <input type="text" class="form-control search-limit-group" name="order_refund_date_start" id="order_refund_date_start" value="{{ request()->input('order_refund_date_start') }}" autocomplete="off" data-input />
                                                    <span class="input-group-btn" data-toggle>
                                                        <button class="btn btn-default" type="button">
                                                            <i class="fa-solid fa-calendar-days"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-1 text-center">
                                            <label class="control-label">～</label>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <div class="input-group" id="order_refund_date_end_flatpickr">
                                                    <input type="text" class="form-control search-limit-group" name="order_refund_date_end" id="order_refund_date_end" value="{{ request()->input('order_refund_date_end') }}" autocomplete="off" data-input />
                                                    <span class="input-group-btn" data-toggle>
                                                        <button class="btn btn-default" type="button">
                                                            <i class="fa-solid fa-calendar-days"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">退貨申請單號
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input class="form-control search-limit-group" name="request_no" id="request_no"
                                                value="{{ request()->input('request_no') }}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">會員帳號
                                            </label>
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
                                            <label class="control-label">退貨申請狀態</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-shipment-status-code" id="status_code"
                                                name="status_code">
                                                <option></option>
                                                @if (config()->has('uec.return_request_status_options'))
                                                    @foreach (config('uec.return_request_status_options') as $key => $value)
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
                                            <label class="control-label">訂單編號
                                            </label>
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
                                            <label class="control-label">會員姓名
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input class="form-control search-limit-group" name="member_name"
                                                id="member_name" value="{{ request()->input('member_name') }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">訂單類型</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-shipment-status-code"
                                                    name="ship_from_whs">
                                                <option></option>
                                                    @foreach ($shipFromWhs as $type)
                                                        <option value='{{ $type['id'] }}'
                                                            {{ $type['id'] == request('ship_from_whs') ? 'selected' : '' }}>
                                                            {{ $type['text'] }}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label">待辦項目</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2-shipment-status-code"
                                                    name="to_do_item">
                                                <option></option>
                                                @foreach ($toDoItems as $item)
                                                    <option value='{{ $item['id'] }}'
                                                        {{ $item['id'] == request('to_do_item') ? 'selected' : '' }}>
                                                        {{ $item['text'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div class="col-sm-3"></div>
                                        <div class="col-sm-9 text-right">
                                            @if ($share_role_auth['auth_export'])
                                                <button data-url="{{ route('order_refund.export_excel') }}"
                                                        class="btn btn-primary" id="btn-export-excel" type="button">
                                                    <i class="fa-solid fa-file-excel"></i> 匯出EXCEL
                                                </button>
                                            @endif

                                            @if ($share_role_auth['auth_query'])
                                                <button class="btn btn-warning" id="btn-search">
                                                    <i class="fa-solid fa-magnifying-glass"></i> 查詢
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />
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
                                        <th class="text-nowrap">退貨申請時間</th>
                                        <th class="text-nowrap">退貨申請單號</th>
                                        <th class="text-nowrap">訂單編號</th>
                                        <th class="text-nowrap">退貨申請單狀態</th>
                                        <th class="text-nowrap">物流方式</th>
                                        <th class="text-nowrap">訂單類型</th>
                                        <th class="text-nowrap">退款方式</th>
                                        <th class="text-nowrap">退貨完成時間</th>
                                        <th class="text-nowrap">取件聯絡人</th>
                                        <th class="text-nowrap">取件聯絡手機</th>
                                        <th class="text-nowrap">取件地址</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderRefunds as $orderRefund)
                                        <tr>
                                            <td>
                                                @if ($share_role_auth['auth_query'])
                                                    <button data-id="{{ $orderRefund->id }}" type="button"
                                                        class="btn btn-info btn-sm order_refund_detail" title="檢視">
                                                        <i class="fa-solid fa-magnifying-glass"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $orderRefund->request_date }}</td>
                                            <td>{{ $orderRefund->request_no }}</td>
                                            <td>{{ $orderRefund->order_no }}</td>
                                            <td>{{ $orderRefund->status_code }}</td>
                                            <td>{{ $orderRefund->lgst_method }}</td>
                                            <td>{{ $orderRefund->ship_from_whs }}</td>
                                            <td>{{ $orderRefund->refund_method }}</td>
                                            <td>{{ $orderRefund->completed_at }}</td>
                                            <td>{{ $orderRefund->req_name }}</td>
                                            <td>{{ $orderRefund->req_mobile }}</td>
                                            <td>{{ sprintf('%s%s%s', $orderRefund->req_city, $orderRefund->req_district, $orderRefund->req_address) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- 詳細 modal--}}
        @include('backend.order_refund.detail')
        {{-- 協商回報 modal--}}
        @include('backend.order_refund.negotiated_return')
        <!-- /.modal -->
    </div>
@endsection

@section('js')
    <script src="{{ mix('js/order_refund.js') }}"></script>
    <script>
        $(function() {
            let get_detail_url = '{{ route('order_refund.detail') }}';
            let nego_refund_amount_object = $('#nego_refund_amount');
            const required_message = '須指定﹝退貨申請時間﹞起訖、或﹝退貨申請單號﹞、或﹝訂單編號﹞、或﹝會員帳號﹞才可執行查詢！';

            let order_refund_date_start_flatpickr = flatpickr("#order_refund_date_start_flatpickr", {
                dateFormat: "Y-m-d",
                maxDate: $("#order_refund_date_end").val(),
                onChange: function(selectedDates, dateStr, instance) {
                    order_refund_date_end_flatpickr.set('minDate', dateStr);
                },
            });

            let order_refund_date_end_flatpickr = flatpickr("#order_refund_date_end_flatpickr", {
                dateFormat: "Y-m-d",
                minDate: $("#order_refund_date_start").val(),
                onChange: function(selectedDates, dateStr, instance) {
                    order_refund_date_start_flatpickr.set('maxDate', dateStr);
                },
            });

            // 搜尋-驗證表單
            $("#search-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#btn-search').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    order_refund_date_start: {
                        require_from_group: [1, ".search-limit-group"],
                    },
                    order_refund_date_end: {
                        require_from_group: [1, ".search-limit-group"],
                    },
                    request_no: {
                        require_from_group: [1, ".search-limit-group"],
                    },
                    member_account: {
                        require_from_group: [1, ".search-limit-group"],
                    },
                },
                messages: {
                    order_refund_date_start: {
                        require_from_group: required_message,
                    },
                    order_refund_date_end: {
                        require_from_group: required_message,
                    },
                    request_no: {
                        require_from_group: required_message,
                    },
                    member_account: {
                        require_from_group: required_message,
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

            //退貨協商回報-驗證表單
            $("#negotiated-return-form").validate({
                submitHandler: function(form) {
                    updateNegotiatedReturn();
                },
                rules: {
                    //協商結果
                    nego_result: {
                        required:true
                    },
                    //退款金額
                    nego_refund_amount: {
                        required:true,
                        min:0,
                        max:999999,
                        digits:true
                    },
                    //協商內容備註
                    nego_remark: {
                        required:true,
                        maxlength:250
                    },
                },
                messages: {
                    //退款金額
                    nego_refund_amount: {
                        min:'請輸入大於{0}的數字',
                        max:'請輸入小於{0}的數字',
                        digits:'金額格式錯誤'
                    },
                    //協商內容備註
                    nego_remark: {
                        maxlength:'字數請勿超過{0}',
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

            //切換協商結果radio
            $(document).on('change', 'input[name="nego_result"]', function(){
                nego_refund_amount_object.prop('disabled', false);
                //不允許退貨
                if($(this).val() == 0){
                    nego_refund_amount_object.prop('disabled', true);
                    nego_refund_amount_object.val(0);
                }
            });

            //點擊放大鏡
            $(document).on('click', '.order_refund_detail', function() {
                axios.get(get_detail_url, {
                        params: {
                            id: $(this).data('id')
                        },
                        responseType: 'json',
                    })
                    .then(function(response) {

                        if (response.data.status == false) {
                            alert('發生錯誤');
                            return false;
                        }

                        let return_request = response.data.data.return_request;
                        let return_details = response.data.data.return_details;
                        let return_information = response.data.data.return_information;
                        //文字是取件單號或是物流單號
                        $('#number_or_logistics_name_column_name').html(response.data.data.number_or_logistics_name_column_name);

                        //檢視資料內的內容
                        handleReturnRequest(return_request);
                        //退款明細
                        handleReturnDetails(return_details, return_request.request_no);
                        //處理退貨明細資料
                        handleReturnInformation(return_information);

                        $('#order_refund_detail').modal('show');
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });

            // 匯出excel
            $('#btn-export-excel').on('click', function() {

                let url = $(this).data('url');

                axios.get(url, {
                        params: {
                            order_refund_date_start: $('#order_refund_date_start').val(),
                            order_refund_date_end: $('#order_refund_date_end').val(),
                            request_no: $('#request_no').val(),
                            member_account: $('#member_account').val(),
                            status_code: $('#status_code').val(),
                            order_no: $('#order_no').val(),
                            member_name: $('#member_name').val(),
                        },
                        responseType: 'blob',
                    })
                    .then(function(response) {
                        saveAs(response.data, "order_refunds.xlsx");
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });

            //協商回報按鈕
            $(document).on('click', '.negotiated-return', function() {

                $('input[name="return_examination_id"]').val($(this).data('return_examination_id'));
                $('#negotiated-return-request-no').html($(this).data('return_request_no'));
                $('#negotiated-return-examination-no').html($(this).data('return_examination_no'));
            });
        });

        //更新協商狀態
        function updateNegotiatedReturn() {

            axios.post('{{ route('order_refund.update_negotiated_return') }}', {
                _method: 'PUT',
                return_examination_id: $('#return_examination_id').val(),
                nego_result: $('input[name="nego_result"]:checked').val(),
                nego_refund_amount: $('#nego_refund_amount').val(),
                nego_remark: $('#nego_remark').val(),
            }).then(function (response) {
                console.log(1);
                //saveAs(response.data, "order_refunds.xlsx");
            }).catch(function (error) {
                console.log(2);
                console.log(error);
            });
        }

        //檢視資料內的內容
        function handleReturnRequest(return_request) {
            //檢視資料內的內容 start
            //退貨申請單號
            $('#modal-request-no').empty().text(return_request.request_no);
            //退貨申請時間
            $('#modal-request-date').empty().text(return_request.request_date);
            //訂單編號
            $('#modal-order-no').empty().text(return_request.order_no);
            //退貨單狀態
            $('#modal-status-code').empty().text(return_request.status_code);
            //退貨完成時間
            $('#modal-completed-at').empty().text(return_request.completed_at);
            //物流方式
            $('#modal-lgst-method').empty().text(return_request.lgst_method);
            //會員編號
            $('#modal-member-account').empty().text(return_request.member_account);
            //訂購人
            $('#modal-buyer-name').empty().text(return_request.buyer_name);
            //取件聯絡人
            $('#modal-req-name').empty().text(return_request.req_name);
            //取件聯絡手機
            $('#modal-req-mobile').empty().text(return_request.req_mobile);
            //取件聯絡電話
            $('#modal-req-telephone').empty().text(return_request.req_telephone);
            //取件聯絡地址
            $('#modal-req-fulladdress').empty().text(
                `${return_request.req_city}${return_request.req_district}${return_request.req_address}`);
            //退貨原因
            $('#modal-req-reason-description').empty().text(return_request.req_reason_description);
            //退貨備註
            $('.modal-req-remark').empty().text(return_request.req_remark);

            //檢視資料內的內容 end
        }

        //退款明細
        function handleReturnDetails(return_details, request_no) {
            let list = '';
            $('#return_details_content').empty();

            //退款明細 start
            $.each(return_details, function(index, value) {

                let details = '';
                //詳細內容
                $.each(value.details, function(index, detail) {
                    details += `<tr>
                        <td>${detail.item_no}</td>
                        <td>${detail.product_name}</td>
                        <td>${detail.spec_1_value}</td>
                        <td>${detail.spec_2_value}</td>
                        <td>${detail.request_qty}</td>
                        <td>${detail.supplier_product_no}</td>
                    </tr>`;
                });

                let button = '';
                if(value.button.can_operate){
                    button = `<button type="button" class="btn btn-warning negotiated-return" data-return_examination_id="${value.return_examination_id}" data-return_request_no="${request_no}" data-return_examination_no="${ value.examination_no}" data-toggle="modal" data-target="#negotiated_return" data-dismiss="modal">${ value.button.title }</button>`;
                }

                list += `<tr>
                            <td class="text-nowrap">${index + 1 }</td>
                            <td class="text-nowrap">
                                ${button}
                            </td>
                            <td class="text-nowrap refund-item">
                                <span>></span>
                                <i class="fa-sharp fa-solid fa-chevron-down"></i>
                                ${value.examination_no}
                            </td>
                            <td class="text-nowrap">${value.status_code}</td>
                            <td class="text-nowrap">${value.supplier_name}</td>
                            <td class="text-nowrap">${value.lgst_dispatched_at}</td>
                            <td class="text-nowrap">${value.number_or_logistics_name}</td>
                            <td class="text-nowrap">${value.lgst_doc_no}</td>
                            <td class="text-nowrap">${value.examination_reported_at}</td>
                            <td class="text-nowrap">${value.is_examination_passed}</td>
                            <td class="text-nowrap">${value.examination_remark}</td>
                            <td class="text-nowrap">${value.nego_result}</td>
                            <td class="text-nowrap">${value.nego_refund_amount}</td>
                            <td class="text-nowrap">${value.nego_remark}</td>
                        </tr>
                        <tr class="detail detail-${index}" style="background:#eee">
                            <td style="border:none" colspan="2"></td>
                            <td style="border:none" colspan="5">
                                <table class="table table-bordered">
                                   <thead>
                                        <tr class="active">
                                          <th>Item編號</th>
                                          <th>商品名稱</th>
                                          <th>規格一</th>
                                          <th>規格二</th>
                                          <th>申請數量</th>
                                          <th>廠商料號</th>
                                        </tr>
                                    </thead>
                                   <tbody>
                                        ${details}
                                    </tbody>
                                </table>
                            </td>
                            <td style="border:none" colspan="5"></td>
                        </tr>`;
                index++;
            });

            $('#return_details_content').append(list);
            //退款明細 end
        }

        //退貨檢驗單號收合
        $(document).on('click', '.refund-item', function () {
            const index = [...$('.refund-item')].indexOf(this);
            $(`.detail-${index}`).toggleClass('detail-show');
            $(this).toggleClass('refund-item-active');
        })

        //處理退貨明細資料
        function handleReturnInformation(return_information) {
            let list = '';
            $('#return_information_content').empty();

            //處理退貨明細資料 start
            $.each(return_information, function(index, value) {
                list += `<tr>
                            <td class="text-nowrap">${index + 1 }</td>
                            <td class="text-nowrap">${value.created_at}</td>
                            <td class="text-nowrap">${value.payment_type_desc}</td>
                            <td class="text-nowrap">Tappay</td>
                            <td class="text-nowrap">${value.amount}</td>
                            <td class="text-nowrap">${value.payment_status_desc}</td>
                            <td class="text-nowrap">${value.remark == null ? '' : value.remark}</td>
                        </tr>`;
                index++;
            })

            $('#return_information_content').append(list);
            //處理退貨明細資料 end
        }
    </script>
@endsection
