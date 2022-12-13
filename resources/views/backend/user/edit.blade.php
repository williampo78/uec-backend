@extends('backend.layouts.master')

@section('title', '使用者管理')

@section('css')
    <style>
        #password-title {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: space-between;
        }
    </style>
@endsection

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-pencil"></i> 編輯資料</h1>
            </div>
        </div>
        <form id="edit-form" method="post" action="{{ route('users.update', $user->id) }}">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">請輸入下列欄位資料</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="user_account">帳號 <span class="text-red">*</span></label>
                                                <input class="form-control" disabled name="user_account" id="user_account"
                                                    value="{{ $user->user_account }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="user_name">名稱 <span class="text-red">*</span></label>
                                                <input class="form-control" name="user_name" id="user_name"
                                                    value="{{ $user->user_name }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>狀態 <span class="text-red">*</span></label>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="active" id="active1"
                                                                {{ $user->active == 1 ? 'checked' : '' }} value="1">啟用
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="active" id="active0"
                                                                {{ $user->active == 0 ? 'checked' : '' }} value="0">關閉
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <div id="password-title">
                                                    <label for="user_password">密碼 <span
                                                            class="text-red">(不需變更請留空白)</span></label>
                                                    <span class="text-primary" id="password-tooltip">
                                                        <i class="fa-solid fa-circle-info"></i> 格式說明
                                                    </span>
                                                </div>
                                                <input class="form-control" name="user_password" id="user_password"
                                                    type="password" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="user_email">信箱 <span class="text-red">*</span></label>
                                                <input class="form-control" name="user_email" id="user_email"
                                                    value="{{ $user->user_email }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="supplier_id">供應商 <span
                                                        class="text-red">*供應商專用的帳號才指定供應商</span></label>
                                                <select name="supplier_id" id="supplier_id" class="select2-default">
                                                    <option value="">請選擇</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}"
                                                            {{ $user->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                            {{ $supplier->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">授權角色</div>
                                        <div class="panel-body">
                                            @foreach ($roles as $role)
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" name="roles[]"
                                                                value="{{ $role->id }}" id="role_{{ $role->id }}"
                                                                data-is-for-supplier="{{ $role->is_for_supplier }}"
                                                                {{ $user->roles->contains('id', $role->id) ? 'checked' : '' }}>{{ $role->role_name }}
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        @if ($role->is_for_supplier == 1)
                                                            <span class="text-red">供應商專用</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <hr style="margin-top:3px;" />
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        @if ($share_role_auth['auth_update'])
                                            <button type="button" class="btn btn-success" id="btn-save">
                                                <i class="fa-solid fa-floppy-disk"></i> 儲存
                                            </button>
                                        @endif

                                        <a href="{{ route('users') }}" class="btn btn-danger">
                                            <i class="fa-solid fa-ban"></i> 取消
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('#password-tooltip').tooltip({
                title: "需包含英文和數字，且介於8~20個字元，符號可輸入：!@#$%^&*().-=_~",
            });

            $("#btn-save").on('click', function() {
                $("#edit-form").submit();
            });

            // 有選取供應商專用
            if ($('input[name^="role"][data-is-for-supplier="1"]:checked').length < 1) {
                $("#supplier_id").prop('disabled', true);
            }

            // 點選授權角色
            $('input[name^="role"]').on('click', function() {
                // 有選取供應商專用
                if ($('input[name^="role"][data-is-for-supplier="1"]:checked').length > 0) {
                    $('#supplier_id').prop('disabled', false);
                }
                // 未選取供應商專用
                else {
                    $("#supplier_id").prop('disabled', true).val('').trigger('change');
                }
            });

            // 驗證表單
            $("#edit-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#btn-save').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    user_name: {
                        required: true,
                    },
                    active: {
                        required: true,
                    },
                    user_password: {
                        drowssapCheck: {
                            depends: function(element) {
                                return $('#user_password').val().length > 0;
                            },
                        },
                    },
                    user_email: {
                        required: true,
                        email: true,
                        maxlength: 50,
                    },
                    supplier_id: {
                        required: {
                            depends: function(element) {
                                return $('input[name^="role"][data-is-for-supplier="1"]:checked')
                                    .length > 0;
                            }
                        },
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
