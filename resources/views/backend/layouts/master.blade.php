<!DOCTYPE html>
<html lang="zh-tw">

<head>
    <title>{{ env('TITLE_PREFIX', '') }}@yield('title')</title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('asset/img/favicon.ico') }}" />
    <link rel="stylesheet" href="{{ asset('asset/js/template/dist/css/sb-admin-2.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('asset/js/template/bower_components/metisMenu/dist/metisMenu.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('asset/css/fa/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/editor.css') }}">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    @yield('css')
</head>

<body>
    @auth
        @include('backend.partials.menu')

        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
    @endauth

    @guest
    @endguest

    <div class="container-fluid">
        @yield('content')
    </div>

    <script src="{{ mix('js/app.js') }}"></script>
    <script src="{{ asset('asset/js/template/dist/js/sb-admin-2.js') }}"></script>
    <script src="{{ asset('asset/js/build/ckeditor.js?v=2023010601') }}"></script>
    <script src="{{ asset('asset/js/template/bower_components/metisMenu/dist/metisMenu.min.js') }}"></script>
    <script src="{{ asset('asset/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('asset/js/sweetalert.min.js') }}"></script>

    <script>
        @isset($share_role_auth)
            var RoleAuthJson = @json($share_role_auth);
        @endisset

        var UecConfig = @json(Config('uec'));

        $(function() {
            //檢查使用平台的權限機制　如果沒有使用權限，則回另一個平台
            var inDradviceUse = @json(session('inDradviceUse'));
            var swithBackendUrl = @json(config('uec.swithBackendUrl')); //另一個平台的 url
            function checkingBackendUse() {
                alert('沒有權限，請聯絡管理人員');
                return location.href = swithBackendUrl;
            }
            if (inDradviceUse == 0) {
                checkingBackendUse();
            }

            // datatable 共用
            $('#table_list').DataTable({
                "order": [],
            });
        });
    </script>

    @yield('js')

    @if (auth()->check())
        <script>
            setTimeout(function() {
                !alert('即將登出');
                window.location.reload();
            }, {{ config('session.lifetime') }} * 1000 * 60);
        </script>
    @endif

</body>

</html>
