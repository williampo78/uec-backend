@extends('Backend.master')

@section('title', '常見問題Q&A')

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-pencil"></i> 新增資料</h1>
            </div>
        </div>
        <!-- /.row -->
        <form role="form" id="new-form" method="post" action="{{route('qa.store')}}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">請輸入下列欄位資料</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_account">
                                                <label for="account">類別 <span class="text-danger">*</span></label>
                                                <select name="parent_code" id="parent_code" class="validate[required]">
                                                    <option value="">請選擇</option>
                                                    @foreach($data['category'] as $cate)
                                                        <option value="{{$cate['code']}}">{{$cate['description']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_sort">
                                                <label for="password">排序 <span class="text-danger">*</span></label>
                                                <input class="form-control validate[required,custom[integer]]" type="number" name="sort"
                                                       id="sort">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_name">
                                                <label for="name">狀態 <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <input type="radio"
                                                               name="active" id="active1" checked
                                                               value="1">
                                                        <label for="active1">啟用</label>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <input type="radio"
                                                               name="active" id="active0"
                                                               value="0">
                                                        <label for="active0">關閉</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group" id="div_email">
                                                <label for="content_name">問題描述 <span class="text-danger">*</span></label>
                                                <input class="form-control validate[required]" name="content_name"
                                                       id="content_name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group" id="div_email">
                                                <label for="content_name">問題解答 <span class="text-danger">*</span></label>
                                                <textarea class="form-control validate[required]" rows="5" name="content_text"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 text-center">
                                    <div class="form-group">
                                        <button class="btn btn-success" id="btn-save" type="button"><i
                                                class="fa fa-check"></i> 完成
                                        </button>
                                        <button type="button" class="btn btn-danger" id="btn-cancel"><i
                                                class="fa fa-ban"></i> 取消
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        $(function () {
            $("#new-form").validationEngine();
            $("select").select2();
            $("#btn-save").click(function () {
                $("#new-form").submit();
            });
            $("#btn-cancel").click(function () {
                window.location.href = '{{route("qa")}}';
            });

            //文字編輯器
            var editor = CKEDITOR.replace('content_text', {
                filebrowserBrowseUrl: 'ckfinder/ckfinder.html',
                filebrowserImageBrowseUrl: 'ckfinder/ckfinder.html?Type=Images',
                //filebrowserUploadUrl : 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files', //可上傳一般檔案
                filebrowserImageUploadUrl: 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images' //可上傳圖檔
            });
        })
    </script>
@endsection
