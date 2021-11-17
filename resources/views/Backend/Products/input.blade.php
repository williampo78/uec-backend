@extends('Backend.master')
@section('title', '分類階層內容管理')
@section('content')
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
                    <div class="panel-body" id="category_hierarchy_content_input">
                        <form role="form" id="new-form" method="POST" action="{{ route('products.store') }}"
                            enctype="multipart/form-data" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="col-sm-3">
                                                <label for="doc_number">庫存類型 <span class="redtext">*</span></label>
                                            </div>
                                            <div class="col-sm-3 form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="active" id="inlineRadio1"
                                                    value="1">
                                                <label class="form-check-label" for="inlineRadio1">買斷 [A] </label>
                                            </div>
                                            <div class="col-sm-3 form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="active" id="inlineRadio2"
                                                    value="0">
                                                <label class="form-check-label" for="inlineRadio2">寄售 [B]</label>
                                            </div>
                                            <div class="col-sm-3 form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="active" id="inlineRadio2"
                                                    value="0">
                                                <label class="form-check-label" for="inlineRadio2">轉單[T]</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="col-sm-3">
                                        <label for="doc_number">商品序號 <span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="meta_title" id="meta_title" value="" readonly>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // var products = Vue.extend({
        //     data: function() {
        //         return {}
        //     },
        //     methods: {},
        //     mounted: function() {},
        //     computed: {},

        // });

        // new products().$mount('#category_hierarchy_content_input');
    </script>
@endsection
