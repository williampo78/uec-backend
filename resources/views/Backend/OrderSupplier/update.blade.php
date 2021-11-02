@extends('Backend.master')
@section('title', $data['act'] == 'upd' ? '編輯採購單' : '新建採購單')
@section('content')
<div id="page-wrapper">
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
                                            <label for="supplier">請購單</label>
                                            <input class="form-control" id="number" value="{{ $data['order_supplier']['requisitions_purchase_number'] ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    <input type="hidden" name="requisitions_purchase_id" value="{{$data['order_supplier']['requisitions_purchase_id']}}">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_trade_date">
                                            <label for="trade_date">採購日期</label>
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
                                            <label for="number">採購單號</label>
                                            <input class="form-control" id="number" value="{{ $data['order_supplier']['number'] ?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="supplier">供應商</label>
                                            <input class="form-control" value="{{ $data['order_supplier']['supplier_name'] }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="receiver_name">收件人名稱</label>
                                            <input class="form-control" name="receiver_name" id="receiver_name"
                                                value="{{ $data['order_supplier']['receiver_name'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="receiver_address">收件人地址</label>
                                            <input class="form-control" name="receiver_address" id="receiver_address"
                                                value="{{ $data['order_supplier']['receiver_address'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="currency_code">幣別</label>
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
                                                value="{{ $data['order_supplier']['original_total_tax_price'] ?? '' }}"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="original_total_price">原幣總金額</label>
                                            <input class="form-control" id="original_total_price"
                                                name="original_total_price"
                                                value="{{ $data['order_supplier']['original_total_price'] ?? '' }}"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="tax">稅別</label>
                                        @switch($data['order_supplier']['tax'])
                                            @case(1)
                                            <input class="form-control" id="tax_name" value="未稅" readonly>
                                                @break
                                            @case(2)
                                            <input class="form-control" id="tax_name" value="應稅" readonly>
                                                @break
                                            @case(3)
                                            <input class="form-control" id="tax_name" value="內含" readonly>

                                            @default
                                            <input class="form-control" id="tax_name" value="零稅率" readonly>
                                        @endswitch
                                            <input type="hidden" name="tax" id="tax" value="{{ $data['order_supplier']['tax'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="total_tax_price">稅額</label>
                                            <input class="form-control" id="total_tax_price" name="total_tax_price"
                                                value="{{ $data['order_supplier']['total_tax_price'] ?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="total_price">總金額</label>
                                            <input class="form-control" id="total_price" name="total_price"
                                                value="{{ $data['order_supplier']['total_price'] ?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="invoice_company_number">發票統編</label>
                                            <input class="form-control" name="invoice_company_number"
                                               id="invoice_company_number"
                                                value="{{ $data['order_supplier']['invoice_company_number'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="invoice_name">發票抬頭</label>
                                            <input class="form-control" name="invoice_name" id="invoice_name"
                                                value="{{ $data['order_supplier']['invoice_name'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="invoice_address">發票地址</label>
                                            <input class="form-control" name="invoice_address" id="invoice_address"
                                                value="{{ $data['order_supplier']['invoice_address'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="doc_number">庫別</label>
                                            <input class="form-control" id="warehouse_name" value="{{ $data['order_supplier']['invoice_address'] ?? '' }}" readonly>
                                            <input type="hidden">
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
                                <textarea style="display: none;" name="order_supplier_detail_json">@{{ order_supplier_detail }}</textarea>
                                <hr>
                                <h4><i class="fa fa-th-large"></i> 品項</h4>
                                <div id="ItemDiv">
                                    <div class="add_row">
                                        <div class="row">
                                            <div class="col-sm-2 text-left">商品編號<span class="redtext">*</span></div>
                                            <div class="col-sm-2 text-left">商品名稱</div>
                                            <div class="col-sm-2 text-left">單價<span class="redtext">*</span></div>
                                            <div class="col-sm-1 text-left">採購量<span class="redtext">*</span></div>
                                            <div class="col-sm-1 text-left">單位</div>
                                            <div class="col-sm-1 text-left">小計</div>
                                            <div class="col-sm-1 text-left">贈品</div>
                                            <div class="col-sm-1 text-left">最小採購量</div>
                                            <div class="col-sm-1 text-left">進貨量</div>
                                            {{-- <div class="col-sm-2 text-left">原幣小計<span class="redtext">*</span></div> --}}
                                            {{-- <div class="col-sm-1 text-left">功能</div> --}}
                                        </div>
                                    </div>
                                    <div class="add_row" v-for="(detail, detailKey) in order_supplier_detail">
                                        <div class="row">
                                            {{-- 商品編號 --}}
                                            <div class="col-sm-2">
                                                <input class="form-control" v-model="detail.item_number" readonly>
                                            </div>
                                            {{-- 商品名稱 --}}
                                            <div class="col-sm-2">
                                                <input class="form-control" v-model="detail.item_name" readonly>
                                            </div>
                                            {{-- 單價 --}}
                                            <div class="col-sm-2">
                                                <input class="form-control qty" type="number" readonly
                                                    v-model="detail.item_price">
                                            </div>
                                            {{-- 採購量 --}}
                                            <div class="col-sm-1"><input class="form-control"
                                                    v-model="detail.item_qty" type="number" min="0">
                                            </div>
                                            {{-- 單位 --}}
                                            <div class="col-sm-1">
                                                <input class="form-control" v-model="detail.item_unit" readonly>
                                            </div>
                                            {{-- 小計 --}}
                                            <div class="col-sm-1"><input class="form-control"
                                                    v-model="detail.subtotal_price" readonly>
                                            </div>
                                            {{-- 贈品 --}}
                                            <div class="col-sm-1">
                                                <input type="checkbox" class="big-checkbox" v-model="detail.is_giveaway"
                                                    :true-value="1" :false-value="0" readonl="readonly">
                                            </div>
                                            {{-- 最小採購量 --}}
                                            <div class="col-sm-1"><input class="form-control" readonly
                                                    value="最小採購量">
                                            </div>
                                            {{-- 進貨量 --}}
                                            <div class="col-sm-1"><input class="form-control" readonly
                                                    v-model="detail.purchase_qty">
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
                                            {{-- <button type="button" @click="test">測試</button> --}}
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
                    order_supplier_detail: @json($data['order_supplier_detail']),
                    requisitions_purchase_options: @json(isset($data['requisitions_purchase']) ? $data['requisitions_purchase'] : '{}'),
                    requisitions_purchase_id: '',
                    status_code: '' , 
                }
            },
            methods: {
                submitBtn(status) {
                    this.status_code = status ; 
                    this.$nextTick(() => {
                        $('#new-form').submit();
                    });
                },
                cancel() {
                    console.log('取消');
                },
                test() {
                    console.log('TEST')
                }
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
            },
            computed: {
                changeRequisitionsPurchase() {
                    console.log('抓到囉' + changeRequisitionsPurchase.inputVal);
                }
            },

        })

        new requisitions().$mount('#requisitions_vue_app');
    </script>

@endsection

{{-- @include('Backend.OrderSupplier.item') --}}
@endsection