@extends('Backend.master')

@section('title', '使用者管理')

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-pencil"></i> 編輯資料</h1>
            </div>
        </div>
        <!-- /.row -->
        <form role="form" id="update-form" method="post" action="{{ route('users.update', $data['user']->id) }}"
            enctype="multipart/form-data">
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
                                            <div class="form-group" id="div_account">
                                                <label for="account">帳號 <span class="text-danger">*</span></label>
                                                <input class="form-control" disabled name="user_account" id="user_account"
                                                    value="{{ $data['user']['user_account'] }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_user_name">
                                                <label for="password">名稱 <span class="text-danger">*</span></label>
                                                <input class="form-control" name="user_name"
                                                    id="user_name" value="{{ $data['user']['user_name'] }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="name">狀態 <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="active" id="active1"
                                                                {{ $data['user']['active'] == 1 ? 'checked' : '' }}
                                                                value="1">啟用
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="active" id="active0"
                                                                {{ $data['user']['active'] == 0 ? 'checked' : '' }}
                                                                value="0">關閉
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_user_password">
                                                <label for="name">密碼 <span class="text-danger">*不需變更請留空白</span></label>
                                                <input class="form-control" name="user_password" id="user_password"
                                                    type="password">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_email">
                                                <label for="email">信箱 <span class="text-danger">*</span></label>
                                                <input class="form-control" name="user_email"
                                                    id="user_email" value="{{ $data['user']['user_email'] }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_supplier_id">
                                                <label for="email">供應商 <span
                                                        class="text-primary">*供應商專用的帳號才指定供應商</span></label>
                                                <select name="supplier_id" id="supplier_id" class="js-select2">
                                                    <option value="">請選擇</option>
                                                    @foreach ($data['suppliers'] as $item)
                                                        <option value="{{ $item['id'] }}"
                                                            {{ $data['user']['supplier_id'] == $item['id'] ? 'selected' : '' }}>
                                                            {{ $item['name'] }}</option>
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
                                            @foreach ($data['roles'] as $item)
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" name="role[]"
                                                                value="{{ $item['id'] }}" id="role_{{ $item['id'] }}"
                                                                data-id="{{ $item['is_for_supplier'] }}"
                                                                {{ isset($data['user_roles'][$item['id']]) == 1 ? 'checked' : '' }}>{{ $item['role_name'] }}
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        @if ($item['is_for_supplier'] == 1)
                                                            <span class="text-danger">供應商專用</span>
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
                                <div class="col-xs-12 text-center">
                                    <div class="form-group">
                                        <button class="btn btn-success" id="btn-save" type="button"><i
                                                class="fa fa-check"></i> 完成
                                        </button>
                                        <button type="button" class="btn btn-danger" id="btn-cancel"><i
                                                class="fa fa-ban"></i> 取消
                                        </button>
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
            $('.js-select2').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '請選擇',
            });

            $("#btn-save").on('click', function() {
                $("#update-form").submit();
            });

            $("#btn-cancel").on('click', function() {
                window.location.href = '{{ route('users') }}';
            });

            $('input[name="role[]"]').on('click', function() {
                if ($('input[name="role[]"][data-id="1"]:checked').length < 1) {
                    $("#supplier_id").val('').trigger('change');
                }
            });

            // 驗證表單
            $("#update-form").validate({
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
                    user_email: {
                        required: true,
                    },
                    supplier_id: {
                        required: {
                            depends: function(element) {
                                return $('input[name="role[]"][data-id="1"]:checked').length > 0;
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
