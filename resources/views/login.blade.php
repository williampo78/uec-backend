@extends('backend.master')

@section('title', '綠杏健康力EC電商後台管理系統')

@section('css')
    <link rel="stylesheet" href="{{ asset('asset/css/login.css') }}">
@endsection

@section('content')
    <div id="app">
        <div class="limiter">
            <div class="container-login100">
                <div class="wrap-login100">
                    <div class="row">
                        <div class="col-xs-12 text-center" style="padding:40px;">
                            <img src="{{ asset('asset/img/logo_1.png') }}" width="120"><sup>&reg;</sup>
                        </div>
                    </div>
                    <div class="login100-form-title" style="background-image: url('/asset/img/bg-01.jpg'); color:#ffffff;">
                        <span class="login100-form-title-1">
                            綠杏健康力EC電商後台管理系統
                        </span>
                    </div>
                    <form class="login100-form validate-form" id="login-form" method="post" action="{{ route('login') }}">
                        @csrf
                        <div class="wrap-input100 validate-input" data-validate="必須填寫">
                            <span class="label-input100"><i class="fa-solid fa-user fa-fw fa-lg"></i></span>
                            <input class="input100" type="text" name="account" placeholder="使用者帳號" value="{{ old('account') }}">
                            <span class="focus-input100"></span>
                        </div>

                        <div class="wrap-input100 validate-input" data-validate="必須填寫">
                            <span class="label-input100"><i class="fa-solid fa-key fa-fw fa-lg"></i></span>
                            <input class="input100" type="password" name="pwd" placeholder="密碼"
                                autocomplete="off">
                            <span class="focus-input100"></span>
                        </div>

                        @if ($errors->any())
                            <p>&nbsp;</p>
                            <div class="alert alert-danger alert-dismissible" style="width: 100%; margin: 0px;">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <ul>
                                @foreach ($errors->all() as $error)
                                    <li>
                                        <span style="color:red;">{{ $error }}</span>
                                    </li>
                                @endforeach
                                </ul>
                            </div>
                        @endif

                        <p>&nbsp;</p>
                        <div class="container-login100-form-btn">
                            <button type="button" class="btn btn-lg btn-primary btn-block" :disabled="saveButton.isDisabled" @click="submitForm">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i> 登入
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        let vm = new Vue({
            el: "#app",
            data: {
                saveButton: {
                    isDisabled: false,
                },
            },
            mounted() {
                let self = this;

                // 驗證表單
                $("#login-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        self.saveButton.isDisabled = true;
                        form.submit();
                    },
                    rules: {
                        account: {
                            required: true,
                        },
                        pwd: {
                            required: true,
                        },
                    },
                    errorClass: "help-block",
                    errorElement: "span",
                    errorPlacement: function(error, element) {},
                    highlight: function(element, errorClass, validClass) {
                        $(element).closest(".validate-input").addClass("alert-validate");
                    },
                    success: function(label, element) {
                        $(element).closest(".validate-input").removeClass("alert-validate");
                    },
                });
            },
            methods: {
                submitForm() {
                    $("#login-form").submit();
                },
            }
        });
    </script>
@endsection
