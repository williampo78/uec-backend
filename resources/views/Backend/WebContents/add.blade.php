@extends('Backend.master')

@section('title', '商城頁面內容管理')

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-plus"></i> 商城頁面內容管理 新增資料</h1>
            </div>
        </div>
        <!-- /.row -->
        <form role="form" id="new-form" method="post" action="{{route('webcontents.store')}}" enctype="multipart/form-data">
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
                                                <label for="account">類別 <span class="text-danger">*</span></label>
                                                <select name="parent_code" id="parent_code" class="validate[required]">
                                                    <option value="">請選擇</option>
                                                    @foreach($data['category'] as $cate)
                                                        <option value="{{$cate['code']}}">{{$cate['description']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_content_name">
                                                <label for="content_name">項目名稱 <span class="text-danger">*</span></label>
                                                <input class="form-control validate[required]" type="text" name="content_name"
                                                       id="content_name">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_sort">
                                                <label for="password">排序 <span class="text-danger">*</span></label>
                                                <input class="form-control validate[required,custom[integer]]" type="number" name="sort"
                                                       id="sort">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_content_target">
                                                <label for="content_target">類型 <span class="text-danger">*</span></label>
                                                <select name="content_target" id="content_target" class="validate[required]">
                                                    <option value="">請選擇</option>
                                                    @foreach($data['target'] as $k=>$v)
                                                        <option value="{{$k}}">{{$v}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_name">
                                                <label for="name">狀態 <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <input type="radio"
                                                               name="active" id="active1" checked
                                                               value="1">
                                                        <label for="active1">啟用</label>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <input type="radio"
                                                               name="active" id="active0"
                                                               value="0">
                                                        <label for="active0">關閉</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="display: none;" id="div_content_url">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="content_name">URL </label>
                                                <input class="form-control" name="content_url"
                                                       id="content_url">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="display: none;" id="div_editor">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="editor">單一圖文 </label>
                                                <textarea class="form-control" rows="5" id="editor" name="content_text"></textarea>
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

        ClassicEditor.create( document.querySelector( '#editor' ), {

            ckfinder: {
                // Upload the images to the server using the CKFinder QuickUpload command.
                uploadUrl: "/ckfinder/connector?command=QuickUpload&type=Images&responseType=json&_token=" +document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                //uploadUrl:"/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }

            },
        })
        $(function () {
            $("#new-form").validationEngine();
            $("select").select2();
            $("#btn-save").click(function () {
                $("#new-form").submit();
            });
            $("#btn-cancel").click(function () {
                window.location.href = '{{route("webcontents")}}';
            });
            $("#content_target").change(function () {
                if ($(this).val() =='S') {
                    $("#div_content_url").show();
                    $("#div_editor").hide();
                } else if ($(this).val() =='H') {
                    $("#div_content_url").hide();
                    $("#div_editor").show();
                } else {
                    $("#div_content_url").hide();
                    $("#div_editor").hide();
                }
            });
        })
    </script>
@endsection
