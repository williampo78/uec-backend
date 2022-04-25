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
                @if (session()->has('dradvice_menu'))
                    @foreach (session('dradvice_menu') as $menuItem)
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="fa {{ $menuItem['icon'] }} fa-fw"></i> {{ $menuItem['name'] }} <span
                                    class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                @foreach ($menuItem['sub_menu'] as $subMenuItem)
                                    <li>
                                        @if (Route::has($subMenuItem['code']))
                                            <a href="{{ route($subMenuItem['code']) }}">
                                                <i class="fa {{ $subMenuItem['icon'] }} fa-fw"></i>
                                                {{ $subMenuItem['name'] }}
                                            </a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                @endif
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="fa-solid fa-bars fa-fw"></i> 設定
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li>
                            <a href="{{ url('/backend/user_profile') }}">
                                <i class="fa-solid fa-user fa-fw"></i> 個人資料
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('logout') }}">
                                <i class="fa-solid fa-arrow-right-from-bracket fa-fw"></i> 登出
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</div>
