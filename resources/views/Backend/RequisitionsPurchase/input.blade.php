@extends('Backend.master')

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">請輸入下列欄位資料</div>
                <div class="panel-body" id="requisitions_vue_app">
                    <form role="form" id="new-form" method="post" action="{{ route('quotation.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- 欄位 -->
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="supplier">
                                            <label for="supplier">供應商 <span class="redtext">*</span></label>
                                            <select class="form-control select2-vue-js" name="supplier_id" id="supplier_id">
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
                                            <input class="form-control" name="doc_number" id="doc_number"
                                                value="{{ $data['quotation']['doc_number'] ?? '' }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="supplier">
                                            <label for="supplier">倉庫 <span class="redtext">*</span></label>
                                            <select class="form-control select2-vue-js" name="tax" id="tax">
                                                @foreach ($warehouse as $obj)
                                                    {{-- {{$warehouse}} --}}
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
                                            <label for="total_tax_price">匯率 <span class="redtext">*</span></label>
                                            <input class="form-control" name="exchange_rate" id="exchange_rate" value="1"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_exchange_rate">
                                            <label for="total_tax_price">原幣稅額 <span class="redtext">*</span></label>
                                            <input class="form-control" name="exchange_rate" id="exchange_rate" value=""
                                                readonly>
                                            <input type="hidden" name="exchange_rate" id="exchange_rate" value="">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_exchange_rate">
                                            <label for="total_tax_price">原幣總金額 <span class="redtext">*</span></label>
                                            <input class="form-control" name="exchange_rate" id="exchange_rate" value=""
                                                readonly>
                                            <input type="hidden" name="exchange_rate" id="exchange_rate" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_currency_code">
                                            <label for="currency_code">稅別 <span class="redtext">*</span></label>
                                            <select class="form-control select2-vue-js" name="currency_code"
                                                id="currency_code">
                                                <option value="1">應稅</option>
                                                <option value="0">未稅</option>
                                                <option value="2">內含</option>
                                                <option value="3">零稅率</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_exchange_rate">
                                            <label for="total_tax_price">稅額 <span class="redtext">*</span></label>
                                            <input class="form-control" name="exchange_rate" id="exchange_rate" value=""
                                                readonly>
                                            <input type="hidden" name="exchange_rate" id="exchange_rate" value="">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="div_exchange_rate">
                                            <label for="total_tax_price">總金額 <span class="redtext">*</span></label>
                                            <input class="form-control" name="exchange_rate" id="exchange_rate" value=""
                                                readonly>
                                            <input type="hidden" name="exchange_rate" id="exchange_rate" value="">
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

                                <hr>
                                <h4><i class="fa fa-th-large"></i> 品項 </h4>
                                <div id="ItemDiv">
                                    <input type="hidden" name="rowNo" id="rowNo" value="0">
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
                                                <select2 :options="options" :details="details" v-model="details[detailKey].item_id"> </select2>
                                            </div>
                                            <div class="col-sm-1">
                                                <input type="checkbox" class="big-checkbox">
                                            </div>
                                            <div class="col-sm-2">
                                                <input class="form-control qty" type="number" readonly>
                                            </div>
                                            <div class="col-sm-1"><input class="form-control">
                                            </div>
                                            <div class="col-sm-1"><input class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-1"><input class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-1"><input class="form-control" readonly>
                                            </div>

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
                                <button type="button" @click="testBtn"> TEST </button>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <hr>
                                    </div>
                                </div>
                                <input type="hidden" name="status_code" id="status_code" value="">

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="btn btn-success" type="button">
                                                <i class="fa fa-save"></i>儲存草稿</button>
                                            <button class="btn btn-success" type="button"><i class="fa fa-save"></i>
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
                    // selected: {},
                    details: [],
                    options: @json(isset($item) ? $item : '{}')
                }
            },

            methods: {
                ItemListAdd() {
                    this.details.push({
                        original_subtotal_price: '', // 原幣小計
                        item_id: '', // 品項ID
                        item_number: '', //品項編號
                        item_name: '', //品項名稱
                        item_brand: '', //品項品牌
                        item_spec: '', //品項規格
                        item_unit: '', //品項單位
                        item_price: '', //單價
                        is_gift: false, // 是否為贈品
                    });
                },
                ItemListDel(id, key) {

                },
                submitBtn() {
                    // console.log('on submit');
                    // $("#formData").submit();
                },
                testBtn() {
                    console.log(this.details);
                }
            },
            mounted: function() {
                $(".select2-vue-js").select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: "請選擇"
                });
                $('#datetimepickera').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
            },

        })

        new requisitions().$mount('#requisitions_vue_app');

        //Vue Js 如果要用 select2 要另外寫 
        Vue.component("select2", {
            props: ["options", "value" ,"details"],
            template: "#select2-template",
            mounted: function() {
                var vm = this;
                // console.log(this.details) ;
                $(this.$el)
                    .select2({
                        data: this.options,
                        theme: "bootstrap",
                        placeholder: "請選擇"
                    })
                    .val(this.value)
                    .trigger("change")
                    .on("change", function() {
                        vm.$emit("input", this.value);
                        // console.log(this.value) ;
                    });
            },
            watch: {
                value: function(value) {
                    $(this.$el)
                        .val(value)
                        .trigger("change");
                    // console.log(value) ;
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
