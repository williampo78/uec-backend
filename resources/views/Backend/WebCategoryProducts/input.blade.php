@extends('Backend.master')
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
                        <form role="form" id="new-form" method="POST"
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
                                                <div class="col-sm-12">
                                                    <label for="doc_number">狀態 <span class="redtext">*</span></label>
                                                </div>
                                                <div class="col-sm-4 form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="active"
                                                        id="inlineRadio1" value="1"
                                                        {{ $category_hierarchy_content->active == 1 ? "checked='checked'" : '' }}>
                                                    <label class="form-check-label" for="inlineRadio1">開啟</label>
                                                </div>
                                                <div class="col-sm-4 form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="active"
                                                        id="inlineRadio2" value="0"
                                                        {{ $category_hierarchy_content->active == 0 ? "checked='checked'" : '' }}>
                                                    <label class="form-check-label" for="inlineRadio2">關閉</label>
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
                                                    <div class="col-sm-4 form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="content_type"
                                                            id="content_type1" value="P" checked="checked">
                                                        <label class="form-check-label" for="content_type1">指定商品</label>
                                                    </div>
                                                    {{-- <div class="col-sm-4 form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="content_type"
                                                        id="content_type2" value="0">
                                                    <label class="form-check-label" for="content_type2">指定賣場</label>
                                                </div> --}}
                                                </div>
                                                <div class="col-sm-6">
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                    @include('Backend.WebCategoryProducts.tab_list')
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-success" @click="submit">儲存</button>
                                                <a class="btn btn-danger" type="button"
                                                    href="{{ route('web_category_products') }}">取消</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @include('Backend.WebCategoryProducts.input_detail')
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
                    var create_start_date = $('input[name="create_start_date"]').val();
                    var create_end_date = $('input[name="create_end_date"]').val();
                    var select_start_date = $('input[name="select_start_date"]').val();
                    var select_end_date = $('input[name="select_end_date"]').val();
                    var filter_product_id = [];
                    this.category_products_list.find((todo, index) => {
                        filter_product_id.push(todo.product_id);
                    })
                    // console.log(filter_product_id)  ; 
                    var req = async () => {
                        const response = await axios.post('/backend/web_category_products/ajax', {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            type: 'getProductsList',
                            product_no: this.select_req.product_no,
                            product_name: this.select_req.product_name,
                            selling_price_min: this.select_req.selling_price_min,
                            selling_price_max: this.select_req.selling_price_max,
                            create_start_date: create_start_date,
                            create_end_date: create_end_date,
                            select_start_date: select_start_date,
                            select_end_date: select_end_date,
                            filter_product_id: filter_product_id, //排除掉 ID 
                        });
                        console.log(response);
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
                                launched_status_desc: todo.launched_status_desc,
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
                    $("#new-form").submit();
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
                    var create_start_date = $('input[name="create_start_date"]').val();
                    var create_end_date = $('input[name="create_end_date"]').val();
                    var select_start_date = $('input[name="select_start_date"]').val();
                    var select_end_date = $('input[name="select_end_date"]').val();
                }
            },
            mounted: function() {

                $('#create_start_date').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
                $('#create_end_date').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
                $('#select_start_date').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
                $('#select_end_date').datetimepicker({
                    format: 'YYYY-MM-DD',
                });

                $("#supplier").select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: "請選擇"
                });
                // $('#products_model_list').DataTable({
                //     "lengthChange": false
                // });
            },
            computed: {},

        });

        new products().$mount('#category_hierarchy_content_input');
        $(document).ready(function() {


        });
    </script>
@endsection