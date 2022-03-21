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
                        <form id="search-form" class="form-horizontal" method="GET" action="">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="col-sm-3 text-right">
                                        <label class="control-label">帳號</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="user_account" id="user_account"
                                            placeholder="模糊查詢" value="{{ request()->input('user_account') }}">
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="col-sm-3 text-right">
                                        <label class="control-label">名稱</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="user_name" id="user_name" placeholder="模糊查詢"
                                            value="{{ request()->input('user_name') }}">
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="col-sm-3 text-right">
                                        <label class="control-label">狀態</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="form-control select2-default" name="active" id="active">
                                            <option value=''></option>
                                            <option value="1" {{ '1' == request()->input('active') ? 'selected' : '' }}>
                                                啟用
                                            </option>
                                            <option value="0" {{ '0' == request()->input('active') ? 'selected' : '' }}>
                                                關閉
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 text-right">
                                        @if ($share_role_auth['auth_query'])
                                            <button class="btn btn-warning">
                                                <i class="fa-solid fa-magnifying-glass"></i> 查詢
                                            </button>
                                        @endif
                                    </div>
                                </div>
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
                                @foreach ($users as $user)
                                    <tr>
                                        <td>
                                            @if ($share_role_auth['auth_query'])
                                                <button class="btn btn-info btn-sm user_detail"
                                                    data-user-id="{{ $user->id }}" title="檢視">
                                                    <i class="fa-solid fa-magnifying-glass"></i>
                                                </button>
                                            @endif

                                            @if ($share_role_auth['auth_update'])
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('users.edit', $user->id) }}">編輯</a>
                                            @endif
                                        </td>
                                        <td>{{ $user->user_account }}</td>
                                        <td>{{ $user->user_name }}</td>
                                        <td>{{ $user->active == 1 ? '啟用' : '關閉' }}</td>
                                        <td>{{ $user->user_email }}</td>
                                        <td>{{ $user->updated_at }}</td>
                                        <td>{{ $user->updatedByUser->user_name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @include('backend.user.show')
@endsection

@section('js')
    <script>
        $(function() {
            $(document).on('click', '.user_detail', function() {
                let userId = $(this).attr("data-user-id");

                axios.get(`/backend/users/${userId}`)
                    .then(function(response) {
                        let user = response.data;

                        $('#modal-account').empty().text(user.user_account);
                        $('#modal-name').empty().text(user.user_name);
                        $('#modal-active').empty().text(user.user_active);
                        $('#modal-email').empty().text(user.user_email);
                        $('#modal-supplier').empty().text(user.supplier);
                        $('#modal-roles').empty().text(user.roles);

                        $('#user_detail').modal('show');
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
        });
    </script>
@endsection
