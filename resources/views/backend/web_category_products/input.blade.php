@extends('backend.master')
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

    </style>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i>分類階層內容編輯</h1>
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
                                                <label for="doc_number">分類 <span class="redtext">*</span></label>
                                                <input class="form-control" name="id" id="id"
                                                    value="{{ $category_hierarchy_content->name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_doc_number">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label for="doc_number">狀態 <span
                                                                class="redtext">*</span></label>
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
                                    <textarea style="display:none" name="category_products_list_json" cols="30"
                                        rows="10">@{{ category_products_list }}</textarea>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_doc_number">
                                                    <div class="col-sm-4">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="content_type" id="content_type1"
                                                                value="P" checked="checked">指定商品
                                                        </label>
                                                    </div>
                                                    {{-- <div class="col-sm-4">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="content_type"
                                                            id="content_type2" value="0">指定賣場
                                                        </label>
                                                    </div> --}}
                                                </div>
                                                <div class="col-sm-6">
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                    @include('backend.web_category_products.tab_list')
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-success" @click="submit"
                                                    id="btn-save">儲存</button>
                                                <a class="btn btn-danger" type="button"
                                                    href="{{ route('web_category_products') }}">取消</a>
                                            </div>
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
                        // supplier_id :$('#supplier').val() ,
                        product_no: '',
                        product_name: '',
                        selling_price_min: '',
                        selling_price_max: '',
                    },
                    result_products: [],
                }
            },
            methods: {
                productsGetAjax() {
                    var start_created_at = $('input[name="start_created_at"]').val();
                    var end_created_at = $('input[name="end_created_at"]').val();
                    var start_launched_at_start = $('input[name="start_launched_at_start"]').val();
                    var start_launched_at_end = $('input[name="start_launched_at_end"]').val();
                    var supplier_id = $('#supplier').val() ;
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
                            supplier_id:supplier_id,
                            product_no: this.select_req.product_no,
                            product_name: this.select_req.product_name,
                            selling_price_min: this.select_req.selling_price_min,
                            selling_price_max: this.select_req.selling_price_max,
                            start_created_at: start_created_at,
                            end_created_at: end_created_at,
                            start_launched_at_start: start_launched_at_start, //上架 - 起
                            start_launched_at_end: start_launched_at_end, //上架 - 止
                            filter_product_id: filter_product_id, //排除掉 ID
                            limit:limit,
                            product_type:product_type,
                        });
                        this.result_products = response.data.result.data;
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
                    var status = '';
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
                TESTFUNCTION() {
                    var start_created_at = $('input[name="start_created_at"]').val();
                    var end_created_at = $('input[name="end_created_at"]').val();
                    var start_launched_at_start = $('input[name="start_launched_at_start"]').val();
                    var start_launched_at_end = $('input[name="start_launched_at_end"]').val();
                }
            },
            mounted: function() {

                $('#start_created_at').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
                $('#end_created_at').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
                $('#start_launched_at_start').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
                $('#start_launched_at_end').datetimepicker({
                    format: 'YYYY-MM-DD',
                });

                $("#supplier").select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: "請選擇"
                });
                $('#product_type').select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: "請選擇"
                });
                // $('#products_model_list').DataTable({
                //     "lengthChange": false
                // });

                // 驗證表單
                $("#update-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        $('#btn-save').prop('disabled', true);
                        form.submit();
                    },
                    rules: {
                        id: {
                            required: true,
                        },
                        active: {
                            required: true,
                        },
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

        });

        new products().$mount('#category_hierarchy_content_input');
        $(document).ready(function() {


        });
    </script>
@endsection
