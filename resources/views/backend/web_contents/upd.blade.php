@extends('backend.master')

@section('title', '商城頁面內容管理')

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-pencil"></i> 商城頁面內容管理 編輯資料</h1>
            </div>
        </div>
        <!-- /.row -->
        <form role="form" id="update-form" method="post" action="{{ route('webcontents.update', $data['webcontent']->id) }}"
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
                                            <div class="form-group">
                                                <label for="account">類別 <span class="text-danger">*</span></label>
                                                <select name="parent_code" id="parent_code"
                                                    class="js-select2">
                                                    <option value="">請選擇</option>
                                                    @foreach ($data['category'] as $cate)
                                                        <option value="{{ $cate['code'] }}"
                                                            {{ $data['webcontent']['parent_code'] == $cate['code'] ? 'selected' : '' }}>
                                                            {{ $cate['description'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_content_name">
                                                <label for="content_name">項目名稱 <span class="text-danger">*</span></label>
                                                <input class="form-control" type="text"
                                                    name="content_name" id="content_name"
                                                    value="{{ $data['webcontent']['content_name'] }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_sort">
                                                <label for="password">排序 <span class="text-danger">*</span></label>
                                                <input class="form-control" type="number"
                                                    name="sort" id="sort" value="{{ $data['webcontent']['sort'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_content_target">
                                                <label for="content_target">類型 <span class="text-danger">*</span></label>
                                                <select name="content_target" id="content_target"
                                                    class="js-select2">
                                                    <option value="">請選擇</option>
                                                    @foreach ($data['target'] as $k => $v)
                                                        <option value="{{ $k }}"
                                                            {{ $data['webcontent']['content_target'] == $k ? 'selected' : '' }}>
                                                            {{ $v }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="name">狀態 <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="active" id="active1"
                                                                {{ $data['webcontent']['active'] == 1 ? 'checked' : '' }}
                                                                value="1">啟用
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="active" id="active0"
                                                                {{ $data['webcontent']['active'] == 0 ? 'checked' : '' }}
                                                                value="0">關閉
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="div_content_url"
                                        style="display: {{ $data['webcontent']['content_target'] == 'S' ? '' : 'none;' }};">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="content_url">URL </label>
                                                <input class="form-control" name="content_url" id="content_url"
                                                    value="{{ $data['webcontent']['content_url'] }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="div_editor"
                                        style="display: {{ $data['webcontent']['content_target'] == 'H' ? '' : 'none;' }};">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="editor">單一圖文</label>
                                                <textarea id="editor" name="content_text"
                                                    placeholder="請在這裡填寫內容">{{ $data['webcontent']['content_text'] }}</textarea>

                                            </div>
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
            $('.js-select2').select2();

            $("#btn-save").on('click', function() {
                $("#update-form").submit();
            });

            $("#btn-cancel").on('click', function() {
                window.location.href = '{{ route('webcontents') }}';
            });

            $("#content_target").on('change', function() {
                if ($(this).val() == 'S') {
                    $("#div_content_url").show();
                    $("#div_editor").hide();
                } else if ($(this).val() == 'H') {
                    $("#div_content_url").hide();
                    $("#div_editor").show();
                } else {
                    $("#div_content_url").hide();
                    $("#div_editor").hide();
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
                    parent_code: {
                        required: true,
                    },
                    content_name: {
                        required: true,
                    },
                    sort: {
                        required: true,
                        digits: true,
                    },
                    content_target: {
                        required: true,
                    },
                    active: {
                        required: true,
                    },
                    content_url: {
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

        ClassicEditor.create(document.querySelector('#editor'), {
            ckfinder: {
                // Upload the images to the server using the CKFinder QuickUpload command.
                uploadUrl: "/ckfinder/connector?command=QuickUpload&type=Images&responseType=json&_token=" +
                    document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                //uploadUrl:"/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            },
            mediaEmbed: {
                previewsInData: true
            },
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            }
        })
    </script>
@endsection
