@extends('Backend.master')
@section('title', '分類階層內容管理')
@section('content')
    <!--列表-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-bank"></i>分類階層內容管理</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <form role="form" id="select-form" method="GET" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-2">
                                        <h5>分類名稱</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="company_number" id="company_number" value="">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="col-sm-3">
                                        <h5>狀態</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="status" id="status">
                                            <option value=''>無</option>
                                            <option value='on'>開啟</option>
                                            <option value='off'>關閉</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 text-right">
                                    <div class="col-sm-12"><button class="btn btn-warning"><i
                                                class="fa fa-search  "></i>
                                            查詢</button></div>
                                </div>
                            </div>

                        </form>
                    </div>

                    <!-- Table list -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-2">
                                <a class="btn btn-block btn-warning btn-sm"
                                    href="{{ route('web_category_products.create') }}"><i class="fa fa-plus"></i> 創建</a>
                            </div>
                        </div>
                        <hr>
                        <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                            <thead>
                                <tr>
                                    <th>功能</th>
                                    <th>分類ID</th>
                                    <th>分類名稱</th>
                                    <th>狀態</th>
                                    <th>內容類型</th>
                                </tr>
                            </thead>
                            <tbody>
                                <button style="display:none;" class="btn btn-info btn-sm toggle-show-model"
                                    data-toggle="modal" data-target="#row_detail">SHOW
                                </button>
                                {{-- {{$category_products_list}} --}}
                                @foreach ($category_products_list as $key => $val)
                                    <tr>
                                        <form id="del" method="post">
                                            @method('DELETE')
                                            @csrf
                                        </form>
                                        <td>
                                            {{-- @if ($share_role_auth['auth_query']) --}}
                                            <button class="btn btn-info btn-sm"><i class="fa fa-search"></i></button>
                                            {{-- @endif --}}

                                            {{-- @if ($share_role_auth['auth_update'] && $v['status_code'] == 'DRAFTED' && $v['created_by'] == $data['user_id']) --}}
                                            <a class="btn btn-info btn-sm" href="{{route('web_category_products.edit',$val->id)}}">修改</a>
                                            {{-- @endif --}}

                                            {{-- @if ($share_role_auth['auth_delete'] && $v['status_code'] == 'DRAFTED' && $v['created_by'] == $data['user_id']) --}}
                                            <button class="btn btn-danger btn-sm" type="button">刪除</button>
                                            {{-- @endif --}}
                                        </td>
                                        <td>{{$val->id}}</td>
                                        <td>{{$val->name}}</td>
                                        <td>{{$val->active}}</td>
                                        <td>{{$val->content_type}}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="web_category_products" v-cloak>

        </div>
    </div>
@endsection
@section('js')
    <script>
        var products = Vue.extend({
            data: function() {
                return {}
            },
            methods: {},
            mounted: function() {

                $("#status").select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: "請選擇"
                });

            },
            computed: {},

        });

        new products().$mount('#web_category_products');
    </script>
@endsection
