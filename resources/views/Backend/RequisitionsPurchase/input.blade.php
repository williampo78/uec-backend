@extends('Backend.master')

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">請輸入下列欄位資料</div>
                <div class="panel-body" id="requisitions_vue_app">
                    <form role="form" id="new-form" method="post" action="{{ route('requisitions_purchase.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- 欄位 -->
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="supplier">
                                            {{-- 目前是以填單日 --}}
                                            <label for="supplier">供應商 <span class="redtext">*</span></label>
                                            <select class="form-control select2-vue-js" name="trade_date" id="trade_date">
                                                @foreach ($supplier as $obj)
                                                    <option value="{{ $obj->id }}">{{ $obj->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_trade_date">
                                            <label for="trade_date">請購日期 <span class="redtext">*</span></label>
                                            <div class='input-group date' id='datetimepickera'>
                                                <input type='text' class="form-control" name="submitted_at"
                                                    id="submitted_at" value="{{ date('Y-m-d') }}" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_doc_number">
                                            <label for="doc_number">報價單號 <span class="redtext">*</span></label>
                                            <input class="form-control" name="number" id="number"
                                                value="{{ $data['number']['number'] ?? '' }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="supplier">
                                            <label for="supplier">倉庫 <span class="redtext">*</span></label>
                                            <select class="form-control select2-vue-js" name="warehouse_id">
                                                @foreach ($warehouse as $obj)
                                                    <option value="{{ $obj->id }}">{{ $obj->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group" id="div_currency_code">
                                            <label for="currency_code">幣別 <span class="redtext">*</span></label>
                                            <select class="form-control select2-vue-js" name="currency_code"
                                                id="currency_code">
                                                <option value='TWD'>新台幣</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group" id="div_exchange_rate">
                                            <label>匯率 <span class="redtext">*</span></label>
                                            <input class="form-control" name="currency_price" id="currency_price"
                                                value="1" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_exchange_rate">
                                            <label>原幣稅額 <span class="redtext">*</span></label>
                                            <input class="form-control" name="original_total_tax_price"
                                                id="original_total_tax_price" v-model="original_total_tax_price" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_exchange_rate">
                                            <label>原幣總金額 <span class="redtext">*</span></label>
                                            <input class="form-control" name="original_total_price"
                                                id="original_total_price" v-model="original_total_price" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_currency_code">
                                            <label for="currency_code">稅別<span class="redtext">*</span></label>
                                            <select class="form-control" name="tax" id="tax" v-model="tax">
                                                @foreach ($taxList as $id => $tax)
                                                    <option value="{{ $id }}">{{ $tax }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_exchange_rate">
                                            <label>稅額 <span class="redtext">*</span></label>
                                            <input class="form-control" name="total_tax_price" id="total_tax_price"
                                                v-model="total_tax_price" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_exchange_rate">
                                            <label>總金額 <span class="redtext">*</span></label>
                                            <input class="form-control" name="total_price" id="total_price"
                                                v-model="total_price" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group" id="div_remark">
                                            <label for="remark">備註</label>
                                            <textarea class="form-control" rows="3" name="remark"
                                                id="remark">{{ $data['quotation']['remark'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <textarea name="requisitions_purchase_detail" id="" cols="100"
                                    rows="10">@{{ details }}</textarea>
                                <textarea cols="30" rows="10"> @{{ detailsCount }}</textarea>
                                <hr>
                                <h4><i class="fa fa-th-large"></i> 品項 </h4>
                                <div id="ItemDiv">
                                    <div class="add_row">
                                        <div class="row">
                                            <div class="col-sm-3 text-left">品項<span class="redtext">*</span></div>
                                            <div class="col-sm-1 text-left">贈品</div>
                                            <div class="col-sm-2 text-left">單價<span class="redtext">*</span></div>
                                            <div class="col-sm-1 text-left">數量<span class="redtext">*</span></div>
                                            <div class="col-sm-1 text-left">單位</div>
                                            <div class="col-sm-1 text-left">最小採購量</div>
                                            <div class="col-sm-1 text-left">原幣小計<span class="redtext">*</span></div>
                                            <div class="col-sm-1 text-left">功能</div>
                                        </div>
                                    </div>
                                    <div class="add_row" v-for="(detail, detailKey) in details">

                                        <div class="row">
                                            <div class="col-sm-3">
                                                <select2 :options="options" :details="details"
                                                    v-model="details[detailKey].item_id"> </select2>
                                            </div>
                                            <div class="col-sm-1">
                                                <input type="checkbox" class="big-checkbox">
                                            </div>
                                            {{-- 單價 --}}
                                            <div class="col-sm-2">
                                                <input class="form-control qty" type="number" readonly
                                                    v-model="details[detailKey].item_price">
                                            </div>
                                            {{-- 數量 --}}
                                            <div class="col-sm-1"><input class="form-control"
                                                    v-model="details[detailKey].item_qty" type="number" min="0">
                                            </div>
                                            {{-- 單位 --}}
                                            <div class="col-sm-1"><input class="form-control" readonly
                                                    v-model="details[detailKey].item_price">
                                            </div>
                                            {{-- 最小採購量 --}}
                                            <div class="col-sm-1"><input class="form-control" readonly
                                                    v-model="details[detailKey].item_price">
                                            </div>
                                            {{-- 原幣小計 --}}
                                            <div class="col-sm-1"><input class="form-control" readonly
                                                    v-model="details[detailKey].original_subtotal_price">
                                            </div>
                                            {{-- 功能 --}}
                                            <div class="col-sm-1"><button class="btn btn-danger btn_close"><i
                                                        class="fa fa-ban"></i>刪除</button></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        <a class="btn btn-warning" id="btn-addNewRow" @click="ItemListAdd"><i
                                                class="fa fa-plus"></i>
                                            新增品項</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <hr>
                                    </div>
                                </div>
                                <input type="hidden" id="status_code" v-model="status_code" name="status_code">
                                <div class="row">
                                    @{{ status_code }}

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="btn btn-success" type="button" @click="submitBtn('DRAFTED')"> <i
                                                    class="fa fa-save"></i>儲存草稿</button>
                                            <button class="btn btn-success" type="button"
                                                @click="submitBtn('REVIEWING')"><i class="fa fa-save"></i>
                                                儲存並送審</button>
                                            <button class="btn btn-danger" type="button"><i class="fa fa-ban"></i>
                                                取消</button>
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

@endsection

@section('js')
    <script type="text/x-template" id="select2-template"><select><slot></slot></select></script>
    <script>
        var requisitions = Vue.extend({
            data: function() {
                return {
                    original_total_tax_price: 0, //原幣稅額
                    original_total_price: 0, //原幣總金額  
                    total_tax_price: 0, //稅額
                    total_price: 0, //總金額
                    status_code: '',
                    tax: '',
                    details: [],
                    options: @json(isset($item) ? $item : '{}')
                }
            },
            methods: {
                ItemListAdd() {
                    this.details.push({
                        id: '',
                        original_subtotal_price: '', // 原幣小計
                        item_id: '', // 品項ID
                        item_number: '', //品項編號
                        item_name: '', //品項名稱
                        item_brand: '', //品項品牌
                        item_spec: '', //品項規格
                        item_unit: '', //品項單位
                        item_price: '', //單價
                        item_qty: 0, //數量
                        is_gift: false, // 是否為贈品
                    });
                },
                ItemListDel(id, key) {

                },
                submitBtn(status_code) {
                    this.status_code = status_code;
                    this.$nextTick(() => {
                        $('#new-form').submit();
                    });
                },
            },
            mounted: function() {
                $(".select2-vue-js").select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: "請選擇",
                    allowClear: false,
                });
                $('#datetimepickera').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
                this.details.push();
            },
            computed: {
                detailsCount() { //笛卡兒積演算法
                    var details = this.details;
                    var sum_price = 0;
                    $.each(details, function(key, obj) {
                        //原幣小計 = 單價 * 數量 
                        if (obj.item_qty > 0 && obj.item_qty !== '') {
                            obj.original_subtotal_price = obj.item_price * obj.item_qty;
                        } else {
                            obj.original_subtotal_price = 0;
                        }
                        switch (this.tax) {
                            case 0:
                                console.log('未稅');
                                break;
                            case 1:
                                console.log('應稅');
                                break;
                            case 2:
                                console.log('內含');
                                break;
                            case 3:
                                console.log('零稅率');
                                break;
                            default:
                                break;
                        }
                        sum_price += obj.original_subtotal_price;

                    });
                    this.original_total_tax_price = 100; //原幣稅額
                    this.total_tax_price = 100; //稅額
                    this.original_total_price = sum_price; //原幣總金額  
                    this.total_price = sum_price; //總金額
                    return details;
                },
            },

        })

        new requisitions().$mount('#requisitions_vue_app');

        //Vue Js 如果要用 select2 要另外寫 
        Vue.component("select2", {
            props: ["options", "value", "details"],
            template: "#select2-template",
            mounted: function() {
                var vm = this;
                $(this.$el)
                    .select2({
                        data: this.options,
                        theme: "bootstrap",
                        placeholder: "請選擇",
                        allowClear: false,
                    })
                    .val(this.value)
                    .trigger("change")
                    .on("change", function() {
                        vm.$emit("input", this.value);
                    });
            },
            watch: {
                value: function(value) {
                    var getSelectKey = this._uid - 1; //這個u_id 當作找到選擇器的排序
                    var details = this.details;
                    // console.log(details) ;
                    $(this.$el)
                        .val(value)
                        .trigger("change");

                    $.each(this.options, function(key, obj) {
                        if (obj.id == value) {
                            details[getSelectKey].item_name = obj.name; //品項名稱
                            details[getSelectKey].item_number = obj.item_number; //品項編號
                            details[getSelectKey].item_brand = obj.brand; //品牌
                            details[getSelectKey].item_spec = obj.spec; //規格
                            details[getSelectKey].item_unit = obj.small_unit; // 單位
                            details[getSelectKey].item_price = obj.sell_price1; //目前先以販售價格**要用審核過後的價格帶出
                        }
                    });
                },
                options: function(options) {
                    $(this.$el)
                        .empty()
                        .select2({
                            data: options
                        });
                }
            },
            destroyed: function() {
                $(this.$el)
                    .off()
                    .select2("destroy");
            }
        });
    </script>

@endsection
