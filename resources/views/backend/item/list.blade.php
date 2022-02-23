@extends('backend.master')

@section('title', '物品管理')

@section('content')
    <!--新增-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-cube"></i> 物品管理</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">

                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="col-sm-2">
                                    <h5>品項分類：</h5>
                                </div>
                                <div class="col-sm-10">
                                    <select class="form-control js-select2" name="category" id="category">
                                        <option value="">插入品項</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-2">
                                    <h5>狀態：</h5>
                                </div>
                                <div class="col-sm-10">
                                    <select class="form-control" name="active" id="active">
                                        <option value="%">所有狀態</option>
                                        <option value="">狀態1</option>
                                        <option value="">狀態2</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="row">
                            @if($share_role_auth['auth_create'])
                                <div class="col-sm-2">
                                    <a href="{{ route('item') }}/create" class="btn btn-block btn-warning btn-sm"
                                       id="btn-new"><i class="fa-solid fa-plus"></i> 新增物品</a>
                                </div>
                            @endif
                        </div>
                        <hr>
                        <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                            <thead>
                                <tr>
                                    <th>功能</th>
                                    <th>編號</th>
                                    <th>供應商</th>
                                    <th>品牌</th>
                                    <th>品名</th>
                                    <th>規格</th>
                                    <th>上次進價</th>
                                    <th>售價</th>
                                    <th>當前庫存</th>
                                    <th>單位</th>
                                    <th>顯示</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($item as $val)
                                    <tr role="row">
                                        <td>
                                            <a href="{{ route('item') }}/{{ $val->id }}/edit"
                                                class="btn btn-block btn-info btn-sm"><i class="fa-solid fa-pencil"></i>
                                                編輯</a>
                                        </td>
                                        <td>{{ $val->number }}</td>
                                        <td>{{ $val->supplier_name }}</td>
                                        <td>{{ $val->brand }}</td>
                                        <td>{{ $val->name }}</td>
                                        <td>{{ $val->spec }}</td>
                                        <td>{{ $val->last_price }}</td>
                                        <td>{{ $val->sell_price1 }}</td>
                                        <td>{{ $val->stock_qty }}</td>
                                        <td>{{ $val->small_unit }}</td>
                                        <td>{{ $val->active }}</td>
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
