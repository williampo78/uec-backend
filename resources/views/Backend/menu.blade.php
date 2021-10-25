<?php
$menus = App\Services\PermissionService::GetUserMenu();
?>
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
                    @foreach($menus['mainMenu'] as $menu)
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">
                            <i class="fa {{$menu['mainIcon']}} fa-fw"></i> {{$menu['mainMenu']}} <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @foreach($menus['subMenu'][$menu['mainID']] as $subMenu)
                            <li>
                                <a href="{{route($subMenu['code'])}}">
                                    <i class="fa {{$subMenu['icon']}} fa-fw"></i>
                                    {{$subMenu['subMenu']}} </a>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                    @endforeach
                </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                       aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars fa-fw"></i> 設定</a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="{{url('/backend/user_profile')}}"><i class="fa fa-user fa-fw"></i> 個人資料</a></li>

                        <li class="divider"></li>
                        <li><a href="{{ route('signOut') }}"><i class="fa fa-sign-out fa-fw"></i> 登出</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</div>
