@extends('backend.layouts.master')

@section('title', '功能名稱')

@section('content')
    <!--新增-->

    <div id="page-wrapper" style="min-height: 508px;">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-plus"></i>{{ isset($ShowData) ? '編輯' : '新增' }}供應商類別</h1>
            </div>
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body">
                        @if (isset($ShowData))
                            <form role="form" id="new-form" method="POST"
                                action="{{ route('supplier_type.update', $ShowData['id']) }}"
                                enctype="multipart/form-data" novalidate="novalidate">
                                @method('PUT')
                                @csrf
                            @else
                                <form role="form" id="new-form" method="POST" action="{{ route('supplier_type') }}"
                                    enctype="multipart/form-data" novalidate="novalidate">
                        @endif
                        <input type="hidden" name="id" value="{{ isset($ShowData['id']) ? $ShowData['id'] : '' }}">
                        <div class="row">
                            @csrf
                            <!-- 欄位 -->
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_supplier_type_code">
                                            <label for="supplier_type_code">編號 <span style="color:red;">*</span></label>
                                            <input class="form-control" name="code"
                                                value="{{ isset($ShowData['code']) ? $ShowData['code'] : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_supplier_type_name">
                                            <label for="supplier_type_name">供應商類別名稱 <span style="color:red;">*</span></label>
                                            <input class="form-control" name="name"
                                                value="{{ isset($ShowData['name']) ? $ShowData['name'] : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="btn btn-success" id="btn-save"><i class="fa-solid fa-floppy-disk"></i>
                                                儲存</button>
                                            <a href="{{route('supplier_type')}}" class="btn btn-danger" id="btn-cancel"><i class="fa-solid fa-ban"></i>
                                                取消</a>
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
        $(function () {
            // 驗證表單
            $("#new-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#btn-save').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    code: {
                        required: true,
                    },
                    name: {
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
