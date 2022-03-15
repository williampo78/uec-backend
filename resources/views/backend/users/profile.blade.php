@extends('backend.master')
@section('title', '個人資料編修')
@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-pencil"></i> 編輯資料</h1>
            </div>
        </div>
        <!-- /.row -->
        <form role="form" id="update-form" method="post" action="{{ url('/backend/user_profile') }}"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="profile" value="1">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">請輸入下列欄位資料</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="account">帳號</label>
                                                <input class="form-control" name="user_account" id="user_account"
                                                    value="{{ $data['user_account'] }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="pwd">更改密碼(不需變更請留空白)</label>
                                                <input class="form-control" name="pwd" id="pwd" type="password"
                                                    autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="name">姓名 <span style="color:red;">*</span></label>
                                                <input class="form-control" name="user_name" id="user_name"
                                                    value="{{ $data['user_name'] }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="email">信箱 <span style="color:red;">*</span></label>
                                                <input class="form-control" name="user_email" id="user_email"
                                                    value="{{ $data['user_email'] }}">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-success" id="btn-save">
                                            <i class="fa-solid fa-floppy-disk"></i> 儲存
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
        $(function() {
            $('#btn-save').on('click', function() {
                $('#update-form').submit();
            });

            // 驗證表單
            $("#update-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#btn-save').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    user_account: {
                        required: true,
                    },
                    pwd: {
                        drowssapCheck: {
                            depends: function(element) {
                                return $('#pwd').val().length > 0;
                            },
                        },
                    },
                    user_name: {
                        required: true,
                    },
                    user_email: {
                        required: true,
                    },
                },
                messages: {
                    user_email: {
                        required: "信箱不得為空",
                    },
                    pwd: {
                        drowssapCheck: "請輸入大小寫英文加數字，且密碼字元不得小於8位",
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
