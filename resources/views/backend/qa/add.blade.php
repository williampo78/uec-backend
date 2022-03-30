@extends('backend.master')

@section('title', '常見問題Q&A')

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-pencil"></i> 新增資料</h1>
            </div>
        </div>
        <!-- /.row -->
        <form role="form" id="new-form" method="post" action="{{ route('qa.store') }}" enctype="multipart/form-data">
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
                                                <label for="parent_code">類別 <span style="color: red;">*</span></label>
                                                <select name="parent_code" id="parent_code" class="js-select2">
                                                    <option value=""></option>
                                                    @foreach ($data['category'] as $cate)
                                                        <option value="{{ $cate['code'] }}">{{ $cate['description'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_sort">
                                                <label for="sort">排序 <span style="color: red;">*</span></label>
                                                <input class="form-control" type="number" name="sort" id="sort">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>狀態 <span style="color: red;">*</span></label>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="active" id="active1" checked
                                                                   value="1">啟用
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="active" id="active0" value="0">關閉
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="content_name">問題描述 <span
                                                        style="color: red;">*</span></label>
                                                <input class="form-control" name="content_name" id="content_name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="editor">問題解答 <span style="color: red;">*</span></label>
                                                <textarea class="form-control" rows="5" id="editor"
                                                          name="content_text"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-success" id="btn-save">
                                            <i class="fa-solid fa-floppy-disk"></i> 儲存
                                        </button>
                                        <button type="button" class="btn btn-danger" id="btn-cancel">
                                            <i class="fa-solid fa-ban"></i> 取消
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
        ClassicEditor.create(document.querySelector('#editor'), {
            ckfinder: {
                // Upload the images to the server using the CKFinder QuickUpload command.
                uploadUrl: "/ckfinder/connector?command=QuickUpload&type=Images&currentFolder=qa_content/&responseType=json&_token=" +
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
                    { name: 'a', attributes: true, classes: true, styles: true },
                    { name: 'abbr', attributes: true, classes: true, styles: true },
                    { name: 'acronym', attributes: true, classes: true, styles: true },
                    { name: 'address', attributes: true, classes: true, styles: true },
                    { name: 'applet', attributes: true, classes: true, styles: true },
                    { name: 'area', attributes: true, classes: true, styles: true },
                    { name: 'article', attributes: true, classes: true, styles: true },
                    { name: 'aside', attributes: true, classes: true, styles: true },
                    { name: 'audio', attributes: true, classes: true, styles: true },
                    { name: 'b', attributes: true, classes: true, styles: true },
                    { name: 'base', attributes: true, classes: true, styles: true },
                    { name: 'basefont', attributes: true, classes: true, styles: true },
                    { name: 'bdi', attributes: true, classes: true, styles: true },
                    { name: 'bdo', attributes: true, classes: true, styles: true },
                    { name: 'big', attributes: true, classes: true, styles: true },
                    { name: 'blockquote', attributes: true, classes: true, styles: true },
                    { name: 'br', attributes: true, classes: true, styles: true },
                    { name: 'button', attributes: true, classes: true, styles: true },
                    { name: 'canvas', attributes: true, classes: true, styles: true },
                    { name: 'caption', attributes: true, classes: true, styles: true },
                    { name: 'center', attributes: true, classes: true, styles: true },
                    { name: 'cite', attributes: true, classes: true, styles: true },
                    { name: 'code', attributes: true, classes: true, styles: true },
                    { name: 'col', attributes: true, classes: true, styles: true },
                    { name: 'colgroup', attributes: true, classes: true, styles: true },
                    { name: 'data', attributes: true, classes: true, styles: true },
                    { name: 'datalist', attributes: true, classes: true, styles: true },
                    { name: 'dd', attributes: true, classes: true, styles: true },
                    { name: 'del', attributes: true, classes: true, styles: true },
                    { name: 'details', attributes: true, classes: true, styles: true },
                    { name: 'dfn', attributes: true, classes: true, styles: true },
                    { name: 'dialog', attributes: true, classes: true, styles: true },
                    { name: 'dir', attributes: true, classes: true, styles: true },
                    { name: 'div', attributes: true, classes: true, styles: true },
                    { name: 'dl', attributes: true, classes: true, styles: true },
                    { name: 'dt', attributes: true, classes: true, styles: true },
                    { name: 'em', attributes: true, classes: true, styles: true },
                    { name: 'embed', attributes: true, classes: true, styles: true },
                    { name: 'fieldset', attributes: true, classes: true, styles: true },
                    { name: 'figcaption', attributes: true, classes: true, styles: true },
                    { name: 'figure', attributes: true, classes: true, styles: true },
                    { name: 'font', attributes: true, classes: true, styles: true },
                    { name: 'footer', attributes: true, classes: true, styles: true },
                    { name: 'form', attributes: true, classes: true, styles: true },
                    { name: 'frame', attributes: true, classes: true, styles: true },
                    { name: 'frameset', attributes: true, classes: true, styles: true },
                    { name: 'h1 to h6', attributes: true, classes: true, styles: true },
                    { name: 'header', attributes: true, classes: true, styles: true },
                    { name: 'hr', attributes: true, classes: true, styles: true },
                    { name: 'i', attributes: true, classes: true, styles: true },
                    { name: 'img', attributes: true, classes: true, styles: true },
                    { name: 'input', attributes: true, classes: true, styles: true },
                    { name: 'ins', attributes: true, classes: true, styles: true },
                    { name: 'kbd', attributes: true, classes: true, styles: true },
                    { name: 'label', attributes: true, classes: true, styles: true },
                    { name: 'legend', attributes: true, classes: true, styles: true },
                    { name: 'li', attributes: true, classes: true, styles: true },
                    { name: 'link', attributes: true, classes: true, styles: true },
                    { name: 'main', attributes: true, classes: true, styles: true },
                    { name: 'map', attributes: true, classes: true, styles: true },
                    { name: 'mark', attributes: true, classes: true, styles: true },
                    { name: 'meta', attributes: true, classes: true, styles: true },
                    { name: 'meter', attributes: true, classes: true, styles: true },
                    { name: 'nav', attributes: true, classes: true, styles: true },
                    { name: 'noframes', attributes: true, classes: true, styles: true },
                    { name: 'noscript', attributes: true, classes: true, styles: true },
                    { name: 'object', attributes: true, classes: true, styles: true },
                    { name: 'ol', attributes: true, classes: true, styles: true },
                    { name: 'optgroup', attributes: true, classes: true, styles: true },
                    { name: 'option', attributes: true, classes: true, styles: true },
                    { name: 'output', attributes: true, classes: true, styles: true },
                    { name: 'p', attributes: true, classes: true, styles: true },
                    { name: 'param', attributes: true, classes: true, styles: true },
                    { name: 'picture', attributes: true, classes: true, styles: true },
                    { name: 'pre', attributes: true, classes: true, styles: true },
                    { name: 'progress', attributes: true, classes: true, styles: true },
                    { name: 'q', attributes: true, classes: true, styles: true },
                    { name: 'rp', attributes: true, classes: true, styles: true },
                    { name: 'rt', attributes: true, classes: true, styles: true },
                    { name: 'ruby', attributes: true, classes: true, styles: true },
                    { name: 's', attributes: true, classes: true, styles: true },
                    { name: 'samp', attributes: true, classes: true, styles: true },
                    { name: 'section', attributes: true, classes: true, styles: true },
                    { name: 'select', attributes: true, classes: true, styles: true },
                    { name: 'small', attributes: true, classes: true, styles: true },
                    { name: 'source', attributes: true, classes: true, styles: true },
                    { name: 'span', attributes: true, classes: true, styles: true },
                    { name: 'strike', attributes: true, classes: true, styles: true },
                    { name: 'strong', attributes: true, classes: true, styles: true },
                    { name: 'style', attributes: true, classes: true, styles: true },
                    { name: 'sub', attributes: true, classes: true, styles: true },
                    { name: 'summary', attributes: true, classes: true, styles: true },
                    { name: 'sup', attributes: true, classes: true, styles: true },
                    { name: 'svg', attributes: true, classes: true, styles: true },
                    { name: 'table', attributes: true, classes: true, styles: true },
                    { name: 'tbody', attributes: true, classes: true, styles: true },
                    { name: 'td', attributes: true, classes: true, styles: true },
                    { name: 'template', attributes: true, classes: true, styles: true },
                    { name: 'textarea', attributes: true, classes: true, styles: true },
                    { name: 'tfoot', attributes: true, classes: true, styles: true },
                    { name: 'th', attributes: true, classes: true, styles: true },
                    { name: 'thead', attributes: true, classes: true, styles: true },
                    { name: 'time', attributes: true, classes: true, styles: true },
                    { name: 'title', attributes: true, classes: true, styles: true },
                    { name: 'tr', attributes: true, classes: true, styles: true },
                    { name: 'track', attributes: true, classes: true, styles: true },
                    { name: 'tt', attributes: true, classes: true, styles: true },
                    { name: 'u', attributes: true, classes: true, styles: true },
                    { name: 'ul', attributes: true, classes: true, styles: true },
                    { name: 'var', attributes: true, classes: true, styles: true },
                    { name: 'video', attributes: true, classes: true, styles: true },
                    { name: 'wbr', attributes: true, classes: true, styles: true },
                ]
            }
        })

        $(function () {
            $('.js-select2').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '請選擇',
            });

            $("#btn-save").on('click', function () {
                $("#new-form").submit();
            });

            $("#btn-cancel").on('click', function () {
                window.location.href = '{{ route('qa') }}';
            });

            // 驗證表單
            $("#new-form").validate({
                // debug: true,
                submitHandler: function (form) {
                    $('#btn-save').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    parent_code: {
                        required: true,
                    },
                    sort: {
                        required: true,
                        digits: true,
                        min: 1,
                    },
                    active: {
                        required: true,
                    },
                    content_name: {
                        required: true,
                    },
                    content_text: {
                        required: true,
                    },
                },
                messages: {
                    sort: {
                        digits: "只可輸入正整數",
                        min: "只可輸入正整數",
                    },
                },
                errorClass: "help-block",
                errorElement: "span",
                errorPlacement: function (error, element) {
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
                highlight: function (element, errorClass, validClass) {
                    $(element).closest(".form-group").addClass("has-error");
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
                success: function (label, element) {
                    $(element).closest(".form-group").removeClass("has-error");
                },
            });
        });
    </script>
@endsection
