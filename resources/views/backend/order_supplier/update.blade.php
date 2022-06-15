@extends('backend.master')
@section('title', $act == 'upd' ? '編輯採購單' : '新建採購單')
@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body" id="requisitions_vue_app">
                        <form role="form" id="new-form" method="post"
                            action="{{ $act == 'add' ? route('order_supplier.store') : route('order_supplier.update', $id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- 欄位 -->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="requisitions_purchase_number">請購單</label>
                                                <input class="form-control" id="requisitions_purchase_number"
                                                    value="{{ $order_supplier['requisitions_purchase_number'] ?? '' }}"
                                                    readonly>
                                                <input type="hidden" name="requisitions_purchase_id"
                                                    value="{{ $order_supplier['requisitions_purchase_id'] }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_trade_date">
                                                <label for="trade_date">採購日期<span class="text-red">*</span></label>
                                                <div class="input-group" id="trade_date_flatpickr">
                                                    <input type="text" class="form-control" name="trade_date" id="trade_date" value="{{ $order_supplier['trade_date'] ?? '' }}" autocomplete="off" data-input />
                                                    <span class="input-group-btn" data-toggle>
                                                        <button class="btn btn-default" type="button">
                                                            <i class="fa-solid fa-calendar-days"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="number">採購單號</label>
                                                <input class="form-control" id="number"
                                                    value="{{ $order_supplier['number'] ?? '' }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="supplier">供應商</label>
                                                <input class="form-control"
                                                    value="{{ $order_supplier['supplier_name'] }}" readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="receiver_name">收件人名稱</label>
                                                <input class="form-control" name="receiver_name" id="receiver_name"
                                                    value="{{ $order_supplier['receiver_name'] ?? '' }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="receiver_address">收件人地址</label>
                                                <input class="form-control" name="receiver_address" id="receiver_address"
                                                    value="{{ $order_supplier['receiver_address'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="currency_code">幣別</label>
                                                <input class="form-control" type="text" value="新台幣" readonly>
                                                <input class="form-control" type="hidden" name="currency_code"
                                                    value='TWD'>

                                                {{-- <select class="form-control js-select2-default" id="currency_code"
                                                name="currency_code" readonly>
                                                <option value='TWD'>新台幣</option>
                                            </select> --}}
                                            </div>

                                            <input type="hidden" name="currency_id" value="1">
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="currency_price">匯率</label>
                                                <input class="form-control" id="currency_price" name="currency_price"
                                                    value="1" readonly>
                                                <input type="hidden" name="exchange_rate" id="exchange_rate" value="1">
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="original_total_tax_price">原幣稅額</label>
                                                <input class="form-control" id="original_total_tax_price"
                                                    name="original_total_tax_price"
                                                    v-model="order_supplier.original_total_tax_price" readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="original_total_price">原幣總金額</label>
                                                <input class="form-control" id="original_total_price"
                                                    name="original_total_price"
                                                    v-model="order_supplier.original_total_price" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="tax">稅別</label>
                                                @switch($order_supplier['tax'])
                                                    @case(1)
                                                        <input class="form-control" id="tax_name" value="免稅" readonly>
                                                    @break
                                                    @case(2)
                                                        <input class="form-control" id="tax_name" value="應稅" readonly>
                                                    @break
                                                    @case(3)
                                                        <input class="form-control" id="tax_name" value="應稅內含" readonly>
                                                    @break
                                                    @default
                                                        <input class="form-control" id="tax_name" value="零稅率" readonly>
                                                @endswitch
                                                <input type="hidden" name="tax" id="tax"
                                                    value="{{ $order_supplier['tax'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="total_tax_price">稅額</label>
                                                <input class="form-control" id="total_tax_price" name="total_tax_price"
                                                    v-model="order_supplier.total_tax_price" readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="total_price">總金額</label>
                                                <input class="form-control" id="total_price" name="total_price"
                                                    v-model="order_supplier.total_price" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="invoice_company_number">發票統編</label>
                                                <input class="form-control" name="invoice_company_number"
                                                    id="invoice_company_number"
                                                    value="{{ $order_supplier['invoice_company_number'] ?? '' }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="invoice_name">發票抬頭</label>
                                                <input class="form-control" name="invoice_name" id="invoice_name"
                                                    value="{{ $order_supplier['invoice_name'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="invoice_address">發票地址</label>
                                                <input class="form-control" name="invoice_address" id="invoice_address"
                                                    value="{{ $order_supplier['invoice_address'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="doc_number">庫別</label>
                                                <input class="form-control" id="warehouse_name"
                                                    value="{{ $order_supplier['invoice_address'] ?? '' }}" readonly>
                                                <input type="hidden">
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="supplier_deliver_date">廠商交貨日</label>
                                                <div class="input-group" id="supplier_deliver_date_flatpickr">
                                                    <input type="text" class="form-control" name="supplier_deliver_date" id="supplier_deliver_date" value="{{ $order_supplier['supplier_deliver_date'] ?? '' }}" autocomplete="off" data-input />
                                                    <span class="input-group-btn" data-toggle>
                                                        <button class="btn btn-default" type="button">
                                                            <i class="fa-solid fa-calendar-days"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="expect_deliver_date">預計進貨日</label>
                                                <div class="input-group" id="expect_deliver_date_flatpickr">
                                                    <input type="text" class="form-control" name="expect_deliver_date" id="expect_deliver_date" value="{{ $order_supplier['expect_deliver_date'] ?? '' }}" autocomplete="off" data-input />
                                                    <span class="input-group-btn" data-toggle>
                                                        <button class="btn btn-default" type="button">
                                                            <i class="fa-solid fa-calendar-days"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- </div> --}}

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group" id="div_remark">
                                                <label for="remark">備註</label>
                                                <textarea class="form-control" rows="3" name="remark"
                                                    id="remark">{{ $order_supplier['remark'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <textarea style="display: none;"
                                        name="order_supplier_detail_json">@{{ order_supplier_detail }}</textarea>
                                    <hr>
                                    <h4><i class="fa-solid fa-table-cells-large"></i> 品項</h4>
                                    <div id="ItemDiv">
                                        <div class="add_row">
                                            <div class="row">
                                                <div class="col-sm-5 text-left">品項<span class="text-red">*</span>
                                                </div>
                                                <div class="col-sm-1 text-left">贈品</div>
                                                <div class="col-sm-1 text-left">單價<span class="text-red">*</span>
                                                </div>
                                                <div class="col-sm-1 text-left">請購量<span class="text-red">*</span>
                                                </div>
                                                <div class="col-sm-1 text-left">採購量<span class="text-red">*</span>
                                                </div>
                                                <div class="col-sm-1 text-left">單位</div>
                                                <div class="col-sm-1 text-left">最小採購量</div>
                                                <div class="col-sm-1 text-left">原幣小計<span class="text-red">*</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="add_row" v-for="(detail, detailKey) in order_supplier_detail">
                                            <div class="row">
                                                {{-- 品項 --}}
                                                <div class="col-sm-5">
                                                    <input class="form-control" v-model="detail.combination_name"
                                                        readonly>
                                                </div>
                                                {{-- 贈品 --}}
                                                <div class="col-sm-1">
                                                    <div v-if="detail.is_giveaway">
                                                        <input type="checkbox" class="big-checkbox" checked disabled>
                                                    </div>

                                                    <div v-else>
                                                        <input type="checkbox" class="big-checkbox" disabled>
                                                    </div>
                                                </div>
                                                {{-- 單價 --}}
                                                <div class="col-sm-1">
                                                    <input class="form-control" type="number" readonly
                                                        v-model="detail.item_price">
                                                </div>
                                                {{-- 請購量 --}}
                                                <div class="col-sm-1">
                                                    <input class="form-control" type="number" readonly
                                                        v-model="detail.purchase_detail_item_qty">
                                                </div>
                                                {{-- 採購量 --}}
                                                <div class="col-sm-1">
                                                    <div class="form-group">
                                                        <input class="form-control item_qty" v-model="detail.item_qty"
                                                            :max="detail.purchase_detail_item_qty"
                                                            :name="'item_qty['+detailKey+']'" type="number"
                                                            @change="detailsCount">
                                                    </div>
                                                </div>
                                                {{-- 單位 --}}
                                                <div class="col-sm-1">
                                                    <input class="form-control " readonly v-model="detail.uom">
                                                </div>
                                                {{-- 最小採購量 --}}
                                                <div class="col-sm-1">
                                                    <input class="form-control " readonly v-model="detail.min_purchase_qty">
                                                </div>
                                                {{-- 原幣小計 --}}
                                                <div class="col-sm-1">
                                                    <input class="form-control " readonly
                                                        v-model="detail.original_subtotal_price">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="status_code" id="status_code" v-model="status_code">
                                    <input type="hidden" name="order_supplier_id" id="order_supplier_id" value="">

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button class="btn btn-success" type="button"
                                                    @click="submitBtn('DRAFTED')">
                                                    <i class="fa-solid fa-floppy-disk"></i> 儲存草稿
                                                </button>
                                                <button class="btn btn-success" type="button"
                                                    @click="submitBtn('APPROVED')">
                                                    <i class="fa-solid fa-floppy-disk"></i> 儲存並核單
                                                </button>
                                                <button class="btn btn-danger" type="button" @click="cancel()">
                                                    <i class="fa-solid fa-ban"></i> 取消
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('js')
        <script>
            var requisitions = Vue.extend({
                data: function() {
                    return {
                        order_supplier: @json($order_supplier),
                        order_supplier_detail: @json($order_supplier_detail),
                        requisitions_purchase_options: @json(isset($data['requisitions_purchase']) ? $data['requisitions_purchase'] : '{}'),
                        requisitions_purchase_id: '',
                        status_code: '',
                    }
                },
                created() {

                },
                methods: {
                    submitBtn(status) {
                        this.status_code = status;
                        this.$nextTick(() => {
                            $('#new-form').submit();
                        });
                    },
                    NumberAdd(arg1, arg2) {
                        var r1, r2, m, n;
                        try {
                            r1 = arg1.toString().split(".")[1].length
                        } catch (e) {
                            r1 = 0
                        }
                        try {
                            r2 = arg2.toString().split(".")[1].length
                        } catch (e) {
                            r2 = 0
                        }
                        m = Math.pow(10, Math.max(r1, r2))
                        n = (r1 >= r2) ? r1 : r2;
                        return ((arg1 * m + arg2 * m) / m).toFixed(n);
                    },
                    cancel() {
                        return history.go(-1);
                        // console.log('取消');
                    },
                    detailsCount() {
                        var vm = this;
                        var taxtype = String(this.order_supplier.tax);
                        var sum_price = 0; //總計
                        var sum_original_total_tax_price = 0.00; //原幣稅額加總
                        var sum_total_tax_price = 0.00; //(本幣)稅額
                        $.each(this.order_supplier_detail, function(key, obj) {
                            if (obj.is_giveaway) { //如果是贈品則不計算單價
                                obj.item_price = 0;
                                obj.subtotal_tax_price = 0;
                                obj.original_subtotal_tax_price = 0;
                                obj.original_subtotal_price = 0;
                            } else {
                                obj.original_subtotal_price = obj.item_price * obj.item_qty; // (本幣)小計
                                obj.subtotal_price = obj.original_subtotal_price; //原幣小計
                                switch (taxtype) {
                                    case '0': //免稅
                                        obj.subtotal_tax_price = 0; //(本幣)稅額
                                        obj.original_subtotal_tax_price = 0 //原幣稅額
                                        break;
                                    case '2': //應稅內含
                                        obj.subtotal_tax_price = (obj.subtotal_price - (obj.subtotal_price /
                                            1.05)).toFixed(2); //(本幣)稅額
                                        obj.original_subtotal_tax_price = (obj.original_subtotal_price - (obj
                                            .original_subtotal_price / 1.05)).toFixed(2); //原幣稅額
                                        break;
                                    case '3': //零稅率
                                        obj.subtotal_tax_price = 0; //(本幣)稅額
                                        obj.original_subtotal_tax_price = 0 //原幣稅額
                                        break;
                                }
                            }
                            sum_price += obj.subtotal_price;
                            sum_total_tax_price = vm.NumberAdd(sum_total_tax_price, obj.subtotal_tax_price);
                            sum_original_total_tax_price = vm.NumberAdd(sum_original_total_tax_price, obj
                                .original_subtotal_tax_price);
                        });
                        switch (taxtype) {
                            case '0': //免稅
                                vm.order_supplier.original_total_tax_price = 0; //原幣稅額
                                vm.order_supplier.total_tax_price = 0; //稅額(本幣)
                                break;
                            case '2': //應稅內含
                                vm.order_supplier.total_tax_price = Number(sum_total_tax_price).toFixed(0); //稅額(本幣)
                                vm.order_supplier.original_total_tax_price = sum_original_total_tax_price; //原幣稅額
                                break;
                            case '3': //零稅率
                                vm.order_supplier.original_total_tax_price = 0; //原幣稅額
                                vm.order_supplier.total_tax_price = 0; //稅額(本幣)
                                break;
                        }
                        vm.order_supplier.original_total_price = sum_price; //原幣總金額
                        vm.order_supplier.total_price = sum_price; //總金額
                    },
                },

                mounted: function() {
                    $(".js-select2-requisitions_purchase_id").select2({
                        allowClear: false,
                        theme: "bootstrap",
                        placeholder: "請選擇"
                    });

                    flatpickr("#trade_date_flatpickr", {
                        dateFormat: "Y-m-d",
                    });

                    flatpickr("#supplier_deliver_date_flatpickr", {
                        dateFormat: "Y-m-d",
                    });

                    flatpickr("#expect_deliver_date_flatpickr", {
                        dateFormat: "Y-m-d",
                    });

                    $("#new-form").validate({
                        // debug: true,
                        submitHandler: function(form) {
                            $('#save_data').prop('disabled', true);
                            form.submit();
                        },
                        rules: {
                            trade_date: {
                                required: true,
                            },
                            requisitions_purchase_id: {
                                required: true,
                            },
                            supplier_deliver_date: {
                                dateGreaterEqualThan: function() {
                                    let obj = {
                                        date: $('#trade_date').val(),
                                        depends: true,
                                    }
                                    if ($('#supplier_deliver_date').val() !== '') {
                                        obj.depends = true;
                                    } else {
                                        obj.depends = false;
                                    }
                                    return obj;
                                },
                            },
                            //預計進貨日
                            expect_deliver_date: {
                                dateGreaterEqualThan: function() {
                                    let obj = {
                                        date: $('#supplier_deliver_date').val(),
                                        depends: true,
                                    }
                                    if ($('#expect_deliver_date').val() !== '') {
                                        obj.depends = true;
                                    } else {
                                        obj.depends = false;
                                    }
                                    return obj;
                                },
                            },
                        },
                        messages: {
                            supplier_deliver_date: {
                                dateGreaterEqualThan: "不可小於採購日期"
                            },
                            expect_deliver_date: {
                                dateGreaterEqualThan: "不可小於廠商交貨日"
                            },
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
                        success: function(label, element) {
                            $(element).closest(".form-group").removeClass("has-error");
                        },
                    });
                    $(".item_qty").each(function() {
                        var qty = $(this).val()  ;
                        $(this).rules("add", {
                            required: true,
                            digits: true,
                            min: 0.01,
                            messages: {
                                digits: '請輸入正整數',
                                max: '採購量不能大於請購量',
                                min: '請輸入正整數',
                            },
                        });
                    })
                },
            })

            new requisitions().$mount('#requisitions_vue_app');
        </script>

    @endsection

    {{-- @include('backend.order_supplier.item') --}}
@endsection
