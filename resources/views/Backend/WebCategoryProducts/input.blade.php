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
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">請輸入下列欄位資料</div>
                <div class="panel-body" id="category_hierarchy_content_input">
                    <form role="form" id="new-form" method="POST"
                        action="{{ route('web_category_products.update', $category_hierarchy_content->id) }}"
                        enctype="multipart/form-data" novalidate="novalidate">
                        {{ method_field('PUT') }}
                        {{ csrf_field() }}
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
                                                <input class="form-check-input" type="radio" name="active" id="inlineRadio1"
                                                    value="1"
                                                    {{ $category_hierarchy_content->active == 1 ? "checked='checked'" : '' }}>
                                                <label class="form-check-label" for="inlineRadio1">開啟</label>
                                            </div>
                                            <div class="col-sm-4 form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="active" id="inlineRadio2"
                                                    value="0"
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
                                            <button class="btn btn-success" type="button">儲存</button>
                                            <button class="btn btn-danger" type="button">取消</button>
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
    <script>
        var products = Vue.extend({
            data: function() {
                return {
                    category_products_list: @json($category_products_list)
                }
            },
            methods: {},
            mounted: function() {

                $('#datetimepicker').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
                $('#datetimepicker2').datetimepicker({
                    format: 'YYYY-MM-DD',
                });
                // console.log('TEST') ; 
                // $('#products_model_list').DataTable({
                //     "lengthChange": false
                // });


            },
            computed: {},

        });

        new products().$mount('#category_hierarchy_content_input');
        $(document).ready(function() {
            $('#products_model_list').DataTable({
                "lengthChange": false
            });
            $("#supplier").select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: "請選擇"
            });
        });
    </script>
@endsection
