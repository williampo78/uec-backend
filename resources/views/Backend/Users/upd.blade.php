@extends('Backend.master')
@section('title', '個人資料編修')
@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-pencil"></i> 編輯資料</h1>
            </div>
        </div>
        <!-- /.row -->
        <form role="form" id="new-form" method="post"
              action="{{ route('profile.update' , $data['id']) }}"
              enctype="multipart/form-data">
            {{ method_field('PUT') }}
            {{ csrf_field() }}
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
                                            <div class="form-group" id="div_account">
                                                <label for="account">帳號</label>
                                                <input class="form-control" name="user_account" id="user_account"
                                                       value="{{$data['user_account']}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_password">
                                                <label for="password">更改密碼(不需變更請留空白)</label>
                                                <input class="form-control" name="password" id="password"
                                                       type="password">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_name">
                                                <label for="name">姓名</label>
                                                <input class="form-control" name="user_name" id="user_name"
                                                       value="{{$data['user_name']}}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_email">
                                                <label for="email">信箱</label>
                                                <input class="form-control" name="user_email" id="user_email"
                                                       value="{{$data['user_email']}}">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6 text-left">
                                    <div class="form-group">
                                        <button class="btn btn-success" id="btn-save"><i class="fa fa-check"></i> 完成
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
        $('#btn-save').click(function () {
            $(".error").hide();
            if ($("#password").val() != "" && $("#password").val().length < 8) {
                $("#div_password").find("label[for='password']").append("<span class='error'>密碼字元不得小於8位</span>");
                return false;
            } else if ($("#password").val() != "") {
                var count_error = 0;

                if ($("#password").val().match(/[0-9]/g)) {
                    count_error++;
                }
                if ($("#password").val().match(/[A-Z]/g)) {
                    count_error++;
                }
                if ($("#password").val().match(/[a-z]/g)) {
                    count_error++;
                }

                if (count_error < 3) {
                    $("#div_password").find("label[for='password']").append("<span class='error'>請輸入大小寫英文加數字</span>");
                    return false;
                } else {
                    $('#new-form').submit();
                }
            } else if ($("#user_email").val() == "") {
                $("#div_email").find("label[for='email']").append("<span class='error'>信箱不得為空</span>");
                return false;
            } else {
                $('#new-form').submit();
            }
        });
    </script>
@endsection

