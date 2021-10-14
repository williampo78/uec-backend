@extends('Backend.master')

@section('title', '功能名稱')

@section('content')
    <!--新增-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-truck"></i>供應商資料</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-2">
                                <a href="{{ route('supplier') }}/create" class="btn btn-block btn-warning btn-sm"
                                    id="btn-new"><i class="fa fa-plus"></i>
                                    新增</a>
                            </div>
                        </div>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div id="table_list_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                            <table class="table table-striped table-bordered table-hover" style="width:100%"
                                id="table_list">
                                <thead>
                                    <tr role="row">
                                        <th class="col-sm-1 ">功能</th>
                                        <th class="col-sm-1 ">編號</th>
                                        <th class="col-sm-1 ">統編</th>
                                        <th class="col-sm-1 ">簡稱</th>
                                        <th class="col-sm-1 ">名稱</th>
                                        <th class="col-sm-1 ">付款條件</th>
                                        <th class="col-sm-1 ">電話</th>
                                        <th class="col-sm-1 ">地址</th>
                                        <th class="col-sm-1 ">備註</th>
                                        <th class="col-sm-1 ">顯示</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($supplier as $obj)
                                        <tr>
                                            <td>
                                                <button class="btn btn-info btn-sm" data-toggle="modal"
                                                    data-target="#supplier_detail" data-id="1"
                                                    onclick="supplier_detail(1);"><i class="fa fa-search"></i></button>
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('supplier') }}/{{ $obj->id }}/edit" value="1"><i
                                                        class="fa fa-pencil"></i></a>
                                            </td>
                                            <td>{{ $obj->display_number }}</td>
                                            <td>{{ $obj->company_number }}</td>
                                            <td>{{ $obj->short_name }}</td>
                                            <td>{{ $obj->name }}</td>
                                            <td>{{-- $obj->number->pay_condition_id 需要left id --}}</td>
                                            <td>{{ $obj->telephone }}</td>
                                            <td>{{ $obj->address }}</td>
                                            <td>{{ $obj->remark }}</td>
                                            <td>{{ $obj->active }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
