@extends('Backend.master')

@section('content')
    <!--新增-->
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
                        <form role="form" id="new-form" method="post" action="{{route('category.store')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <!-- 欄位 -->
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group" id="div_category">
                                                <label for="category">主分類</label>
                                                <select class="form-control js-select2 validate[required]" name="primary_category_id">
                                                    <option value="">請選擇</option>
                                                    @foreach($primary_category as $k => $v)
                                                        <option value='{{ $v['id'] }}'>{{ $v['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_category_number">
                                                <label for="category_number">編號</label>
                                                <input class="form-control validate[required]" name="number" id="category_number">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="div_category_name">
                                                <label for="category_name">分類名稱</label>
                                                <input class="form-control validate[required]" name="name" id="category_name">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> 儲存</button>
                                                <a class="btn btn-danger" href="{{route('category')}}"><i class="fa fa-ban"></i> 取消</a>
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
