@extends('Backend.master')
@section('title', isset($requisitionsPurchase) ? '編輯請購單' : '新建請購單')
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
                                @csrf
                    @endif

                    <div class="row">
                        <input style="display:none;" name="id" value="{{ $requisitionsPurchase->id ?? '' }}">
                        <!-- 欄位 -->
                        <input type="hidden" v-model='switch_computed'>
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group" id="supplier">
                                        <label for="supplier">供應商
                                            <span class="redtext">*</span></label>
                                        <select class="form-control select2-vue-js" name="supplier_id" id="supplier_id">
                                            @foreach ($supplier as $obj)
                                                @if (!isset($requisitionsPurchase->supplier_id))
                                                    <option disabled selected> </option>
                                                @endif
                                                <option
                                                    {{ isset($requisitionsPurchase->supplier_id) && $requisitionsPurchase->supplier_id == $obj->id ? 'selected="selected"' : '' }}
                                                    value="{{ $obj->id }}">{{ $obj->name }}</option>
                                            @endforeach
                                            <input type="hidden" value="{{ $requisitionsPurchase->supplier_id ?? '' }}"
                                                id="old_supplier_id" name="old_supplier_id">
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
                                        <label for="doc_number">請購單號 <span class="redtext">*</span></label>
                                        <input class="form-control" name="number" id="number"
                                            value="{{ $requisitionsPurchase->number ?? '' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group" id="warehouse">
                                        <label for="warehouse">倉庫 <span class="redtext">*</span></label>
                                        <select class="form-control select2-vue-js" name="warehouse_id">
                                            @foreach ($warehouse as $obj)
                                                @if (!isset($requisitionsPurchase->warehouse_id))
                                                    <option disabled selected> </option>
                                                @endif
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
                                        <select class="form-control select2-vue-js" name="currency_code" id="currency_code">
                                            <option value='TWD'>新台幣</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group" id="div_exchange_rate">
                                        <label>匯率 <span class="redtext">*</span></label>
                                        <input class="form-control" name="currency_price" id="currency_price" value="1"
                                            readonly>
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
                                        <input class="form-control" name="original_total_price" id="original_total_price"
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
                                <div class="add_row detailsdata" v-for="(detail, detailKey) in details">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <select2 :selectkey="detailKey" :item_options="itemOptions" :details="details"
                                                :requisitions_purchase="requisitions_purchase"
                                                v-model="details[detailKey].product_item_id"> </select2>
                                        </div>
                                        <div class="col-sm-1">

                                            <input type="checkbox" class="big-checkbox"
                                                v-model="details[detailKey].is_gift" :true-value="1" :false-value="0">
                                        </div>
                                        {{-- 單價 --}}
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <input class="form-control qty item_price" type="number" readonly
                                                    v-model="details[detailKey].item_price"
                                                    :name="'item_price['+detailKey+']'">
                                            </div>
                                        </div>
                                        {{-- 數量 --}}
                                        <div class="col-sm-1">
                                            <div class="form-group">
                                                <input class="form-control item_qty" v-model="details[detailKey].item_qty"
                                                    :name="'item_qty['+detailKey+']'" :min="0" type="number">
                                            </div>
                                        </div>
                                        {{-- 單位 --}}
                                        <div class="col-sm-1"><input class="form-control" readonly
                                                v-model="details[detailKey].item_uom">
                                        </div>
                                        {{-- 最小採購量 --}}
                                        <div class="col-sm-1"><input class="form-control" readonly
                                                v-model="details[detailKey].min_purchase_qty">
                                        </div>
                                        {{-- 原幣小計 --}}
                                        <div class="col-sm-1"><input class="form-control" readonly
                                                v-model="details[detailKey].original_subtotal_price">
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
                                        <button class="btn btn-success" type="button" @click="submitBtn('REVIEWING')"><i
                                                class="fa fa-save"></i>
                                            儲存並核單</button>
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
    {{-- <script type="text/x-template" id="supplier-select"><select><slot></slot></select></script> --}}


    <script>
        var requisitions = Vue.extend({
            data: function() {
                return {
                    requisitions_purchase: @json(isset($requisitionsPurchase) ? $requisitionsPurchase : $requisitionsPurchaseDefault),
                    status: '',
                    details: [],
                    detailsUpdate: @json(isset($requisitionsPurchaseDetail) ? $requisitionsPurchaseDetail : []),
                    itemOptions: @json(isset($itemOptions) ? $itemOptions : []),
                    supplier: @json($supplier),
                    supplier_id: '',
                    switch_computed: 0,
                }
            },
            created() {
                var addArray = [];
                var vm = this;
                $.each(this.detailsUpdate, function(key, obj) {
                    addArray.push({
                        id: obj.id,
                        original_subtotal_price: obj.original_subtotal_price, // 原幣小計
                        product_item_id: String(obj.product_item_id), // 品項ID
                        item_number: obj.product_items_no, //品項編號
                        item_name: obj.product_name, //品項名稱
                        item_brand: '', //品項品牌(商品join)
                        min_purchase_qty: obj.min_purchase_qty, //最小採購量
                        item_uom: obj.uom, //品項單位(商品join)
                        item_price: obj.item_price, //單價
                        item_qty: obj.item_qty, //數量
                        is_gift: obj.is_gift, // 是否為贈品
                        subtotal_tax_price: obj.subtotal_tax_price, ////(本幣)稅額
                        original_subtotal_tax_price: obj.original_subtotal_tax_price, //原幣稅額
                        old_item_price: 0,
                    });
                });
                this.$nextTick(() => {
                    vm.details = addArray;
                    this.switch_computed = 1;
                });
            },
            methods: {
                //新增品項
                ItemListAdd() {
                    var vm = this;
                    if ($('#supplier_id').val() == null) {
                        alert('請先選擇供應商');
                        return false;
                    };
                    var supplier_id = $('#supplier_id').val();
                    axios.post('/backend/requisitions_purchase/ajax', {
                            get_type: 'getItemOption',
                            _token: '{{ csrf_token() }}',
                            supplier_id: supplier_id,
                        })
                        .then(function(response) {
                            vm.itemOptions = response.data.products_item;
                            vm.details.push({
                                id: '',
                                original_subtotal_price: 0, // 原幣小計
                                product_item_id: '', // 品項ID
                                item_number: '', //品項編號
                                item_name: '', //品項名稱
                                item_brand: '', //品項品牌(商品join)
                                min_purchase_qty: '', //最小採購量
                                item_uom: '', //品項單位(商品join)
                                item_price: 0, //單價
                                item_qty: 0, //數量
                                is_gift: false, // 是否為贈品
                                subtotal_tax_price: 0, ////(本幣)稅額
                                original_subtotal_tax_price: 0, //原幣稅額
                            });
                        })
                        .catch(function(error) {
                            console.log('ERROR');
                        })

                },
                //刪除品項
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
                                })
                        }
                        this.$delete(this.details, key)

                    }
                },
                //送出
                submitBtn(status) {
                    $(".item_qty").each(function() {
                        $(this).rules("add", {
                            required: true,
                            digits:true,
                            messages:{
                                digits: '請輸入正整數'
                            },
                        });
                    })
                    $(".item_price").each(function() {
                        $(this).rules("add", {
                            required: true,
                            digits: true,
                            messages: {
                                required: '選取品項取得單價(若選取後無取得單價表示該品項未過報價審核)'
                            },
                        });
                    })
                    this.status = status;
                    this.$nextTick(() => {
                        $('#new-form').submit();
                    });

                },
                //取得品項價格
                getItemLastPrice() {
                    var details = this.details;
                    var requisitions_purchase = this.requisitions_purchase;

                    $.each(details, function(key, obj) {
                        var whereGet = '?supplier_id=' + $('#supplier_id').val() +
                            '&currency_code=' + $('#currency_code').val() +
                            '&tax=' + requisitions_purchase.tax +
                            '&product_item_id=' + obj.product_item_id;
                        var req = async () => {
                            const response = await axios.get('/backend/getItemLastPrice/' +
                                whereGet);
                            details[key].item_price = response.data.item_price;
                            details[key].item_price = response.data.item_price;

                        }
                        req();
                    });
                },
                //計算小數
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
            },
            mounted: function mounted() {
                $(".select2-vue-js").select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: "請選擇",
                    allowClear: false,
                });
                $('#datetimepickera').datetimepicker({
                    format: 'YYYY-MM-DD',
                });

                $('#supplier_id').on('change', function() {
                    if ($('.detailsdata').length > 0) {
                        if ($('#old_supplier_id').val() !== '') {
                            if ($('#old_supplier_id').val() !== $('#supplier_id').find(":selected")
                                .val()) {
                                alert('請先將品項刪除在切換供應商')
                                $("#supplier_id").val($('#old_supplier_id').val()).trigger('change');
                            }
                        }
                    } else {
                        $('#old_supplier_id').val($('#supplier_id').find(":selected").val());
                    }
                });
                //驗證
                $("#new-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        var item_num = $('.detailsdata').length;
                        if (item_num <= 0) {
                            alert('至少輸入一個品項')
                            return false;
                        }
                        // $('#save_data').prop('disabled', true);
                        form.submit();
                    },
                    rules: {
                        supplier_id: {
                            required: true,
                        },
                        trade_date: {
                            required: true,
                        },
                        warehouse_id: {
                            required: true,
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
                this.details.push();
            },
            computed: {
                detailsCount() {
                    var vm = this;
                    if (this.switch_computed === 0) {
                        return false;
                    }
                    var details = this.details;
                    var requisitions_purchase = this.requisitions_purchase
                    var sum_price = 0;
                    var sum_original_total_tax_price = 0.00;
                    var sum_total_tax_price = 0.00;
                    $.each(details, function(key, obj) {
                        //原幣小計 = 單價 * 數量
                        if (obj.is_gift) { //如果是贈品則不計算單價
                            if (obj.item_price) {
                                obj.item_price = 0;
                                obj.subtotal_tax_price = 0 ;
                                obj.original_subtotal_tax_price = 0 ;
                                obj.original_subtotal_price = 0 ; 
                            }
                        } else {
                            if (vm.detailsUpdate.length > 0 && obj.old_item_price == 0) { //因為編輯撈取如果是贈品會抓不到 單價 需要另外ajax 取得
                                var whereGet = '?supplier_id=' + $('#supplier_id').val() +
                                    '&currency_code=' + $('#currency_code').val() +
                                    '&tax=' + requisitions_purchase.tax +
                                    '&product_item_id=' + obj.product_item_id;
                                var req = async () => {
                                    const response = await axios.get('/backend/getItemLastPrice/' +
                                        whereGet);
                                    details[key].item_price = response.data.item_price;
                                    details[key].old_item_price = response.data.item_price;
                                }
                                req();
                            } else {
                                obj.item_price = obj.old_item_price;
                            }
                        }
                        if (obj.item_qty > 0 && obj.item_price) {
                            obj.original_subtotal_price = obj.item_price * obj.item_qty; // (本幣)小計
                            obj.subtotal_price = obj.original_subtotal_price; //原幣小計
                            //各品項計算稅率
                            switch (requisitions_purchase.tax) {
                                case '0': //免稅
                                    obj.subtotal_tax_price = 0; //(本幣)稅額
                                    obj.original_subtotal_tax_price = 0 //原幣稅額
                                    break;
                                case '2': //應稅內含
                                    obj.subtotal_tax_price = (obj.subtotal_price - (obj.subtotal_price / 1.05)).toFixed(0); //(本幣)稅額
                                    obj.original_subtotal_tax_price = (obj.original_subtotal_price - (obj.original_subtotal_price / 1.05)).toFixed(2); //原幣稅額
                                    break;
                                case '3': //零稅率
                                    obj.subtotal_tax_price = 0; //(本幣)稅額
                                    obj.original_subtotal_tax_price = 0 //原幣稅額
                                    break;
                            }
                            // $tes = $ire ? : ;
                            //將稅金寫近來
                        } else {
                            obj.subtotal_price = 0;
                        }
                        sum_price += obj.subtotal_price;
                        sum_total_tax_price = vm.NumberAdd(sum_total_tax_price,obj.subtotal_tax_price);
                        sum_original_total_tax_price = vm.NumberAdd(sum_original_total_tax_price,obj.original_subtotal_tax_price);
                    });
                    //表頭計算稅
                    switch (requisitions_purchase.tax) {
                        case '0': //免稅
                            requisitions_purchase.original_total_tax_price = 0; //原幣稅額
                            requisitions_purchase.total_tax_price = 0; //稅額(本幣)
                            break;
                            // case '1': // 應稅
                            //     requisitions_purchase.original_total_tax_price = sum_price * 0.05; //原幣稅額
                            //     requisitions_purchase.total_tax_price = sum_price * 0.05; //稅額(本幣)
                            //     break;
                        case '2': //應稅內含                     
                            // 單頭金額欄位計算方式
                            requisitions_purchase.total_tax_price = sum_total_tax_price; //稅額(本幣)
                            requisitions_purchase.original_total_tax_price = sum_original_total_tax_price; //原幣稅額


                            break;
                        case '3': //零稅率
                            requisitions_purchase.original_total_tax_price = 0; //原幣稅額
                            requisitions_purchase.total_tax_price = 0; //稅額(本幣)
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
            props: ["item_options", "value", "details", "selectkey", "requisitions_purchase"],
            template: "#select2-template",
            mounted: function() {
                var vm = this;
                $(this.$el).select2({
                        data: this.item_options,
                        theme: "bootstrap",
                        placeholder: "請選擇",
                        allowClear: false,
                    })
                    .val(this.value)
                    .trigger("change")
                    .addClass("item_options")
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
                    $.each(this.item_options, function(key, obj) {
                        if (obj.id == value) {
                            details[getSelectKey].item_name = obj.name; //品項名稱
                            details[getSelectKey].item_number = obj.item_no; //品項編號
                            details[getSelectKey].item_brand = obj.brand; //品牌
                            details[getSelectKey].item_uom = obj.uom; // 單位
                            details[getSelectKey].min_purchase_qty = obj.min_purchase_qty; //最小採購量
                            // details[getSelectKey].item_qty = obj.min_purchase_qty; //最小採購量
                            var find_this_item_id = obj.id;
                            //帶出價格
                            var whereGet = '?supplier_id=' + $('#supplier_id').val() +
                                '&currency_code=' + $('#currency_code').val() + '&tax=' +
                                requisitions_purchase.tax + '&product_item_id=' + find_this_item_id;
                            const req = async () => {
                                const response = await axios.get('/backend/getItemLastPrice/' +
                                    whereGet);
                                details[getSelectKey].item_price = response.data.item_price;
                                details[getSelectKey].old_item_price = response.data.item_price;
                            }
                            req();
                        }
                    });
                },
                options: function(itemOptions) {
                    $(this.$el)
                        .empty()
                        .select2({
                            data: itemOptions
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
