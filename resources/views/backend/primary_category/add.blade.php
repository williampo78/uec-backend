@extends('backend.master')
@section('title', '新增大分類管理')
@section('content')
    @if ($errors->any())
        <div id="error-message" style="display: none;">
            {{ $errors->first('message') }}
        </div>
    @endif

    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-plus"></i> 新增大分類</h1>
            </div>
        </div>

        <!-- /.row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body">
                        <form role="form" id="new-form" method="post" action="{{ route('primary_category.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <!-- 欄位 -->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_number">
                                                <label for="number">編號 <span style="color:red;">*</span></label>
                                                <input class="form-control" type="text" name="number" id="number">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="name">分類名稱 <span style="color:red;">*</span></label>
                                                <input class="form-control" type="text" name="name" id="name">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                @if ($share_role_auth['auth_create'])
                                                    <button class="btn btn-success" type="submit" id="btn-save">
                                                        <i class="fa fa-save"></i> 儲存
                                                    </button>
                                                @endif

                                                <a class="btn btn-danger" href="{{ route('primary_category') }}">
                                                    <i class="fa fa-ban"></i> 取消
                                                </a>
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
    </div>
@endsection
@section('js')
    <script>
        $(function() {
            if ($('#error-message').length) {
                alert($('#error-message').text().trim());
            }

            // 驗證表單
            $("#new-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#btn-save').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    number: {
                        required: true,
                    },
                    name: {
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
        })
    </script>
@endsection
