@extends('backend.master')
@section('title', '分類階層內容管理')
@section('content')
    <style>
        .no-pa {
            padding: 0px;
        }

    </style>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i>測試圖片</h1>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">請輸入下列欄位資料</div>
            <div class="panel-body" id="category_hierarchy_content_input">

                <form action="upload_img" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="photo[]" multiple>
                    <button type="submit">按我按我</button>
                </form>

                <img style="width: 200px ; height:200px; " src="" alt="">
{{--
                <form class="form-horizontal" role="form" id="new-form" method="POST" action="/upload_img"
                    enctype="multipart/form-data" novalidate="novalidate">
                    @csrf
                    <input type="file" name="photo">
                </form> --}}
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
    </script>
@endsection
