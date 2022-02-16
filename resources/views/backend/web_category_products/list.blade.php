@extends('backend.master')
@section('title', '分類階層內容管理')
@section('content')
    <!--列表-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i>分類階層內容管理</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <!-- 功能按鈕(新增) -->
                    <div class="panel-heading">
                        <form role="form" id="select-form" method="GET" action="{{ route('web_category_products') }}"
                            enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-2">
                                        <h5>分類名稱</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="keyword" id="keyword"
                                            value="{{ request()->input('keyword') }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="col-sm-3">
                                        <h5>狀態</h5>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control js-select2" name="active" id="active">
                                            <option value=''>無</option>
                                            <option value='1'
                                                {{ request()->input('active') == '1' ? 'selected="selected"' : '' }}>
                                                開啟</option>
                                            <option value='0'
                                                {{ request()->input('active') == '0' ? 'selected="selected"' : '' }}>
                                                關閉</option>
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

                        <table class="table table-striped table-bordered table-hover" style="width:100%" id="table_list">
                            <thead>
                                <tr>
                                    <th>功能</th>
                                    <th>分類ID</th>
                                    <th>分類名稱</th>
                                    <th>狀態</th>
                                    <th>內容類型</th>
                                    <th>商品數</th>
                                </tr>
                            </thead>
                            <tbody>

                                {{-- {{$category_products_list}} --}}
                                @foreach ($category_hierarchy_content as $key => $val)
                                    <tr>
                                        <form id="del" method="post">
                                            @method('DELETE')
                                            @csrf
                                        </form>
                                        <td>
                                            {{-- @if ($share_role_auth['auth_query']) --}}
                                            <button class="btn btn-info btn-sm toggle-show-model" data-toggle="modal"
                                                data-target="#row_detail" @click="show({{ $val->id }})"><i
                                                    class="fa fa-search"></i>
                                            </button>
                                            {{-- @endif --}}
                                            {{-- @if ($share_role_auth['auth_update'] && $v['status_code'] == 'DRAFTED' && $v['created_by'] == $data['user_id']) --}}
                                            <a class="btn btn-info btn-sm"
                                                href="{{ route('web_category_products.edit', $val->id) }}">修改</a>
                                            {{-- @endif --}}

                                            {{-- @if ($share_role_auth['auth_delete'] && $v['status_code'] == 'DRAFTED' && $v['created_by'] == $data['user_id']) --}}
                                            {{-- <button class="btn btn-danger btn-sm" type="button">刪除</button> --}}
                                            {{-- @endif --}}
                                        </td>
                                        <td>{{ $val->id }}</td>
                                        <td>{{ $val->name }}</td>
                                        <td>
                                            @if ($val->active == 0)
                                                關閉
                                            @else
                                                開啟
                                            @endif
                                        </td>
                                        <td>
                                            @if ($val->content_type == 'M')
                                                指定賣場
                                            @elseif($val->content_type == 'P')
                                                指定商品
                                            @endif
                                        </td>
                                        <td>{{ $val->product_counts }}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @include('backend.web_category_products.list_detail')
    </div>
@endsection
@section('js')
    <script>
        var products = Vue.extend({
            data: function() {
                return {
                    category_products_list: [],
                    category_hierarchy_content: {
                        active: 0,
                        content_type: '',
                        id: '',
                        meta_description: '',
                        meta_keywords: '',
                        meta_title: '',
                        name: '',
                    },
                }
            },
            methods: {
                show(id) {
                    var req = async () => {
                        const response = await axios.post('/backend/web_category_products/ajax', {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            type: 'show_category_products',
                            id: id,
                        });
                        console.log(id);
                        this.category_products_list = response.data.result.data.category_products_list;
                        this.category_hierarchy_content = response.data.result.data
                            .category_hierarchy_content;

                        console.log(response.data.result.data.category_hierarchy_content);
                    }
                    req();
                    // console.log(this.category_products_list) ;
                    // console.log(this.category_hierarchy_content) ;

                },
            },
            mounted: function() {
                $("#active").select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: "請選擇"
                });

            },
            computed: {},

        });

        new products().$mount('#page-wrapper');
    </script>
@endsection
