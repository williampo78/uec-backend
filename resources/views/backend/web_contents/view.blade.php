@extends('backend.layouts.master')

@section('title', '商城頁面內容管理')

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-pencil"></i> 商城頁面內容管理</h1>
            </div>
        </div>
        <!-- /.row -->
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
                                <button type="button" class="btn btn-warning" id="btn-cancel">
                                    <i class="fa-solid fa-reply"></i> 返回列表
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('js')
    <script>
        $(function () {
            $('.js-select2').select2();

            $("#btn-cancel").on('click', function () {
                window.location.href = '{{ route('webcontents') }}';
            });

            ClassicEditor.create(document.querySelector('#editor'), {
                ckfinder: {
                    uploadUrl: "/ckfinder/connector?command=QuickUpload&type=Images&currentFolder=web_contents/&responseType=json&_token=" +
                        document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
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
                },
            })
                .then(editor => {
                    editor.isReadOnly = true;
                    //ck_description = editor; // Save for later use.
                }).catch(error => {
                console.error(error);
            });
        });
    </script>
@endsection
