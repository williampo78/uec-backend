@extends('backend.master')

@section('title', '部門管理')

@section('content')
    <!--新增-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-plus"></i> 新增部門</h1>
            </div>
        </div>

        <!-- /.row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body">
                        @if(isset($department))
                            <form role="form" id="new-form" method="post"
                                  action="{{ route('department.update', $department->id) }}"
                                  enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                        @else
                            <form role="form" id="new-form" method="post" action="{{route('department')}}"
                                  enctype="multipart/form-data">
                        @endif
                        @csrf
                                <div class="row">
                                    <input type="hidden" name="agent_id"
                                           value="{{ Auth::user()->agent_id }}">
                                    <!-- 欄位 -->
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_department_number">
                                                    <label for="department_number">部門編號</label>
                                                    <input class="form-control validate[required]" name="number"
                                                           id="department_number"
                                                           value="{{ isset($department)?$department->number:'' }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group" id="div_department_name">
                                                    <label for="department_name">部門名稱</label>
                                                    <input class="form-control validate[required]" name="name"
                                                           id="department_name"
                                                           value="{{ isset($department)?$department->name:'' }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <button class="btn btn-success" id="btn-save" type="button">
                                                        <i class="fa-solid fa-floppy-disk"></i> 儲存
                                                    </button>
                                                    <button class="btn btn-danger" id="btn-cancel" type="button">
                                                        <i class="fa-solid fa-ban"></i> 取消
                                                    </button>
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
            $("#btn-save").click(function () {
                $("#new-form").submit();
            });
            $("#btn-cancel").click(function () {
                window.location.href = '{{route("department")}}';
            });
        })
    </script>
@endsection
