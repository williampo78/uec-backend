@extends('Backend.master')

@section('title', '部門管理')

@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-users"></i> 部門管理</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">

                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <div class="row">
                            @if($share_role_auth['auth_create'])
                            <div class="col-sm-2">
                                <a class="btn btn-block btn-warning btn-sm" id="btn-new" href="{{route('department.create')}}"><i class="fa fa-plus"></i> 新增</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                            <thead>
                            <tr>
                                <th class="col-sm-1">功能</th>
                                <th class="col-sm-3">編號</th>
                                <th class="col-sm-4">部門名稱</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data['department'] as $item)
                                <tr>
                                    <td>
                                        @if($share_role_auth['auth_update'])
                                            <a class="btn btn-info btn-sm" href="{{ route('department.edit' , $item['id']) }}">修改</a>
                                        @endif
                                    </td>
                                    <td>{{$item['number']}}</td>
                                    <td>{{$item['name']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')

@endsection
