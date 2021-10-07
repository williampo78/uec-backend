@extends('Backend.master')

@section('title', '功能名稱')

@section('content')
    <!--新增-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-cubes"></i>供應商類別處理</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">

                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-2">
                                <a href="{{ route('supplier_type') }}/create" class="btn btn-block btn-warning btn-sm"
                                    id="btn-new"><i class="fa fa-plus"></i>
                                    新增</a>
                            </div>
                        </div>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div id="table_list_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                            <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                                <thead>
                                    <tr role="row">
                                        <th class="col-sm-1 ">功能</th>
                                        <th class="col-sm-1 ">編號</th>
                                        <th class="col-sm-10 ">名稱</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($SupplierTypeService as $val)
                                        <tr role="row">
                                            <td>
                                                <a href="{{ route('supplier_type') }}/{{ $val['id'] }}/edit"
                                                    class="btn btn-block btn-info btn-sm"><i class="fa fa-pencil"></i>
                                                    編輯</a>
                                            </td>
                                            <td>{{ $val['code'] }}</td>
                                            <td>{{ $val['name'] }}</td>
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
