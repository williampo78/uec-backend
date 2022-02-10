@extends('backend.master')

<link rel="stylesheet" href="{{ URL::asset('asset/css/login.css') }}">
@section('title', '諾亞克 uEC - 登入系統')

@section('content')
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="row">
                    <div class="col-xs-12 text-center" style="padding:40px;">
                        <img src="{{ URL::asset('asset/img/logo_1.png') }}" width="120"><sup>&reg;</sup>
                    </div>
                </div>
                <div class="login100-form-title" style="background-image: url('/asset/img/bg-01.jpg'); color:#ffffff;">
                    <span class="login100-form-title-1">
                        諾亞克 uEC 電商後台管理系統
                    </span>
                </div>
                <form class="login100-form validate-form" method="POST" action="{{ route('login.custom') }}">
                    @csrf
                    <div class="wrap-input100 validate-input m-b-26" data-validate="使用者帳號未填寫">
                        <span class="label-input100"><i class="fa fa-user fa-fw fa-lg"></i></span>
                        <input class="input100" type="text" name="account" id="account" placeholder="使用者帳號">
                        <span class="focus-input100"></span>
                    </div>

                    <div class="wrap-input100 validate-input m-b-18" data-validate="Password is required">
                        <span class="label-input100"><i class="fa fa-key fa-fw fa-lg"></i></span>
                        <input class="input100" type="password" name="password" id="password" placeholder="密碼">
                        <span class="focus-input100"></span>
                    </div>
                    <div>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li><span style="color:red;">{{ $error }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                    <p>&nbsp;</p>
                    <div class="container-login100-form-btn">
                        <button type="submit" class="btn btn-lg btn-primary btn-block" id="loginBtn"><i
                                class="fa fa-sign-in"></i> 登入</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection
