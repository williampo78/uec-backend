<!DOCTYPE html>
<html lang="zh-tw">

<head>
    <title>@yield('title')</title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('asset/img/uarklogo.ico') }}" />
    <link rel="stylesheet" href="{{ asset('asset/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('asset/css/bootstrap-treefy.css') }}" />
    <link rel="stylesheet" href="{{ asset('asset/js/template/dist/css/sb-admin-2.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('asset/js/template/bower_components/metisMenu/dist/metisMenu.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('asset/js/template/dist/css/timeline.css') }}" />
    <link rel="stylesheet" href="{{ asset('asset/js/template/bower_components/morrisjs/morris.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('asset/js/template/bower_components/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('asset/js/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('asset/css/fa/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/init.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('asset/css/select2-bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/js/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css"
        crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('asset/css/jquery.fancybox.min.css') }}">
    <link rel="stylesheet" src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css">
    <link rel="stylesheet" href="{{ asset('asset/css/validationEngine.jquery.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/editor.css') }}">

    <style>
        [v-cloak] {
            display: none;
        }

        .select2-results__options {
            max-height: 500px !important;
        }

    </style>

    @yield('style')
</head>

<body>
    @auth

        @include('Backend.menu')

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

    <script src="{{ asset('asset/js/template/bower_components/jquery/dist/jquery.js') }}"></script>
    <script src="{{ asset('asset/js/DataTables/datatables.js') }}"></script>
    <script src="{{ asset('asset/js/DataTables/Buttons-1.5.6/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('asset/js/template/bower_components/bootstrap/dist/js/bootstrap.js') }}"></script>
    <script src="{{ asset('asset/js/template/bower_components/bootstrap/dist/js/bootstrap-filestyle.min.js') }}">
    </script>
    <script src="{{ asset('asset/js/template/bower_components/bootstrap/js/collapse.js') }}"></script>
    <script src="{{ asset('asset/js/template/bower_components/bootstrap/js/transition.js') }}"></script>
    <script src="{{ asset('asset/js/template/dist/js/sb-admin-2.js') }}"></script>
    <script src="{{ asset('asset/js/template/bower_components/moment-develop/moment.js') }}"></script>
    <script src="{{ asset('asset/js/template/bower_components/moment-develop/locale/zh-tw.js') }}"></script>
    <script src="{{ asset('asset/js/select2.min.js') }}"></script>
    <script src="{{ asset('asset/js/build/ckeditor.js') }}"></script>
    <script src="{{ asset('asset/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('asset/js/additional-methods.min.js') }}"></script>
    <script src="{{ asset('asset/js/messages_zh_TW.min.js') }}"></script>
    <script src="{{ asset('asset/js/bootstrap-treefy.js') }}"></script>
    <script src="{{ asset('asset/js/clipboard.min.js') }}"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.debug.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="{{ asset('asset/js/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('asset/js/template/bower_components/morrisjs/morris.min.js') }}"></script>
    <script src="{{ asset('asset/js/template/bower_components/metisMenu/dist/metisMenu.min.js') }}"></script>
    <script src="{{ asset('asset/js/init.js') }}"></script>
    <script src="{{ asset('asset/js/template/bower_components/raphael/raphael-min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
    <script src="{{ asset('asset/js/jquery.validationEngine.js') }}"></script>
    <script src="{{ asset('asset/js/jquery.validationEngine-zh_TW.js') }}"></script>
    <script src="{{ asset('asset/js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('asset/js/axios.min.js') }}"></script>
    <script src="{{ asset('asset/js/bootstrap-colorpicker.min.js') }}"></script>

    <script>
        @isset($share_role_auth)
            var RoleAuthJson = @json($share_role_auth) ;
        @endisset
        var UecConfig = @json(Config('uec'));
        $(document).ready(function() {
            $('#table_list').DataTable();

        });
    </script>

    @yield('js')
</body>

</html>
