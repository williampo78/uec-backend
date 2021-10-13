<!DOCTYPE html>
<html lang="zh-tw">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('asset/css/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('asset/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('asset/css/bootstrap-treefy.css') }}" />
    <link rel="stylesheet" href="{{ asset('asset/js/template/dist/css/sb-admin-2.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('asset/js/template/bower_components/metisMenu/dist/metisMenu.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('asset/js/template/dist/css/timeline.css') }}" />
    <link rel="stylesheet" href="{{ asset('asset/js/template/bower_components/morrisjs/morris.css') }}" />
    {{-- <link rel="stylesheet"
        href="{{ asset('asset/js/template/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css') }}" /> --}}
    {{-- <link rel="stylesheet"
        href="{{ asset('asset/js/template/bower_components/datatables-responsive/css/dataTables.responsive.css') }}" /> --}}
    <link rel="stylesheet"
        href="{{ asset('asset/js/template/bower_components/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.css') }}" />
    {{-- <link rel="stylesheet" href="{{ asset('asset/js/DataTables/datatables.min.css') }}" /> --}}
    <link rel="stylesheet"
        href="{{ asset('asset/js/template/bower_components/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('asset/js/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('asset/css/fa/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/init.css') }}">
</head>

<body>
    @auth
        <div id="wrapper">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="/backend">健康力公司</a>
                    </div>
                    <ul class="nav navbar-nav">
                        <li><a href="/backend"><i class="fa fa-home fa-fw"></i> 首頁</a></li>
                    </ul>
                    <ul class="nav navbar-nav">
                        <!-- <li><a href="index.php"><i class="fa fa-home fa-fw"></i> 首頁</a></li> -->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="fa fa-cog fa-fw"></i> 基本資料 <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('warehouse') }}">
                                        <i class="fa fa-database fa-fw"></i>
                                        倉庫維護 </a>
                                </li>
                                <li>
                                    <a href="{{route('supplier')}}">
                                        <i class="fa fa-truck fa-fw"></i>
                                        供應商資料 </a>
                                </li>
                                <li>
                                    <a href="index.php?func=client">
                                        <i class="fa fa-user fa-fw"></i>
                                        客戶資料 </a>
                                </li>
                                <li>
                                    <a href="{{ route('supplier_type') }}">
                                        <i class="fa fa-cubes fa-fw"></i>
                                        供應商類別管理 </a>
                                </li>
                                <li>
                                    <a href="{{ route('primary_category') }}">
                                        <i class="fa fa-cubes fa-fw"></i>
                                        主分類管理 </a>
                                </li>
                                <li>
                                    <a href="{{ route('category') }}">
                                        <i class="fa fa-cubes fa-fw"></i>
                                        子分類管理 </a>
                                </li>
                                <li>
                                    <a href="{{route('item')}}">
                                        <i class="fa fa-cube fa-fw"></i>
                                        物品管理 </a>
                                </li>
                                {{-- <li>
                                    <a href="index.php?func=bank">
                                        <i class="fa fa-bank fa-fw"></i>
                                        銀行管理 </a>
                                </li> --}}
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="fa fa-truck fa-fw"></i> 進銷存 <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu in">
                                <li>
                                    <a href="{{route('quotation')}}">
                                        <i class="fa fa-shopping-cart fa-fw"></i>
                                        報價單 </a>
                                </li>
                                <li>
                                    <a href="{{route('requisitions_purchase')}}">
                                        <i class="fa fa-shopping-cart fa-fw"></i>
                                        請購單 </a>
                                </li>
                                <li>
                                    <a href="index.php?func=order_supplier">
                                        <i class="fa fa-cart-arrow-down fa-fw"></i>
                                        採購單 </a>
                                </li>
                                <li>
                                    <a href="index.php?func=purchase_acceptance" class="active">
                                        <i class="fa fa-sign-in fa-fw"></i>
                                        驗收單 </a>
                                </li>
                                <li>
                                    <a href="index.php?func=purchase" class="active">
                                        <i class="fa fa-sign-in fa-fw"></i>
                                        進貨單 </a>
                                </li>
                                <li>
                                    <a href="index.php?func=return_purchase">
                                        <i class="fa fa-reply fa-fw"></i>
                                        進貨退出 </a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="fa fa-book fa-fw"></i> 管理功能 <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="index.php?func=agent_permission">
                                        <i class="fa fa-bank fa-fw"></i>
                                        公司管理 </a>
                                </li>
                                <li>
                                    <a href="index.php?func=department">
                                        <i class="fa fa-users fa-fw"></i>
                                        部門管理 </a>
                                </li>
                                <li>
                                    <a href="index.php?func=employee_type">
                                        <i class="fa fa-list fa-fw"></i>
                                        員工類別管理 </a>
                                </li>
                                <li>
                                    <a href="index.php?func=employee">
                                        <i class="fa fa-user fa-fw"></i>
                                        員工管理 </a>
                                </li>
                                <li>
                                    <a href="index.php?func=user_permission">
                                        <i class="fa fa-book fa-fw"></i>
                                        員工權限 </a>
                                </li>
                                <li>
                                    <a href="index.php?func=group_permission">
                                        <i class="fa fa-book fa-fw"></i>
                                        權限套組管理 </a>
                                </li>
                                <li>
                                    <a href="index.php?func=announcement">
                                        <i class="fa fa-bullhorn fa-fw"></i>
                                        公告管理 </a>
                                </li>
                            </ul>
                        </li>

                        <ul class="nav navbar-nav navbar-right">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                    aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars fa-fw"></i> 設定</a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="index.php?func=user"><i class="fa fa-user fa-fw"></i> 個人資料</a></li>

                                    <li class="divider"></li>
                                    <li><a href="{{ route('signOut') }}"><i class="fa fa-sign-out fa-fw"></i> 登出</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </ul>


            </nav>

        </div>

    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
    @endauth
    @guest
    @endguest
    @yield('content')
</body>
<script src="{{ asset('asset/js/template/bower_components/jquery/dist/jquery.js') }}"></script>
<script src="{{ asset('asset/js/template/bower_components/bootstrap/dist/js/bootstrap.js') }}"></script>
<script src="{{ asset('asset/js/template/bower_components/bootstrap/dist/js/bootstrap-filestyle.min.js') }}">
</script>
<script src="{{ asset('asset/js/template/bower_components/bootstrap/js/collapse.js') }}"></script>
<script src="{{ asset('asset/js/template/bower_components/bootstrap/js/transition.js') }}"></script>
<script src="{{ asset('asset/js/template/dist/js/sb-admin-2.js') }}"></script>
<script src="{{ asset('asset/js/DataTables/datatables.js') }}"></script>
<script src="{{ asset('asset/js/DataTables/Buttons-1.5.6/js/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('asset/js/template/bower_components/moment-develop/moment.js') }}"> </script>
<script src="{{ asset('asset/js/template/bower_components/moment-develop/locale/zh-tw.js') }}"></script>
<script src="{{ asset('asset/js/select2.min.js') }}"></script>
<script src="{{ asset('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.full.js') }}"> </script>

<script src="{{ asset('asset/js/ckeditor/ckeditor.js') }}"> </script>
<script src="{{ asset('asset/js/ckfinder/ckfinder.js') }}"> </script>
<script src="{{ asset('asset/js/jquery.validate.js') }}"> </script>
<script src="{{ asset('asset/js/bootstrap-treefy.js') }}"> </script>
<script src="{{ asset('asset/js/clipboard.min.js') }}"> </script>
<script src="https://code.highcharts.com/highcharts.js"> </script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.debug.js"></script>
<link href="{{ asset('asset/css/jquery.fancybox.min.css') }}" rel="stylesheet">
<link src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="{{ asset('asset/js/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"> </script>
<script src="{{ asset('asset/js/template/bower_components/raphael/raphael-min.js') }}"> </script>
<script src="{{ asset('asset/js/template/bower_components/morrisjs/morris.min.js') }}"> </script>
<script src="{{ asset('asset/js/template/bower_components/metisMenu/dist/metisMenu.min.js') }}"> </script>
{{-- <script src="{{ asset('asset/css/init.css') }}"> </script> --}}
<script src="{{ asset('asset/js/init.js') }}"> </script>
{{-- <script src="https://maps.googleapis.com/maps/api/js"> </script> --}}
<script src="{{ asset('asset/js/template/bower_components/raphael/raphael-min.js') }}"> </script>
<script src="{{ asset('asset/js/template/bower_components/raphael/raphael-min.js') }}"> </script>
<script>
    $(document).ready(function() {
        $('#table_list').DataTable();
    });
</script>
@yield('js')

</html>
