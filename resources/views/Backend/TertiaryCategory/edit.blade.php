@extends('Backend.master')
@section('title', '編輯子分類管理')
@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-pencil"></i> 編輯小分類</h1>
            </div>
        </div>
        <!-- /.row -->
        <form method="POST" id="edit-form" action="{{ route('tertiary_category.update', ['id' => $tertiaryCategory->id]) }}">
            @method('put')
            @csrf
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">請輸入下列欄位資料</div>
                        <div class="panel-body">

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group" id="div_category">
                                        <label for="category">上層分類 <span style="color:red;">*</span></label>
                                        <select class="form-control js-select2" name="category_id" id="category_id">
                                            <option value=""></option>
                                            @foreach($parentCategories as $parentCategory)
                                                <option value='{{ $parentCategory->c_id }}' {{ $tertiaryCategory->category_id == $parentCategory->c_id ? 'selected' : null }}>{{ $parentCategory->pc_name }} > {{ $parentCategory->c_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_number">
                                                <label for="number">編號 <span style="color:red;">*</span></label>
                                                <input class="form-control" name="number" id="number" value="{{ $tertiaryCategory->number }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="name">名稱 <span style="color:red;">*</span></label>
                                                <input class="form-control" name="name" id="name" value="{{ $tertiaryCategory->name }}">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <button id="btn-save" class="btn btn-success" type="submit"><i class="fa fa-check"></i> 完成</button>
                                        <a class="btn btn-danger" href=""><i class="fa fa-ban"></i> 取消</a>
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
        $(function () {
            $('.js-select2').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '請選擇',
            });

            // 驗證表單
            $("#edit-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#btn-save').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    category_id: {
                        required: true,
                    },
                    number: {
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
