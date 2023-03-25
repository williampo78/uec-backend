@extends('backend.layouts.master')
@section('title', '進貨單')
@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-arrow-right-to-bracket"></i> 進貨單</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->

                    <div class="panel-heading" style="padding: 15px;">
                        <form role="form" class="form-horizontal" id="select-form" method="GET" action=""
                            enctype="multipart/form-data">
                            {{-- row 1 start --}}
                            <div style="grid-template-columns: 3fr 2fr 2fr; gap: 15px;  border-bottom: 1px solid #eee; margin-bottom: 15px;"
                                class="d-block d-md-grid">
                                <div>
                                    <div class="col-sm-3"><label class="control-label"> 供應商 </label></div>
                                    <div class="col-sm-9">
                                        <div class='input-group mb-4' id='supplier_deliver_date_dp'>
                                            <select class="form-control js-select2-department mb-4" name="supplier"
                                                id="supplier" value="{{ request()->input('supplier') }}">
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
                                <div>
                                    <div class="col-sm-4"><label class="control-label">供應商統編</label></div>
                                    <div class="col-sm-8">
                                        <input class="form-control mb-4" name="company_number" id="company_number"
                                            value="{{ request()->input('company_number') }}">
                                    </div>
                                </div>
                                <div>
                                    <div class="col-sm-4"><label class="control-label">採購單號</label></div>
                                    <div class="col-sm-8">
                                        <input class="form-control" name="order_supplier_number" id="order_supplier_number"
                                            value="{{ request()->input('order_supplier_number') }}">
                                    </div>
                                </div>
                            </div>
                            {{-- row 1 end --}}
                            {{-- row 2 start --}}
                            <div style="grid-template-columns: 3fr 2fr 2fr; gap: 15px" class="d-block d-md-grid">
                                <div>
                                    <div class="col-sm-3"><label class="control-label">進貨日期</label></div>
                                    <div class="col-sm-9" style="display: flex; justify-content: space-between;">
                                        <div class="form-group" style="margin-right: 0; margin-left: 0; margin-bottom: 0;">
                                            <div class="input-group" id="trade_date_start_flatpickr">
                                                <input type="text" class="form-control" name="trade_date_start"
                                                    id="trade_date_start" value="{{ request()->input('trade_date_start') }}"
                                                    autocomplete="off" data-input />
                                                <span class="input-group-btn" data-toggle>
                                                    <button class="btn btn-default" type="button">
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <label style="margin-top: 8px;">～</label>
                                        <div class="form-group" style="margin-right: 0; margin-left: 0; margin-bottom: 0;">
                                            <div class="input-group mb-md-0 mb-4" id="trade_date_end_flatpickr">
                                                <input type="text" class="form-control" name="trade_date_end"
                                                    id="trade_date_end" value="{{ request()->input('trade_date_end') }}"
                                                    autocomplete="off" data-input />
                                                <span class="input-group-btn" data-toggle>
                                                    <button class="btn btn-default" type="button">
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="col-sm-4"><label class="control-label">進貨單號</label></div>
                                    <div class="col-sm-8">
                                        <input class="form-control mb-md-0 mb-4" name="number" id="number"
                                            value="{{ request()->input('number') }}">
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="col-sm-12">
                                        <button class="btn btn-warning"><i class="fa-solid fa-magnifying-glass"></i>
                                            查詢</button>
                                    </div>
                                </div>
                                {{-- row 2 end --}}
                            </div>
                        </form>

                    </div>

                </div>

                <!-- Table list -->
                <div class="panel-body">

                    <table class="table table-striped table-bordered table-hover" style="width:100%;" id="table_list">
                        <thead>
                            <tr>
                                <th>功能</th>
                                <th>進貨日期</th>
                                <th>進貨單號</th>
                                <th>採購單號</th>
                                <th>總金額</th>
                                <th>發票號碼</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(purchase, key) in purchaseData">
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" @click="showData(purchase)">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" @click="showUpdateInvoice(purchase)">補登發票</button>
                                </td>
                                <td>@{{ purchase.trade_date }}</td>
                                <td>@{{ purchase.number }}</td>
                                <td>@{{ purchase.order_supplier_number }}</td>
                                <td>@{{ purchase.total_price }}</td>
                                <td>@{{ purchase.invoice_number }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @include('backend.purchase.detail')
        @include('backend.purchase.update_invoice')

    </div>
    </div>

@section('js')
    <script>
        var app = Vue.extend({
            data: function() {
                return {
                    purchaseData: @json($purchase_data ?? []),
                    purchase:[],
                    purchaseDetail:[],
                    invoiceForm:{
                        id:'',
                        number:'',
                        order_supplier_number:'',
                        invoice_date:'',
                        invoice_number:'',
                    }
                }
            },
            mounted() {
                var vm = this ;
                $('#supplier').select2();

                let trade_date_start_flatpickr = flatpickr("#trade_date_start_flatpickr", {
                    dateFormat: "Y-m-d",
                    maxDate: $("#trade_date_end").val(),
                    onChange: function(selectedDates, dateStr, instance) {
                        trade_date_end_flatpickr.set('minDate', dateStr);
                    },
                });

                let trade_date_end_flatpickr = flatpickr("#trade_date_end_flatpickr", {
                    dateFormat: "Y-m-d",
                    minDate: $("#trade_date_start").val(),
                    onChange: function(selectedDates, dateStr, instance) {
                        trade_date_start_flatpickr.set('maxDate', dateStr);
                    },
                });

                $("#select-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        // $('#save_data').prop('disabled', true);
                        form.submit();
                    },
                    rules: {
                        trade_date_start: {
                            required: {
                                depends: function(element) {
                                    if ($('#order_supplier_number').val() !== '' || $('#number')
                                        .val() !== '') {
                                        return false;
                                    } else {
                                        return true;
                                    }
                                }
                            },
                            monthIntervalVerify: function() {
                                isExecution = true
                                if ($('#order_supplier_number').val() !== '' || $('#number').val() !==
                                    '') {
                                    isExecution = false;
                                }
                                let obj = {
                                    startTime: $('#trade_date_start').val(),
                                    endTime: $('#trade_date_end').val(),
                                    monthNum: 6,
                                    isExecution: isExecution,
                                }
                                return obj;
                            },
                        },
                        trade_date_end: {
                            required: {
                                depends: function(element) {
                                    if ($('#order_supplier_number').val() !== '' || $('#number')
                                        .val() !== '') {
                                        return false;
                                    } else {
                                        return true;
                                    }
                                }
                            },
                            dateGreaterEqualThan: function() {
                                let obj = {
                                    date: $('#trade_date_start').val(),
                                    depends: true,
                                }
                                return obj;
                            },
                        },

                    },
                    messages: {
                        end_launched_at: {
                            dateGreaterEqualThan: "結束時間必須大於等於開始時間",
                        },
                        trade_date_end: {
                            dateGreaterEqualThan: "進貨日期結束時間必須大於開始時間"
                        }
                    },
                    errorClass: "help-block",
                    errorElement: "span",
                    errorPlacement: function(error, element) {
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

                $("#update_invoice_form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        var check = confirm('確定要修改嗎?');
                        console.log(check) ;
                        if (check) {
                            axios.post('/backend/purchase/ajax', {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    type: 'update_invoice',
                                    id: vm.invoiceForm.id,
                                    invoice_number: vm.invoiceForm.invoice_number,
                                    invoice_date: vm.invoiceForm.invoice_date
                                })
                                .then(function(response) {
                                    alert('更新成功')
                                    history.go(0)
                                })
                                .catch(function(error) {
                                    console.log(error);
                                });
                        }
                        return false ;

                    },
                    rules: {
                        invoice_number: {
                            required: true
                        },
                        invoice_date: {
                            required: true
                        },

                    },
                    messages: {

                    },
                    errorClass: "help-block",
                    errorElement: "span",
                    errorPlacement: function(error, element) {
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

            },
            created() {
            },
            methods: {
                showData(purchase) {
                    this.purchase = purchase ;
                    this.purchaseDetail = purchase.purchase_detail ;
                    $('#show_data').modal('show');
                },
                showUpdateInvoice(purchase){
                    this.invoiceForm.id = purchase.id ;
                    this.invoiceForm.number = purchase.number ;
                    this.invoiceForm.order_supplier_number = purchase.order_supplier_number ;
                    this.invoiceForm.invoice_date = purchase.invoice_date ;
                    this.invoiceForm.invoice_number = purchase.invoice_number ;
                    flatpickr("#invoice_date_flatpickr", {
                        dateFormat: "Y-m-d",
                    });
                    $('#update_invoice').modal('show');
                },
            },
            computed: {},
            watch: {},
        })
        new app().$mount('#page-wrapper');
    </script>
@endsection
@endsection
