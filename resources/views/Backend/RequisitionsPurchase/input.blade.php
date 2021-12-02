@extends('Backend.master')
@section('title', isset($requisitionsPurchase) ? '新建請購單' : '編輯請購單' )
@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">請輸入下列欄位資料</div>
                <div class="panel-body" id="requisitions_vue_app">
                    @if (isset($requisitionsPurchase))
                        <form role="form" id="new-form" method="POST"
                            action="{{ route('requisitions_purchase.update', $requisitionsPurchase->id) }}"
                            enctype="multipart/form-data" novalidate="novalidate">
                            @method('PUT')
                            @csrf
                        @else
                            <form role="form" id="new-form" method="post"
                                action="{{ route('requisitions_purchase.store') }}" enctype="multipart/form-data">
                    @endif
                    <form role="form" id="new-form" method="post" action="{{ route('requisitions_purchase.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input style="display:none;" name="id" value="{{ $requisitionsPurchase->id ?? '' }}">
                            <!-- 欄位 -->
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="supplier">
                                            <label for="supplier">供應商
                                                <span class="redtext">*</span></label>
                                            <select class="form-control select2-vue-js" name="supplier_id" id="supplier_id">
                                                @foreach ($supplier as $obj)
                                                    @if (isset($requisitionsPurchase))
                                                        <option value="{{ $obj->id }}"
                                                            {{ $obj->id == $requisitionsPurchase->supplier_id ? 'selected="selected"' : '' }}>
                                                            {{ $obj->name }}</option>
                                                    @else
                                                        <option value="{{ $obj->id }}">{{ $obj->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_trade_date">
                                            <label for="trade_date">請購日期 <span class="redtext">*</span></label>
                                            <div class='input-group date' id='datetimepickera'>
                                                <input type='text' class="form-control" name="trade_date" id="trade_date"
                                                    value="{{ isset($requisitionsPurchase->trade_date) ? $requisitionsPurchase->trade_date : date('Y-m-d') }}" />
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
                                                value="{{ $requisitionsPurchase->number ?? '' }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="supplier">
                                            <label for="supplier">倉庫 <span class="redtext">*</span></label>
                                            <select class="form-control select2-vue-js" name="warehouse_id">
                                                @foreach ($warehouse as $obj)
                                                    @if (isset($requisitionsPurchase))
                                                        <option value="{{ $obj->id }}"
                                                            {{ $obj->id == $requisitionsPurchase->warehouse_id ? 'selected="selected"' : '' }}>
                                                            {{ $obj->name }}</option>
                                                    @else
                                                        <option value="{{ $obj->id }}">{{ $obj->name }}</option>
                                                    @endif
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
                                                id="original_total_tax_price"
                                                v-model="requisitions_purchase.original_total_tax_price" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_exchange_rate">
                                            <label>原幣總金額 <span class="redtext">*</span></label>
                                            <input class="form-control" name="original_total_price"
                                                id="original_total_price"
                                                v-model="requisitions_purchase.original_total_price" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_currency_code">
                                            <label for="currency_code">稅別<span class="redtext">*</span></label>
                                            <select class="form-control" name="tax" id="tax"
                                                v-model="requisitions_purchase.tax" @change="getItemLastPrice">
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
                                                v-model="requisitions_purchase.total_tax_price" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_exchange_rate">
                                            <label>總金額 <span class="redtext">*</span></label>
                                            <input class="form-control" name="total_price" id="total_price"
                                                v-model="requisitions_purchase.total_price" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group" id="div_remark">
                                            <label for="remark">備註</label>
                                            <textarea class="form-control" rows="3" name="remark"
                                                id="remark">{{ $requisitionsPurchase->remark ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <textarea style="display:none"
                                    name="requisitions_purchase_detail">@{{ details }}</textarea>
                                <textarea style="display:none"> @{{ detailsCount }}</textarea>
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
                                                <select2 :selectkey="detailKey" :options="options" :details="details"
                                                    :requisitions_purchase="requisitions_purchase"
                                                    v-model="details[detailKey].item_id"> </select2>
                                            </div>
                                            <div class="col-sm-1">

                                                <input type="checkbox" class="big-checkbox"
                                                    v-model="details[detailKey].is_gift" :true-value="1" :false-value="0">
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
                                                    v-model="details[detailKey].subtotal_price">
                                            </div>
                                            {{-- 功能 --}}
                                            <div class="col-sm-1">
                                                <button type="button" class="btn btn-danger"
                                                    @click="ItemListDel(details[detailKey].id,detailKey)">
                                                    <i class="fa fa-ban"></i>刪除
                                                </button>
                                            </div>
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
                                <input type="hidden" id="status" v-model="status" name="status">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="btn btn-success" type="button" @click="submitBtn('DRAFTED')"> <i
                                                    class="fa fa-save"></i>儲存草稿</button>
                                            <button class="btn btn-success" type="button"
                                                @click="submitBtn('REVIEWING')"><i class="fa fa-save"></i>
                                                儲存並送審</button>
                                            <a href="{{ route('requisitions_purchase') }}" class="btn btn-danger"
                                                type="button"><i class="fa fa-ban"></i>
                                                取消</a>
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
                    requisitions_purchase: @json(isset($requisitionsPurchase) ? $requisitionsPurchase : $requisitionsPurchaseDefault),
                    status: '',
                    details: @json(isset($requisitionsPurchaseDetail) ? $requisitionsPurchaseDetail : []),
                    options: @json(isset($item) ? $item : '{}')
                }
            },
            methods: {
                ItemListAdd() {
                    this.details.push({
                        id: '',
                        subtotal_price: '', // 原幣小計
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
                    var checkDel = confirm('你確定要刪除嗎？');
                    if (checkDel) {
                        if (id !== '') { //如果ID 不等於空 就 AJAX DEL
                            axios({
                                    method: 'delete',
                                    url: '/backend/requisitions_purchase/' + id + '?&type=Detail'
                                }).then(function(response) {
                                    if (response.data.status) {
                                        alert('刪除成功');
                                    }
                                })
                                .catch(function(error) {
                                    console.log(error);
                                    console.log('ERROR');
                                })
                        }
                        this.$delete(this.details, key)

                    }
                },
                submitBtn(status) {
                    var details = this.details;
                    this.status = status; //訂單狀態
                    var check_item_status = true; //檢查表單內容 給予狀態

                    console.log(details);
                    $.each(details, function(key, obj) {
                        if (obj.item_price == null) {
                            check_item_status = false;
                        }
                        if (obj.item_id == "") {
                            check_item_status = false;
                        }
                    });
                    if (details.length == 0) {
                        check_item_status = false;
                    }
                    this.$nextTick(() => {
                        if (details.length == 0 && this.status !== 'DRAFTED') {
                            alert('請填入品項')
                            return false;
                        }
                        if (check_item_status == false && this.status == 'REVIEWING') {
                            alert('品項點選未帶入單價 表示該品項上未通過任何報價審核喔!')
                            return false;

                        }
                        if (check_item_status == false && this.status == 'DRAFTED') {
                            $('#new-form').submit();
                        } else if (check_item_status == true) {
                            $('#new-form').submit();
                        }
                    });

                },
                getItemLastPrice() {
                    var details = this.details;
                    var requisitions_purchase = this.requisitions_purchase;

                    $.each(details, function(key, obj) {
                        var whereGet = '?supplier_id=' + $('#supplier_id').val() +
                            '&currency_code=' + $('#currency_code').val() +
                            '&tax=' + requisitions_purchase.tax +
                            '&item_id=' + obj.item_id;
                        var req = async () => {
                            const response = await axios.get('/backend/getItemLastPrice/' +
                                whereGet);
                            details[key].item_price = response.data.original_unit_price;
                            console.log(response.data);
                        }
                        req();
                    });
                }
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
                detailsCount() {
                    var details = this.details;
                    var requisitions_purchase = this.requisitions_purchase
                    var sum_price = 0;
                    $.each(details, function(key, obj) {
                        // details[key].item_price = 0  ;
                        //原幣小計 = 單價 * 數量
                        if (obj.is_gift == 1) { //如果是贈品則不計算單價
                            obj.subtotal_price = 0;
                        } else if (obj.item_qty > 0) {
                            obj.subtotal_price = obj.item_price * obj.item_qty;
                        } else {
                            obj.subtotal_price = 0;
                        }
                        sum_price += obj.subtotal_price;
                    });
                    switch (requisitions_purchase.tax) {
                        case '0':
                            // console.log('未稅');
                            requisitions_purchase.original_total_tax_price = 0; //原幣稅額
                            requisitions_purchase.total_tax_price = 0; //稅額
                            break;
                        case '1':
                            // console.log('應稅');
                            requisitions_purchase.original_total_tax_price = sum_price * 0.05; //原幣稅額
                            requisitions_purchase.total_tax_price = sum_price * 0.05; //稅額
                            break;
                        case '2':
                            // console.log('內含');
                            requisitions_purchase.original_total_tax_price = (sum_price * 0.0476).toFixed(2); //原幣稅額
                            requisitions_purchase.total_tax_price = (sum_price * 0.0476).toFixed(2); //稅額
                            break;
                        case '3':
                            // console.log('零稅率');
                            requisitions_purchase.original_total_tax_price = 0; //原幣稅額
                            requisitions_purchase.total_tax_price = 0; //稅額
                            break;
                    }
                    requisitions_purchase.original_total_price = sum_price; //原幣總金額
                    requisitions_purchase.total_price = sum_price; //總金額
                    return details;
                },

            },

        })

        new requisitions().$mount('#requisitions_vue_app');

        //Vue Js 如果要用 select2 要另外寫
        Vue.component("select2", {
            props: ["options", "value", "details", "selectkey", "requisitions_purchase"],
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
                    .trigger("change")
                    .on("change", function() {
                        vm.$emit("input", this.value);
                    });
            },
            watch: {
                value: function(value) {
                    var getSelectKey = this.selectkey; //這個u_id 當作找到選擇器的排序
                    var details = this.details;
                    var requisitions_purchase = this.requisitions_purchase;
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
                            // details[getSelectKey].item_price = obj.sell_price1; //目前先以販售價格**要用審核過後的價格帶出
                            var find_this_item_id = obj.id;
                            //帶出價格
                            var whereGet = '?supplier_id=' + $('#supplier_id').val() +
                                '&currency_code=' + $('#currency_code').val() +
                                '&tax=' + requisitions_purchase.tax +
                                '&item_id=' + find_this_item_id;
                            const req = async () => {
                                const response = await axios.get('/backend/getItemLastPrice/' +
                                    whereGet);
                                details[getSelectKey].item_price = response.data
                                    .original_unit_price;
                            }
                            req();
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
