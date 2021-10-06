@extends('Backend.master')

@section('title', '功能名稱')

@section('content')
    <!--新增-->
    <div id="page-wrapper">

        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-cube"></i> 物品管理</h1>
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
                            <div class="col-sm-2">
                                <a href="/item/create" class="btn btn-block btn-warning btn-sm" id="btn-new"><i
                                        class="fa fa-plus"></i> 新增物品</a>
                            </div>
                            <div class="col-sm-10 text-right">
                            </div>
                        </div>
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

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

