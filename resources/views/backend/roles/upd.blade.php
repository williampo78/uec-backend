@extends('backend.master')

@section('title', '角色管理')

@section('content')
    <!--新增-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-plus"></i> 編輯角色</h1>
            </div>
        </div>

        <!-- /.row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body">
                        <form id="update-form" method="post" action="{{ route('roles.update', $data['role']->id) }}"
                            enctype="multipart/form-data">
                            @method('PUT')
                            @csrf
                            <div class="row">

                                <!-- 欄位 -->
                                <div class="col-sm-12">

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="name">名稱 <span style="color: red;">*</span></label>
                                                <input class="form-control" name="role_name" id="role_name"
                                                    value="{{ $data['role']->role_name }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="name">狀態 <span style="color: red;">*</span></label>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="active" id="active1"
                                                                {{ $data['role']->active == 1 ? 'checked' : '' }}
                                                                value="1">啟用
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="active" id="active0"
                                                                {{ $data['role']->active == 0 ? 'checked' : '' }}
                                                                value="0">關閉
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="name">供應商專用 <span style="color: red;">*</span></label>
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="is_for_supplier" id="is_for_supplier1"
                                                                {{ $data['role']->is_for_supplier == 1 ? 'checked' : '' }}
                                                                value="1">是
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="is_for_supplier" id="is_for_supplier0"
                                                                {{ $data['role']->is_for_supplier == 0 ? 'checked' : '' }}
                                                                value="0">否
                                                        </label>
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
                                                                <div class="row">
                                                                    <div class="col-sm-3">
                                                                        <label class="checkbox-inline">
                                                                            <input type="checkbox"
                                                                                id="auth_index_{{ $sub }}"
                                                                                {{ isset($data['rolePermission'][$sub]) ? 'checked' : '' }}
                                                                                name="auth_index[]"
                                                                                value="{{ $sub }}"><i
                                                                                class="fa {{ $data['permissionDetail'][$main['id']]['icon'][$k] }} fa-fw"></i>
                                                                            {{ $data['permissionDetail'][$main['id']]['name'][$k] }}
                                                                        </label>
                                                                    </div>
                                                                    <div class="col-sm-9">
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <div class="col-sm-2">
                                                                                    <label class="checkbox-inline">
                                                                                        <input type="checkbox"
                                                                                            id="auth_query_{{ $sub }}"
                                                                                            {{ isset($data['rolePermission'][$sub]['auth_query']) && $data['rolePermission'][$sub]['auth_query'] == 1 ? 'checked' : '' }}
                                                                                            name="auth_query_{{ $sub }}"
                                                                                            value="1">查詢
                                                                                    </label>
                                                                                </div>
                                                                                <div class="col-sm-2">
                                                                                    <label class="checkbox-inline">
                                                                                        <input type="checkbox"
                                                                                            id="auth_create_{{ $sub }}"
                                                                                            {{ isset($data['rolePermission'][$sub]['auth_create']) && $data['rolePermission'][$sub]['auth_create'] == 1 ? 'checked' : '' }}
                                                                                            name="auth_create_{{ $sub }}"
                                                                                            value="1">新增
                                                                                    </label>
                                                                                </div>
                                                                                <div class="col-sm-2">
                                                                                    <label class="checkbox-inline">
                                                                                        <input type="checkbox"
                                                                                            id="auth_update_{{ $sub }}"
                                                                                            {{ isset($data['rolePermission'][$sub]['auth_update']) && $data['rolePermission'][$sub]['auth_update'] == 1 ? 'checked' : '' }}
                                                                                            name="auth_update_{{ $sub }}"
                                                                                            value="1">修改
                                                                                    </label>
                                                                                </div>
                                                                                <div class="col-sm-2">
                                                                                    <label class="checkbox-inline">
                                                                                        <input type="checkbox"
                                                                                            id="auth_delete_{{ $sub }}"
                                                                                            {{ isset($data['rolePermission'][$sub]['auth_delete']) && $data['rolePermission'][$sub]['auth_delete'] == 1 ? 'checked' : '' }}
                                                                                            name="auth_delete_{{ $sub }}"
                                                                                            value="1">刪除
                                                                                    </label>
                                                                                </div>
                                                                                <div class="col-sm-2">
                                                                                    <label class="checkbox-inline">
                                                                                        <input type="checkbox"
                                                                                            id="auth_void_{{ $sub }}"
                                                                                            {{ isset($data['rolePermission'][$sub]['auth_void']) && $data['rolePermission'][$sub]['auth_void'] == 1 ? 'checked' : '' }}
                                                                                            name="auth_void_{{ $sub }}"
                                                                                            value="1">作廢
                                                                                    </label>
                                                                                </div>
                                                                                <div class="col-sm-2">
                                                                                    <label class="checkbox-inline">
                                                                                        <input type="checkbox"
                                                                                            id="auth_export_{{ $sub }}"
                                                                                            {{ isset($data['rolePermission'][$sub]['auth_export']) && $data['rolePermission'][$sub]['auth_export'] == 1 ? 'checked' : '' }}
                                                                                            name="auth_export_{{ $sub }}"
                                                                                            value="1">批次匯出
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr style="margin-top:3px;" />
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
                                                <button type="button" class="btn btn-success" id="btn-save"><i
                                                        class="fa fa-save"></i> 儲存
                                                </button>
                                                <button type="button" class="btn btn-danger" id="btn-cancel"><i
                                                        class="fa fa-ban"></i> 取消
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
            $("#btn-save").on('click', function() {
                $("#update-form").submit();
            });

            $("#btn-cancel").on('click', function() {
                window.location.href = '{{ route('roles') }}';
            });

            // 驗證表單
            $("#update-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#btn-save').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    role_name: {
                        required: true,
                    },
                    active: {
                        required: true,
                    },
                    is_for_supplier: {
                        required: true,
                    },
                },
                errorClass: "help-block",
                errorElement: "span",
                errorPlacement: function(error, element) {
                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                        return;
                    }

                    if (element.closest(".form-group").length) {
                        element.closest(".form-group").append(error);
                        return;
                    }

                    error.insertAfter(element);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).closest(".form-group").addClass("has-error");
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
                success: function(label, element) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
            });
        })
    </script>
@endsection
