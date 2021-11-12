@extends('Backend.master')
@section('title', '分類階層內容管理')
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
                            {{ method_field('PUT') }}
                            {{ csrf_field() }}
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
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_doc_number">
                                            <label for="doc_number">分類 <span class="redtext">*</span></label>
                                            <input class="form-control" name="number" id="number" value="" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_doc_number">
                                            <div class="col-sm-12">
                                                <label for="doc_number">狀態 <span class="redtext">*</span></label>
                                            </div>
                                            <div class="col-sm-4 form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                    id="inlineRadio1" value="1">
                                                <label class="form-check-label" for="inlineRadio1">開啟</label>
                                            </div>
                                            <div class="col-sm-4 form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                    id="inlineRadio2" value="0">
                                                <label class="form-check-label" for="inlineRadio2">關閉</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_doc_number">
                                            <label for="doc_number">網頁標題</label>
                                            <input class="form-control" name="number" id="number">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_doc_number">
                                            <label for="doc_number">網頁描述</label>
                                            <input class="form-control" name="number" id="number" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_doc_number">
                                            <label for="doc_number">網頁關鍵字</label>
                                            <input class="form-control" name="number" id="number">
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
                                                        id="content_type1" value="1">
                                                    <label class="form-check-label" for="content_type1">指定商品</label>
                                                </div>
                                                <div class="col-sm-4 form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="content_type"
                                                        id="content_type2" value="0">
                                                    <label class="form-check-label" for="content_type2">指定賣場</label>
                                                </div>
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
                                            <button class="btn btn-danger" type="button" >取消</button>
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
    </script>
@endsection
