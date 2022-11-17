@extends('backend.layouts.master')

@section('title', '個人資料編修')

@section('css')
    <style>
        #password-title {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: space-between;
        }
    </style>
@endsection

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-pencil"></i> 編輯資料</h1>
            </div>
        </div>
        <!-- /.row -->
        <form id="update-form" method="post" action="{{ route('user_profile.update') }}">
            @method('PUT')
            @csrf
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
                                                <label for="user_account">帳號</label>
                                                <input class="form-control" name="user_account" id="user_account"
                                                    value="{{ $user->user_account }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div id="password-title">
                                                    <label for="pwd">密碼 <span
                                                            class="text-red">(不需變更請留空白)</span></label>
                                                    <span class="text-primary" id="password-tooltip">
                                                        <i class="fa-solid fa-circle-info"></i> 格式說明
                                                    </span>
                                                </div>
                                                <input class="form-control" name="pwd" id="pwd" type="text"
                                                    autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="user_name">姓名 <span class="text-red">*</span></label>
                                                <input class="form-control" name="user_name" id="user_name"
                                                    value="{{ $user->user_name }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="user_email">信箱 <span class="text-red">*</span></label>
                                                <input class="form-control" name="user_email" id="user_email"
                                                    value="{{ $user->user_email }}">
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
            $('#password-tooltip').tooltip({
                title: "需包含英文和數字，且介於8~20個字元，符號可輸入：!@#$%^&*().-=_~",
            });

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
