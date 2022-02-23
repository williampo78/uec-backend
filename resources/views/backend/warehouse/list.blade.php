@extends('backend.master')
@section('title', '倉庫維護')
@section('content')
    <!--列表-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-building-columns"></i> 倉庫維護</h1>
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
                                <a class="btn btn-block btn-warning btn-sm" id="btn-new" href="{{route('warehouse.create')}}"><i class="fa-solid fa-plus"></i> 新增</a>
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
                                <th class="col-sm-2">編號</th>
                                <th class="col-sm-9">名稱</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($data as $k => $v)
                                <tr>
                                    <td>
                                        @if($share_role_auth['auth_update'])
                                        <a class="btn btn-block btn-info btn-sm" href="{{route('warehouse.edit' , $v['id'])}}"><i class="fa-solid fa-pencil"></i> 編輯</a>
                                        @endif
                                    </td>
                                    <td>{{ $v['number'] }}</td>
                                    <td>{{ $v['name'] }}</td>
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
