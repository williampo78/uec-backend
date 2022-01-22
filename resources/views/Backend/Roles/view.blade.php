@extends('Backend.master')

@section('title', '角色管理')

@section('content')
    <!--新增-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-plus"></i> 檢視角色</h1>
            </div>
        </div>

        <!-- /.row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body">
                        <form id="new-form" method="post" enctype="multipart/form-data">
                            <div class="row">

                                <!-- 欄位 -->
                                <div class="col-sm-12">

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="name">名稱</label>
                                                <input class="form-control validate[required]" name="role_name" disabled
                                                    id="role_name" value="{{ $data['role']->role_name }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="name">狀態</label>
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <input type="radio" name="active" id="active1" disabled
                                                            {{ $data['role']->active == 1 ? 'checked' : '' }} value="1">
                                                        <label for="active1">啟用</label>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <input type="radio" name="active" id="active0" disabled
                                                            {{ $data['role']->active == 0 ? 'checked' : '' }} value="0">
                                                        <label for="active0">關閉</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="name">供應商專用</label>
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <input type="radio" disabled name="is_for_supplier"
                                                            id="is_for_supplier1"
                                                            {{ $data['role']->is_for_supplier == 1 ? 'checked' : '' }}
                                                            value="1">
                                                        <label for="is_for_supplier1">是</label>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <input type="radio" disabled name="is_for_supplier"
                                                            id="is_for_supplier0"
                                                            {{ $data['role']->is_for_supplier == 0 ? 'checked' : '' }}
                                                            value="0">
                                                        <label for="is_for_supplier0">否</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @foreach ($data['permission'] as $main)
                                            <div class="col-sm-12">
                                                <div class="panel panel-info">
                                                    <div class="panel-heading">
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <i class="fa {{ $main['icon'] }} fa-fw"></i>
                                                                {{ $main['name'] }}
                                                            </div>
                                                            <div class="col-sm-8">
                                                                操作項目
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-body">
                                                        @if (isset($data['permissionDetail'][$main['id']]['id']))
                                                            @foreach ($data['permissionDetail'][$main['id']]['id'] as $k => $sub)
                                                                @if (isset($data['rolePermission'][$sub]))
                                                                    <div class="row">
                                                                        <div class="col-sm-3">
                                                                            <input type="checkbox" disabled
                                                                                id="auth_index_{{ $sub }}"
                                                                                {{ isset($data['rolePermission'][$sub]) ? 'checked' : '' }}
                                                                                name="auth_index[]"
                                                                                value="{{ $sub }}">
                                                                            <label for="auth_index_{{ $sub }}"><i
                                                                                    class="fa {{ $data['permissionDetail'][$main['id']]['icon'][$k] }} fa-fw"></i>
                                                                                {{ $data['permissionDetail'][$main['id']]['name'][$k] }}
                                                                            </label>

                                                                        </div>
                                                                        <div class="col-sm-9">
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div class="col-sm-2">

                                                                                        <input type="checkbox" disabled
                                                                                            id="auth_query_{{ $sub }}"
                                                                                            {{ isset($data['rolePermission'][$sub]['auth_query']) && $data['rolePermission'][$sub]['auth_query'] == 1 ? 'checked' : '' }}
                                                                                            name="auth_query_{{ $sub }}"
                                                                                            value="1">
                                                                                        <label
                                                                                            for="auth_query_{{ $sub }}">查詢</label>
                                                                                    </div>
                                                                                    <div class="col-sm-2">
                                                                                        <input type="checkbox" disabled
                                                                                            id="auth_create_{{ $sub }}"
                                                                                            {{ isset($data['rolePermission'][$sub]['auth_create']) && $data['rolePermission'][$sub]['auth_create'] == 1 ? 'checked' : '' }}
                                                                                            name="auth_create_{{ $sub }}"
                                                                                            value="1">
                                                                                        <label
                                                                                            for="auth_create_{{ $sub }}">新增</label>
                                                                                    </div>
                                                                                    <div class="col-sm-2">
                                                                                        <input type="checkbox" disabled
                                                                                            id="auth_update_{{ $sub }}"
                                                                                            {{ isset($data['rolePermission'][$sub]['auth_update']) && $data['rolePermission'][$sub]['auth_update'] == 1 ? 'checked' : '' }}
                                                                                            name="auth_update_{{ $sub }}"
                                                                                            value="1">
                                                                                        <label
                                                                                            for="auth_update_{{ $sub }}">修改</label>
                                                                                    </div>
                                                                                    <div class="col-sm-2">
                                                                                        <input type="checkbox" disabled
                                                                                            id="auth_delete_{{ $sub }}"
                                                                                            {{ isset($data['rolePermission'][$sub]['auth_delete']) && $data['rolePermission'][$sub]['auth_delete'] == 1 ? 'checked' : '' }}
                                                                                            name="auth_delete_{{ $sub }}"
                                                                                            value="1">
                                                                                        <label
                                                                                            for="auth_delete_{{ $sub }}">刪除</label>
                                                                                    </div>
                                                                                    <div class="col-sm-2">
                                                                                        <input type="checkbox" disabled
                                                                                            id="auth_void_{{ $sub }}"
                                                                                            {{ isset($data['rolePermission'][$sub]['auth_void']) && $data['rolePermission'][$sub]['auth_void'] == 1 ? 'checked' : '' }}
                                                                                            name="auth_void_{{ $sub }}"
                                                                                            value="1">
                                                                                        <label
                                                                                            for="auth_void_{{ $sub }}">作廢</label>
                                                                                    </div>
                                                                                    <div class="col-sm-2">
                                                                                        <input type="checkbox" disabled
                                                                                            id="auth_export_{{ $sub }}"
                                                                                            {{ isset($data['rolePermission'][$sub]['auth_export']) && $data['rolePermission'][$sub]['auth_export'] == 1 ? 'checked' : '' }}
                                                                                            name="auth_export_{{ $sub }}"
                                                                                            value="1">
                                                                                        <label
                                                                                            for="auth_export_{{ $sub }}">批次匯出</label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <hr style="margin-top:3px;" />
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-warning" id="btn-cancel"><i
                                                        class="fa fa-reply"></i> 返回列表
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(function() {
            $("#btn-cancel").click(function() {
                window.location.href = '{{ route('roles') }}';
            });
        })
    </script>
@endsection
