@extends('backend.master')
@section('title', '新增主分類管理')
@section('content')
<div id="page-wrapper">

    <!-- 表頭名稱 -->
    <div class="row">
        <div class="col-sm-12">
            <h1 class="page-header"><i class="fa fa-plus"></i> 新增分類</h1>
        </div>
    </div>

    <!-- /.row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">請輸入下列欄位資料</div>
                <div class="panel-body">
                    <form role="form" id="new-form" method="post" action="{{route('primary_category.store')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <!-- 欄位 -->
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_number">
                                            <label for="number">編號</label>
                                            <input class="form-control validate[required]" name="number" id="number">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_name">
                                            <label for="name">分類名稱</label>
                                            <input class="form-control validate[required]" name="name" id="name">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> 儲存</button>
                                            <a class="btn btn-danger" href="{{ route('primary_category') }}"><i class="fa fa-ban"></i> 取消</a>
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
            $("#new-form").validationEngine();
        })
    </script>
@endsection
