@extends('Backend.master')
@section('title', $data['act'] == 'upd' ? '編輯採購單' : '新建採購單')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">請輸入下列欄位資料</div>
                <div class="panel-body" id="requisitions_vue_app">
                    <form role="form" id="new-form" method="post"
                        action="{{ $data['act'] == 'add' ? route('order_supplier.store') : route('order_supplier.update', $data['id']) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @if ($data['act'] == 'upd')
                            @method('PUT')
                        @endif

                        <div class="row">

                            <!-- 欄位 -->
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="supplier">請購單<span class="redtext">*</span></label>
                                            <select2 :options="requisitions_purchase_options"
                                                :order_supplier_detail="order_supplier_detail"
                                                :order_supplier="order_supplier" v-model="requisitions_purchase_id">
                                            </select2>
                                        </div>
                                    </div>
                                    {{-- <input type="hidden" name="requisitions_purchase_id" v-model="requisitions_purchase_id"> --}}
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_trade_date">
                                            <label for="trade_date">採購日期<span class="redtext">*</span></label>
                                            <div class='input-group date' id='datetimepicker'>
                                                <input type='text' class="form-control" name="trade_date" id="trade_date"
                                                    value="{{ $data['order_supplier']['trade_date'] ?? '' }}" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="number">採購單號<span class="redtext">*</span></label>
                                            <input class="form-control" id="number"
                                                value="{{ $data['order_supplier']['number'] ?? '' }}"
                                                v-model="order_supplier.number" readonly>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="supplier">供應商<span class="redtext">*</span></label>
                                            <input class="form-control" id="supplier"
                                                v-model="order_supplier.supplier_name"
                                                value="{{ isset($data['order_supplier']) && $data['supplier'][$data['order_supplier']['supplier_id']]['name'] ?? '' }}"
                                                readonly>
                                            <input type="hidden" id="supplier_id" name="supplier_id"
                                                v-model="order_supplier.supplier_id">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="receiver_name">收件人名稱</label>
                                            <input class="form-control" name="receiver_name" id="receiver_name"
                                                v-model="order_supplier.receiver_name"
                                                value="{{ $data['order_supplier']['receiver_name'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="receiver_address">收件人地址</label>
                                            <input class="form-control" name="receiver_address" id="receiver_address"
                                                v-model="order_supplier.receiver_address"
                                                value="{{ $data['order_supplier']['receiver_address'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="currency_code">幣別<span class="redtext">*</span></label>
                                            <input class="form-control" type="text" value="新台幣" readonly>
                                            <input class="form-control" type="hidden" name="currency_code" value='TWD'>

                                            {{-- <select class="form-control js-select2-default" id="currency_code" 
                                                name="currency_code" readonly>
                                                <option value='TWD'>新台幣</option>
                                            </select> --}}
                                        </div>

                                        <input type="hidden" name="currency_id" value="1">
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="currency_price">匯率<span class="redtext">*</span></label>
                                            <input class="form-control" id="currency_price" name="currency_price"
                                                value="1" readonly>
                                            <input type="hidden" name="exchange_rate" id="exchange_rate" value="1"
                                                v-model="order_supplier.exchange_rate">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="original_total_tax_price">原幣稅額<span
                                                    class="redtext">*</span></label>
                                            <input class="form-control" id="original_total_tax_price"
                                                name="original_total_tax_price"
                                                v-model="order_supplier.original_total_tax_price" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="original_total_price">原幣總金額<span
                                                    class="redtext">*</span></label>
                                            <input class="form-control" id="original_total_price"
                                                name="original_total_price" v-model="order_supplier.original_total_price"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="tax">稅別<span class="redtext">*</span></label>
                                            <input class="form-control" id="tax_name" v-model="order_supplier.tax_name"
                                                readonly>
                                            <input type="hidden" name="tax" id="tax" v-model="order_supplier.tex">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="total_tax_price">稅額<span class="redtext">*</span></label>
                                            <input class="form-control" id="total_tax_price" name="total_tax_price"
                                                v-model="order_supplier.total_tax_price" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="total_price">總金額<span class="redtext">*</span></label>
                                            <input class="form-control" id="total_price" name="total_price"
                                                v-model="order_supplier.total_price" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="invoice_company_number">發票統編</label>
                                            <input class="form-control" name="invoice_company_number"
                                                v-model="order_supplier.invoice_company_number" id="invoice_company_number"
                                                value="{{ $data['order_supplier']['invoice_company_number'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="invoice_name">發票抬頭</label>
                                            <input class="form-control" name="invoice_name" id="invoice_name"
                                                v-model="order_supplier.invoice_name"
                                                value="{{ $data['order_supplier']['invoice_name'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="invoice_address">發票地址</label>
                                            <input class="form-control" name="invoice_address" id="invoice_address"
                                                v-model="order_supplier.invoice_address"
                                                value="{{ $data['order_supplier']['invoice_address'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="doc_number">庫別</label>
                                            <input class="form-control" id="warehouse_name" value="庫別"
                                                v-model="order_supplier.warehouse_name" readonly>
                                            <input type="hidden" name="order_supplier.warehouse_id"
                                                v-model="order_supplier.warehouse_id">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="supplier_deliver_date">廠商交貨日</label>
                                            <div class='input-group date' id='datetimepicker2'>
                                                <input type='text' class="form-control" name="supplier_deliver_date"
                                                    id="supplier_deliver_date"
                                                    value="{{ $data['order_supplier']['supplier_deliver_date'] ?? '' }}" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="expect_deliver_date">預計進貨日</label>
                                            <div class='input-group date' id='datetimepicker3'>
                                                <input type='text' class="form-control" name="expect_deliver_date"
                                                    id="expect_deliver_date"
                                                    value="{{ $data['order_supplier']['expect_deliver_date'] ?? '' }}" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group" id="div_remark">
                                            <label for="remark">備註</label>
                                            <textarea class="form-control" rows="3" name="remark"
                                                id="remark">{{ $data['order_supplier']['remark'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <textarea type="hidden" style="display: none"
                                    name="order_supplier_detail_json">@{{ order_supplier_detail }}</textarea>
                                <hr>
                                <h4><i class="fa fa-th-large"></i> 品項</h4>
                                <div id="ItemDiv">
                                    <div class="add_row">
                                        <div class="row">
                                            <div class="col-sm-2 text-left">品項<span class="redtext">*</span></div>
                                            <div class="col-sm-1 text-left">贈品</div>
                                            <div class="col-sm-2 text-left">單價<span class="redtext">*</span></div>
                                            <div class="col-sm-1 text-left">請購量<span class="redtext">*</span></div>
                                            <div class="col-sm-1 text-left">採購量<span class="redtext">*</span></div>
                                            <div class="col-sm-1 text-left">單位</div>
                                            <div class="col-sm-1 text-left">最小採購量</div>
                                            <div class="col-sm-2 text-left">原幣小計<span class="redtext">*</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="add_row" v-for="(detail, detailKey) in order_supplier_detail">
                                        <div class="row">
                                            {{-- 品項 --}}
                                            <div class="col-sm-2">
                                                <input class="form-control" v-model="detail.item_no" readonly>
                                            </div>
                                            {{-- 贈品 --}}
                                            <div class="col-sm-1">
                                                <div v-if="detail.is_giveaway">
                                                    <input type="checkbox" class="big-checkbox" onclick="return false"
                                                        checked>
                                                </div>

                                                <div v-else>
                                                    <input type="checkbox" class="big-checkbox" onclick="return false">
                                                </div>
                                            </div>
                                            {{-- 單價 --}}
                                            <div class="col-sm-2">
                                                <input class="form-control" type="number" readonly
                                                    v-model="detail.item_price">
                                            </div>
                                            {{-- 請購量 --}}
                                            <div class="col-sm-1">
                                                <input class="form-control" type="number" readonly
                                                    v-model="detail.show_item_qty">
                                            </div>
                                            {{-- 採購量 --}}
                                            <div class="col-sm-1">
                                                <input class="form-control" type="number" v-model="detail.item_qty"
                                                    :min="0" :max="detail.show_item_qty" @change="detailsCount">
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

                                <div class="row">
                                    <div class="col-sm-12">
                                        <hr>
                                    </div>
                                </div>

                                <input type="hidden" name="status_code" id="status_code" v-model="status_code">
                                <input type="hidden" name="order_supplier_id" id="order_supplier_id" value="">

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="btn btn-success" type="button" @click="submitBtn('DRAFTED')"><i
                                                    class="fa fa-save"></i> 儲存草稿</button>
                                            <button class="btn btn-success" type="button" @click="submitBtn('APPROVED')"><i
                                                    class="fa fa-save"></i> 儲存並轉單</button>
                                            <button class="btn btn-danger" type="button" @click="cancel()"><i
                                                    class="fa fa-ban"></i> 取消</button>
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

@section('js')
    <script type="text/x-template" id="select2-template"><select><slot></slot></select></script>
    <script>
        var order_supplier = {
            supplier_name: '',
            original_total_tax_price: '',
            original_total_price: '',
            total_tax_price: '',
            total_price: '',
            tax_name: '',
            tax: '',
            warehouse_id: '',
            warehouse_name: '',
        };
        var order_supplier_detail = [];

        var requisitions = Vue.extend({
            data: function() {
                return {
                    order_supplier: order_supplier,
                    order_supplier_detail: order_supplier_detail,
                    requisitions_purchase_options: @json(isset($data['requisitions_purchase']) ? $data['requisitions_purchase'] : '{}'),
                    requisitions_purchase_id: '',
                    status_code: '',
                }
            },
            methods: {
                submitBtn(status) {
                    this.status_code = status;
                    this.$nextTick(() => {
                        $('#new-form').submit();
                    });
                },
                cancel() {
                    console.log('取消');
                },
                detailsCount() {
                    var taxtype = String(this.order_supplier.tax);

                    var original_total_tax_price = 0; // 原幣稅額
                    var original_total_price = 0; // 原幣總金額
                    var total_tax_price = 0; //(本幣)稅額
                    var total_price = 0; //(本幣)總金額
                    var sum_price = 0;
                    $.each(this.order_supplier_detail, function(key, obj) {
                        if (obj.is_gift) { //如果是贈品則不計算單價
                            obj.subtotal_price = 0;
                            obj.original_subtotal_price = 0;
                        } else {
                            switch (taxtype) {
                                case '0': //免稅
                                    price = obj.item_qty * obj.item_price;
                                    obj.original_subtotal_price = obj.item_qty * obj.item_price; // 數量 * 金錢
                                    sum_price += obj.original_subtotal_price;
                                    break;
                                case '1': //應稅
                                    alert('error');
                                    return false;
                                    break;
                                case '2': //應稅內含
                                    price = obj.item_qty * obj.item_price;
                                    obj.original_subtotal_price = price; // 數量 * 金錢
                                    sum_price += obj.original_subtotal_price;
                                    total_tax_price += ((price * 1.05).toFixed(2)) - price; //(本幣)稅額
                                    original_total_tax_price += ((price * 1.05).toFixed(2)) - price; //原幣稅額
                                    break;
                                case '3': //零稅率
                                    price = obj.item_qty * obj.item_price;
                                    obj.original_subtotal_price = price; // 數量 * 金錢
                                    sum_price += price;
                                    break;
                                default:
                                    break;
                            }
                        }
                    });
                    this.order_supplier.original_total_price = sum_price;
                    this.order_supplier.original_total_tax_price = original_total_tax_price;
                    this.order_supplier.total_tax_price = total_tax_price;
                    this.order_supplier.total_price = sum_price;
                    this.order_supplier.sum_price = sum_price;
                },
            },
            mounted: function() {
                $(".js-select2-requisitions_purchase_id").select2({
                    allowClear: false,
                    theme: "bootstrap",
                    placeholder: "請選擇"
                });
                $('#datetimepicker').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
                $('#datetimepicker2').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
                $('#datetimepicker3').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
                $("#new-form").validate({
                    debug: true,
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
                        }
                    },
                    messages: {
                        // end_launched_at: {
                        //     greaterThan: "結束時間必須大於開始時間",
                        // },
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
                        console.log(element);

                        // error.insertAfter(element);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).closest(".form-group").addClass("has-error");
                    },
                    success: function(label, element) {
                        $(element).closest(".form-group").removeClass("has-error");
                    },
                });
            },

        })
        Vue.component("select2", {
            props: ["options", "value", "order_supplier", "order_supplier_detail"],
            template: "#select2-template",
            mounted: function() {
                var vm = this;
                $(this.$el).select2({
                        data: this.options,
                        theme: "bootstrap",
                        placeholder: "請選擇",
                        allowClear: false,
                    })
                    .val(this.value)
                    .attr('name', 'requisitions_purchase_id')
                    .trigger("change")
                    .on("change", function() {
                        vm.$emit("input", this.value);
                    });
            },
            watch: {
                value: function(value) {
                    $(this.$el).val(value).trigger("change");
                    this.changeRequisitionsPurchase(value);
                },
                options: function(options) {
                    $(this.$el).empty().select2({
                        data: options
                    });
                }
            },
            methods: {
                changeRequisitionsPurchase(requisitions_purchase_id) {
                    var req = async () => {
                        const response = await axios.post('/backend/order_supplier/ajax', {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            type: 'getRequisitionsPurchase',
                            id: requisitions_purchase_id,
                        });
                        requisitionsPurchase = response.data.requisitionsPurchase;
                        order_supplier.supplier_name = requisitionsPurchase.supplier_name;
                        order_supplier.supplier_id = requisitionsPurchase.supplier_id;
                        order_supplier.original_total_tax_price = requisitionsPurchase
                            .original_total_tax_price; // 原幣稅額
                        order_supplier.original_total_price = requisitionsPurchase
                            .original_total_price; // 原幣總金額
                        order_supplier.total_tax_price = requisitionsPurchase.total_tax_price; //(本幣)稅額
                        order_supplier.total_price = requisitionsPurchase.total_price; //(本幣)總金額
                        order_supplier.tax = requisitionsPurchase.tax;
                        switch (requisitionsPurchase.tax) {
                            case 0:
                                order_supplier.tax_name = '免稅';
                                break;
                            case 1:
                                order_supplier.tax_name = '應稅';
                                break;
                            case 2:
                                order_supplier.tax_name = '應稅內含';
                                break;
                            case 3:
                                order_supplier.tax_name = '零稅率';
                                break;
                        }
                        order_supplier.tex = requisitionsPurchase.tax;
                        order_supplier.warehouse_name = requisitionsPurchase.warehouse_name;
                        order_supplier.warehouse_id = requisitionsPurchase.warehouse_id;
                        if (order_supplier_detail.length !== 0) {
                            order_supplier_detail.splice(0);
                        }
                        requisitionsPurchaseDetail = response.data.requisitionsPurchaseDetail;
                        $.each(requisitionsPurchaseDetail, function(key, obj) {
                            order_supplier_detail.push({
                                requisitions_purchase_dtl_id: obj
                                    .id, // requisitions_purchase_detail
                                product_item_id: obj.product_item_id, //  品項ID
                                combination_name: obj.combination_name, //顯示的品項名稱
                                item_no: obj.item_number, //編號
                                item_price: obj.item_price, //單價
                                show_item_qty: obj.item_qty, //顯示請購數量
                                item_qty: obj.item_qty, //數量
                                is_giveaway: obj.is_gift, //贈品
                                min_purchase_qty: obj.min_purchase_qty, //最小採購量
                                uom: obj.uom, //單位
                                original_subtotal_price: obj.original_subtotal_price, //原幣小計
                                subtotal_price: obj.subtotal_price,

                            });
                        });
                    }
                    req();
                }
            },
            destroyed: function() {
                $(this.$el).off().select2("destroy");
            }
        });

        new requisitions().$mount('#requisitions_vue_app');
    </script>

@endsection

{{-- @include('Backend.OrderSupplier.item') --}}
@endsection
