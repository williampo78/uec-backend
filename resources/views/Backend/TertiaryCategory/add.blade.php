@extends('Backend.master')
@section('title', '新增小分類管理')
@section('content')
    <!--新增-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-plus"></i> 新增小分類</h1>
            </div>
        </div>

        <!-- /.row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body">
                        <form role="form" id="new-form" method="post" action="{{route('tertiary_category.store')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <!-- 欄位 -->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group" id="div_category">
                                                <label for="category">上層分類 <span style="color:red;">*</span></label>
                                                <select class="form-control js-select2" name="category_id">
                                                    <option value=""></option>
                                                    @foreach($parentCategories as $parentCategory)
                                                        <option value='{{ $parentCategory->c_id }}'>{{ $parentCategory->pc_name }} > {{ $parentCategory->c_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_category_number">
                                                <label for="category_number">編號 <span style="color:red;">*</span></label>
                                                <input class="form-control" name="number" id="number">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_category_name">
                                                <label for="category_name">分類名稱 <span style="color:red;">*</span></label>
                                                <input class="form-control" name="name" id="name">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button class="btn btn-success" type="submit" id="btn-save"><i class="fa fa-save"></i> 儲存</button>
                                                <a class="btn btn-danger" href="{{ route('tertiary_category') }}"><i class="fa fa-ban"></i> 取消</a>
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
            $('.js-select2').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '請選擇',
            });

            // 驗證表單
            $("#new-form").validate({
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
