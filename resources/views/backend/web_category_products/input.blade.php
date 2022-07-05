@extends('backend.layouts.master')
@section('title', '分類階層內容管理')
@section('content')
    <style>
        .modal-dialog {
            max-width: 100%;
        }

        #products_model_list_info {
            display: none;
        }

        #products_model_list_paginate {
            display: none;
        }

        .nav-tabs li a {
            color: #333;
            pointer-events: none;
        }

        .nav-tabs .active a {
            font-weight: bold;
        }

    </style>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-list"></i>分類階層內容編輯</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body" id="category_hierarchy_content_input" v-cloak>
                        <form role="form" id="update-form" method="POST"
                            action="{{ route('web_category_products.update', $category_hierarchy_content->id) }}"
                            enctype="multipart/form-data" novalidate="novalidate">
                            @method('PUT')
                            @csrf
                            <div class="row">
                                <!-- 欄位 -->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_doc_number">
                                                <label for="doc_number">分類 <span class="text-red">*</span></label>
                                                <input class="form-control" name="id" id="id"
                                                    value="{{ $category_hierarchy_content->name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_doc_number">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label for="doc_number">狀態 <span
                                                                class="text-red">*</span></label>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="active" id="inlineRadio1" value="1"
                                                                {{ $category_hierarchy_content->active == 1 ? "checked='checked'" : '' }}>開啟
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="active" id="inlineRadio2" value="0"
                                                                {{ $category_hierarchy_content->active == 0 ? "checked='checked'" : '' }}>關閉
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_doc_number">
                                                <label for="doc_number">網頁標題</label>
                                                <input class="form-control" name="meta_title" id="meta_title"
                                                    value="{{ $category_hierarchy_content->meta_title }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_doc_number">
                                                <label for="doc_number">網頁描述</label>
                                                <input class="form-control" name="meta_description" id="meta_description"
                                                    value="{{ $category_hierarchy_content->meta_description }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_doc_number">
                                                <label for="doc_number">網頁關鍵字</label>
                                                <input class="form-control" name="meta_keyword" id="meta_keyword"
                                                    value="{{ $category_hierarchy_content->meta_keywords }}">
                                            </div>
                                        </div>
                                    </div>
                                    <textarea style="display: none" name="category_products_list_json" cols="30" rows="10">@{{ category_products_list }}</textarea>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_doc_number">
                                                    <div class="col-sm-4">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="content_type" id="content_type1"
                                                                v-model="category_hierarchy_content.content_type"
                                                                :value="'P'">指定商品
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="content_type" id="content_type2"
                                                                v-model="category_hierarchy_content.content_type"
                                                                :value="'M'">指定賣場
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                    @include('backend.web_category_products.tab_list')
                                    <div class="row">
                                        <div class="col-sm-1 pull-right">
                                            <a class="btn btn-danger" type="button" href="{{redirect()->back()->getTargetUrl()}}"><i class="fa-solid fa-ban"></i> 取消</a>
                                        </div>
                                        <div class="col-sm-1 pull-right">
                                                <button type="button" class="btn btn-success " @click="submit" id="btn-save"><i class="fa-solid fa-floppy-disk"></i> 儲存</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @include('backend.web_category_products.input_detail')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        var products = Vue.extend({
            data: function() {
                return {
                    category_products_list: @json($category_products_list),
                    select_req: {
                        product_no: '',
                        product_name: '',
                        selling_price_min: '',
                        selling_price_max: '',
                    },
                    category_hierarchy_content: @json($category_hierarchy_content),
                    result_products: [],
                    result_promotional_campaigns:[],

                }
            },
            methods: {
                productsGetAjax() {
                    var start_created_at = $('input[name="start_created_at"]').val();
                    var end_created_at = $('input[name="end_created_at"]').val();
                    var start_launched_at_start = $('input[name="start_launched_at_start"]').val();
                    var start_launched_at_end = $('input[name="start_launched_at_end"]').val();
                    var selling_price_min = $('input[name="selling_price_min"]').val();
                    var selling_price_max = $('input[name="selling_price_max"]').val();
                    var supplier_id = $('#supplier').val();
                    var limit = $('#limit').val();
                    var product_type = $('#product_type').val();
                    var filter_product_id = [];
                    this.category_products_list.find((todo, index) => {
                        filter_product_id.push(todo.product_id);
                    })
                    var req = async () => {
                        const response = await axios.post('/backend/web_category_products/ajax', {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            type: 'getProductsList',
                            supplier_id: supplier_id,
                            product_no: this.select_req.product_no,
                            product_name: this.select_req.product_name,
                            selling_price_min: selling_price_min,
                            selling_price_max: selling_price_max,
                            start_created_at: start_created_at,
                            end_created_at: end_created_at,
                            start_launched_at_start: start_launched_at_start, //上架 - 起
                            start_launched_at_end: start_launched_at_end, //上架 - 止
                            filter_product_id: filter_product_id, //排除掉 ID
                            limit: limit,
                            product_type: product_type,
                        });
                        this.result_products = response.data.result.data;
                    }
                    req();
                },
                promotionalCampaignsGetAjax(){
                    var promotional_campaigns_time_type = $('#promotional_campaigns_time_type').val();
                    var promotional_campaigns_key_word = $('#promotional_campaigns_key_word').val();
                    var req = async () => {
                        const response = await axios.post('/backend/web_category_products/ajax', {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            type: 'promotionalCampaignsGetAjax',
                            promotional_campaigns_time_type:promotional_campaigns_time_type,
                            promotional_campaigns_key_word:promotional_campaigns_key_word,
                        });
                        this.result_promotional_campaigns = response.data.result.data;
                    }
                    req();
                },
                productsForCategory() {
                    let list = [];
                    let readyDel = [];
                    var findthis = this.result_products.find((todo, index) => {
                        if (todo.check_use == 1) {
                            this.category_products_list.push({
                                web_category_products_id: '',
                                agent_id: todo.agent_id,
                                created_date: todo.created_date,
                                end_launched_at: todo.end_launched_at,
                                gross_margin: todo.gross_margin,
                                product_id: todo.id,
                                item_cost: todo.item_cost,
                                launched_status: todo.launched_status,
                                launched_status_desc: todo.launched_status,
                                product_name: todo.product_name,
                                product_no: todo.product_no,
                                selling_price: todo.selling_price,
                                start_launched_at: todo.start_launched_at,
                                stock_type: todo.stock_type,
                                supplier_id: todo.supplier_id,
                            });
                            readyDel.push(index);
                            todo.del = 1;
                        } else {
                            todo.del = 0;
                        }
                    })

                    let new_array = this.result_products.filter(function(obj) {
                        return obj.del == 0;
                    });
                    this.result_products = new_array;

                },
                check_all(act) {
                    let status = '';
                    if (act == 'allon') {
                        status = 1;
                    } else if (act == 'alloff') {
                        status = 0;
                    }
                    var findthis = this.result_products.find((todo, index) => {
                        todo.check_use = status;
                    })
                },
                submit() {
                    $("#update-form").submit();
                },
                del_category_products_list(index) {
                    var yes = confirm('你確定要刪除嗎？');
                    if (yes) {
                        var del_data = this.category_products_list[index];
                        if (del_data.web_category_products_id !== '') {
                            var req = async () => {
                                const response = await axios.post('/backend/web_category_products/ajax', {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    type: 'DelProductsList',
                                    id: del_data.web_category_products_id,
                                });
                            };
                            req();
                        }
                        this.category_products_list.splice(index, 1);
                    }

                },
                exchange_promotional_campaigns(promotional_campaigns_data){

                    this.category_hierarchy_content.campaign_brief = promotional_campaigns_data.campaign_brief;
                    this.category_hierarchy_content.promotion_campaign_id = promotional_campaigns_data.id;
                    $('#campaign_brief').rules("add", {required: true,});
                },
                del_promotion_campaign_id(){
                    var check_alert = confirm('你確定要刪除賣場嗎?');
                    if (check_alert) {
                        this.category_hierarchy_content.campaign_brief = null;
                        this.category_hierarchy_content.promotion_campaign_id = null;
                    }
                },
            },
            mounted: function() {
                let start_created_at_flatpickr = flatpickr("#start_created_at_flatpickr", {
                    dateFormat: "Y-m-d",
                    maxDate: $("#end_created_at").val(),
                    onChange: function(selectedDates, dateStr, instance) {
                        end_created_at_flatpickr.set('minDate', dateStr);
                    },
                });

                let end_created_at_flatpickr = flatpickr("#end_created_at_flatpickr", {
                    dateFormat: "Y-m-d",
                    minDate: $("#start_created_at").val(),
                    onChange: function(selectedDates, dateStr, instance) {
                        start_created_at_flatpickr.set('maxDate', dateStr);
                    },
                });

                let start_launched_at_start_flatpickr = flatpickr("#start_launched_at_start_flatpickr", {
                    dateFormat: "Y-m-d",
                    maxDate: $("#start_launched_at_end").val(),
                    onChange: function(selectedDates, dateStr, instance) {
                        start_launched_at_end_flatpickr.set('minDate', dateStr);
                    },
                });

                let start_launched_at_end_flatpickr = flatpickr("#start_launched_at_end_flatpickr", {
                    dateFormat: "Y-m-d",
                    minDate: $("#start_launched_at_start").val(),
                    onChange: function(selectedDates, dateStr, instance) {
                        start_launched_at_start_flatpickr.set('maxDate', dateStr);
                    },
                });

                $("#supplier").select2();
                $('#product_type').select2();
                $("#promotional_campaigns_time_type").select2({
                    allowClear: false,
                    theme: "bootstrap",
                });
                // 驗證表單
                $("#update-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        var check_icon_name = @json($check_icon_name) ;
                        if(!check_icon_name && $("input[name='active']:checked").val() == '1'){
                            alert('請先到W006 維護該大分類的圖檔才能將狀態開啟');
                        }else{
                            form.submit();
                        }
                    },
                    rules: {
                        id: {
                            required: true,
                        },
                        active: {
                            required: true,
                        },
                        campaign_brief:{
                            required: true,
                        }
                    },
                    errorClass: "help-block",
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        if (element.parent('.input-group').length) {
                            error.insertAfter(element.parent());
                            return;
                        }

                        if (element.closest(".form-group").length) {
                            element.closest(".form-group").append(error);
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
            computed: {},
            watch: {
                'category_hierarchy_content.content_type': function() {
                    // 監聽切換型態 - M賣場 P商品
                    if (this.category_hierarchy_content.content_type == 'M' && this.category_products_list
                        .length > 0) {
                        alert('請先將商品刪除');
                        this.category_hierarchy_content.content_type = 'P';
                        $("#content_type1").prop("checked", true);
                    }
                    if (this.category_hierarchy_content.content_type == 'P' && this.category_hierarchy_content
                        .promotion_campaign_id !== null) {
                        alert('請先將賣場移除');
                        this.category_hierarchy_content.content_type = 'M';
                        $("#content_type2").prop("checked", true);
                    }
                },
            }
        });

        new products().$mount('#category_hierarchy_content_input');
        $(document).ready(function() {


        });
    </script>
@endsection
