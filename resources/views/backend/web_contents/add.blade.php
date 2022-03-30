@extends('backend.master')

@section('title', '商城頁面內容管理')

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-plus"></i> 商城頁面內容管理 新增資料</h1>
            </div>
        </div>
        <!-- /.row -->
        <form role="form" id="new-form" method="post" action="{{ route('webcontents.store') }}"
            enctype="multipart/form-data">
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
                                                <select name="parent_code" id="parent_code" class="js-select2">
                                                    <option value="">請選擇</option>
                                                    @foreach ($data['category'] as $cate)
                                                        <option value="{{ $cate['code'] }}">{{ $cate['description'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_content_name">
                                                <label for="content_name">項目名稱 <span class="text-danger">*</span></label>
                                                <input class="form-control" type="text"
                                                    name="content_name" id="content_name">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_sort">
                                                <label for="password">排序 <span class="text-danger">*</span></label>
                                                <input class="form-control" type="number"
                                                    name="sort" id="sort">
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
                                                        <option value="{{ $k }}">{{ $v }}</option>
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
                                    <div class="row" style="display: none;" id="div_content_url">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="content_url">URL </label>
                                                <input class="form-control" name="content_url" id="content_url">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="display: none;" id="div_editor">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="editor">單一圖文 </label>
                                                <textarea class="form-control" rows="5" id="editor"
                                                    name="content_text"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        @if ($share_role_auth['auth_create'])
                                            <button class="btn btn-success" type="button" id="btn-save">
                                                <i class="fa-solid fa-floppy-disk"></i> 儲存
                                            </button>
                                        @endif
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
                uploadUrl: "/ckfinder/connector?command=QuickUpload&type=Images&currentFolder=web_contents/&responseType=json&_token=" +
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

        $(function() {
            $('.js-select2').select2();

            $("#btn-save").on('click', function() {
                $("#new-form").submit();
            });

            $("#btn-cancel").on('click', function() {
                window.location.href = '{{ route('webcontents') }}';
            });

            $("#content_target").on('change', function() {
                switch ($(this).val()) {
                    // 站內連結
                    case 'S':
                    // 另開視窗
                    case 'B':
                        $("#div_content_url").show();
                        $("#div_editor").hide();
                        break;

                    // 單一圖文
                    case 'H':
                        $("#div_content_url").hide();
                        $("#div_editor").show();
                        break;

                    default:
                        $("#div_content_url").hide();
                        $("#div_editor").hide();
                        break;
                }
            });

            // 驗證表單
            $("#new-form").validate({
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
        });
    </script>
@endsection
