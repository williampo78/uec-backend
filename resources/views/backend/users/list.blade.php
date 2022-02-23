@extends('backend.master')

@section('title', '使用者管理')

@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-users"></i> 使用者管理</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">

                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <form id="search-form" method="GET" action="">
                            <div class="row">
                                <div class="col-sm-1 text-right">
                                    <h5>帳號</h5>
                                </div>
                                <div class="col-sm-3"><input class="form-control" name="user_account"
                                        id="user_account" placeholder="模糊查詢"
                                        value="{{ request()->input('user_account') }}"></div>
                                <div class="col-sm-1 text-right">
                                    <h5>名稱</h5>
                                </div>
                                <div class="col-sm-3"><input class="form-control" name="user_name" id="user_name"
                                        placeholder="模糊查詢" value="{{ request()->input('user_name') }}"></div>
                                <div class="col-sm-1 text-right">
                                    <h5>狀態</h5>
                                </div>
                                <div class="col-sm-2">
                                    <select class="form-control js-select2-active" name="active" id="active">
                                        <option value="1" {{ '1' == request()->input('active') ? 'selected' : '' }}>
                                            啟用
                                        </option>
                                        <option value="0" {{ '0' == request()->input('active') ? 'selected' : '' }}>
                                            關閉
                                        </option>
                                    </select>
                                </div>
                                @if ($share_role_auth['auth_query'])
                                    <div class="col-sm-1 text-right">
                                        <button class="btn btn-warning"><i class="fa-solid fa-magnifying-glass"></i></i> 查詢</button>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="row">
                            @if ($share_role_auth['auth_create'])
                                <div class="col-sm-2">
                                    <a class="btn btn-block btn-warning btn-sm" id="btn-new"
                                        href="{{ route('users.create') }}"><i class="fa-solid fa-plus"></i> 新增</a>
                                </div>
                            @endif
                        </div>
                        <hr>
                        <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">功能</th>
                                    <th class="text-nowrap">帳號</th>
                                    <th class="text-nowrap">名稱</th>
                                    <th class="text-nowrap">狀態</th>
                                    <th class="text-nowrap">e-Mail</th>
                                    <th class="text-nowrap">最後異動時間</th>
                                    <th class="text-nowrap">最後異動者</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['role'] as $item)
                                    <tr>
                                        <td>
                                            @if ($share_role_auth['auth_query'])
                                                <button class="btn btn-info btn-sm" data-toggle="modal"
                                                    data-target="#row_detail" data-id="{{ $item['id'] }}"
                                                    onclick="row_detail({{ $item['id'] }});">
                                                    <i class="fa-solid fa-magnifying-glass"></i>
                                                </button>
                                            @endif

                                            @if ($share_role_auth['auth_update'])
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('users.edit', $item->id) }}">編輯</a>
                                            @endif
                                        </td>
                                        <td>{{ $item->user_account }}</td>
                                        <td>{{ $item->user_name }}</td>
                                        <td>{{ $item->active == 1 ? '啟用' : '關閉' }}</td>
                                        <td>{{ $item->user_email }}</td>
                                        <td>{{ $item->updated_at }}</td>
                                        <td>{{ $item->updated_by > 0 ? $data['user'][$item->updated_by]['user_name'] : $data['user'][$item->created_by]['user_name'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @include('backend.users.detail')
@endsection

@section('js')
    <script>
        $('.js-select2-active').select2({
            theme: "bootstrap",
        });
    </script>
@endsection
