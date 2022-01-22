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
        <form role="form" id="new-form" method="post" action="{{ route('qa.store') }}" enctype="multipart/form-data">
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
                                                <label for="parent_code">類別 <span class="text-danger">*</span></label>
                                                <select name="parent_code" id="parent_code" class="js-select2">
                                                    <option value=""></option>
                                                    @foreach ($data['category'] as $cate)
                                                        <option value="{{ $cate['code'] }}">{{ $cate['description'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_sort">
                                                <label for="sort">排序 <span class="text-danger">*</span></label>
                                                <input class="form-control" type="number" name="sort" id="sort">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>狀態 <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="active" id="active1" checked
                                                                value="1">啟用
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="active" id="active0" value="0">關閉
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group" id="div_email">
                                                <label for="content_name">問題描述 <span class="text-danger">*</span></label>
                                                <input class="form-control" name="content_name" id="content_name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group" id="div_email">
                                                <label for="editor">問題解答 <span class="text-danger">*</span></label>
                                                <textarea class="form-control" rows="5" id="editor"
                                                    name="content_text"></textarea>
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
        ClassicEditor.create(document.querySelector('#editor'), {
            ckfinder: {
                // Upload the images to the server using the CKFinder QuickUpload command.
                uploadUrl: "/ckfinder/connector?command=QuickUpload&type=Images&responseType=json&_token=" +
                    document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                //uploadUrl:"/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            },
        })

        $(function() {
            $('.js-select2').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '請選擇',
            });

            $("#btn-save").on('click', function() {
                $("#new-form").submit();
            });

            $("#btn-cancel").on('click', function() {
                window.location.href = '{{ route('qa') }}';
            });

            //文字編輯器
            /*
            var editor = CKEDITOR.replace('content_text', {
                filebrowserBrowseUrl: 'ckfinder/ckfinder.html',
                filebrowserImageBrowseUrl: 'ckfinder/ckfinder.html?Type=Images',
                //filebrowserUploadUrl : 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files', //可上傳一般檔案
                filebrowserImageUploadUrl: 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images' //可上傳圖檔
            });
            */

            // 驗證表單
            $("#new-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#btn-save').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    parent_code: {
                        required: true,
                    },
                    sort: {
                        required: true,
                        digits: true,
                    },
                    active: {
                        required: true,
                    },
                    content_name: {
                        required: true,
                    },
                    content_text: {
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
        });
    </script>
@endsection
