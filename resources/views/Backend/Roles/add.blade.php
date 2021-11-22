@extends('Backend.master')

@section('title', '角色管理')

@section('content')
    <!--新增-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-plus"></i> 新增角色</h1>
            </div>
        </div>

        <!-- /.row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body">
                        <form id="new-form" method="post" action="{{route('roles.store')}}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <!-- 欄位 -->
                                <div class="col-sm-12">

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_name">
                                                <label for="name">名稱 <span class="text-danger">*</span></label>
                                                <input class="form-control validate[required]" name="role_name"
                                                       id="role_name">
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
                                        <div class="col-sm-4">
                                            <div class="form-group" id="div_name">
                                                <label for="name">供應商專用 <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <input type="radio"
                                                               name="is_for_supplier" id="is_for_supplier1"
                                                               value="1">
                                                        <label for="is_for_supplier1">是</label>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <input type="radio"
                                                               name="is_for_supplier" id="is_for_supplier0" checked
                                                               value="0">
                                                        <label for="is_for_supplier0">否</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @foreach($data['permission'] as $main)
                                            <div class="col-sm-12">
                                                <div class="panel panel-info">
                                                    <div class="panel-heading">
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <i class="fa {{$main['icon']}} fa-fw"></i> {{$main['name']}}
                                                            </div>
                                                            <div class="col-sm-8">
                                                                操作項目
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-body">
                                                        @if(isset($data['permissionDetail'][$main['id']]['id']))
                                                            @foreach($data['permissionDetail'][$main['id']]['id'] as $k=>$sub)
                                                                <div class="row">
                                                                    <div class="col-sm-3">
                                                                        <input type="checkbox"
                                                                               id="auth_index_{{$sub}}"
                                                                               name="auth_index[]" value="{{$sub}}">
                                                                        <label
                                                                            for="auth_index_{{$sub}}"><i
                                                                                class="fa {{$data['permissionDetail'][$main['id']]['icon'][$k]}} fa-fw"></i> {{$data['permissionDetail'][$main['id']]['name'][$k]}}
                                                                        </label>

                                                                    </div>
                                                                    <div class="col-sm-9">
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <div class="col-sm-2">
                                                                                    <input
                                                                                        type="checkbox"
                                                                                        id="auth_query_{{$sub}}"
                                                                                        name="auth_query_{{$sub}}"
                                                                                        value="1">
                                                                                    <label
                                                                                        for="auth_query_{{$sub}}">查詢</label>
                                                                                </div>
                                                                                <div class="col-sm-2">
                                                                                    <input
                                                                                        type="checkbox"
                                                                                        id="auth_create_{{$sub}}"
                                                                                        name="auth_create_{{$sub}}"
                                                                                        value="1">
                                                                                    <label
                                                                                        for="auth_create_{{$sub}}">新增</label>
                                                                                </div>
                                                                                <div class="col-sm-2">
                                                                                    <input
                                                                                        type="checkbox"
                                                                                        id="auth_update_{{$sub}}"
                                                                                        name="auth_update_{{$sub}}"
                                                                                        value="1">
                                                                                    <label
                                                                                        for="auth_update_{{$sub}}">修改</label>
                                                                                </div>
                                                                                <div class="col-sm-2">
                                                                                    <input
                                                                                        type="checkbox"
                                                                                        id="auth_delete_{{$sub}}"
                                                                                        name="auth_delete_{{$sub}}"
                                                                                        value="1">
                                                                                    <label
                                                                                        for="auth_delete_{{$sub}}">刪除</label>
                                                                                </div>
                                                                                <div class="col-sm-2">
                                                                                    <input
                                                                                        type="checkbox"
                                                                                        id="auth_void_{{$sub}}"
                                                                                        name="auth_void_{{$sub}}"
                                                                                        value="1">
                                                                                    <label
                                                                                        for="auth_void_{{$sub}}">作廢</label>
                                                                                </div>
                                                                                <div class="col-sm-2">
                                                                                    <input
                                                                                        type="checkbox"
                                                                                        id="auth_export_{{$sub}}"
                                                                                        name="auth_export_{{$sub}}"
                                                                                        value="1">
                                                                                    <label
                                                                                        for="auth_export_{{$sub}}">批次匯出</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr style="margin-top:3px;"/>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group text-center">
                                                <button type="button" class="btn btn-success" id="btn-save"><i
                                                        class="fa fa-save"></i> 儲存
                                                </button>
                                                <button type="button" class="btn btn-danger" id="btn-cancel"><i
                                                        class="fa fa-ban"></i> 取消
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
                window.location.href = '{{route("roles")}}';
            });
        })
    </script>
@endsection
