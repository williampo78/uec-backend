@extends('Backend.master')
@section('title', '分類階層管理')
@section('content')
    <style>
        h4 {
            font-weight: bold;
        }

        h4 span {
            color: darkturquoise;
        }

    </style>
    <!--列表-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-bank"></i>分類階層管理</h1>
            </div>
        </div>
        <div class="row" id="web_category_hierarchy">
            <button type="button" @click="test()">TEST BTN</button>
            <div>
                <div class="panel panel-default container-fluid">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-4 ">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4 style="font-weight:bold;">大分類</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <a class="btn btn-block btn-warning btn-sm " data-toggle="modal"
                                            data-target="#addCategory" @click="addCategoryModelShow('1')">新增大類</a>
                                    </div>
                                    <div class="col-sm-3">
                                        <a class="btn btn-block btn-success btn-sm">儲存</a>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-4">名稱</th>
                                            <th class="col-sm-8">功能</th>
                                        </tr>
                                    </thead>
                                    <tbody v-for="(level_1_obj, level_1_key) in category_level_1">
                                        <tr>
                                            <td style="vertical-align:middle">
                                                <i class="fa fa-list"></i>
                                                @{{ level_1_obj . category_name }}
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <button type="button" class="btn btn-primary"
                                                            @click="GetCategory(level_1_obj,'1')">展中類</button>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <button type="button" class="btn btn-warning">編輯</button>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <button type="button" class="btn btn-danger">刪除</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-4">
                                <div class="row">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <h4> 【<span>@{{ category_level_2_title }}</span>】的中分類</h4>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <a class="btn btn-block btn-warning btn-sm" data-toggle="modal"
                                            data-target="#addCategory" @click="addCategoryModelShow('2')">新增中類</a>
                                    </div>
                                    <div class="col-sm-3">
                                        <a class="btn btn-block btn-success btn-sm" @click="">儲存</a>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-4">名稱</th>
                                            <th class="col-sm-8">功能</th>
                                        </tr>
                                    </thead>
                                    <tbody v-for="(level_2_obj, level_2_key) in category_level_2">
                                        <tr>
                                            <td style="vertical-align:middle">
                                                <i class="fa fa-list"></i>
                                                @{{ level_2_obj . category_name }}
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-sm-5">
                                                        <button type="button" class="btn btn-primary"
                                                            @click="GetCategory(level_2_obj,'2')">展開小類</button>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="button" class="btn btn-warning">編輯</button>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="button" class="btn btn-danger">刪除</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-4">
                                <div class="row">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <h4> 【<span>@{{ category_level_3_title }}</span>】的小分類</h4>
                                            <div v-if="category_level_3_title">
                                            </div>
                                            <div v-else>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <a class="btn btn-block btn-warning btn-sm" data-toggle="modal"
                                            data-target="#addCategory" @click="addCategoryModelShow('3')">新增小類</a>
                                    </div>
                                    <div class="col-sm-3">
                                        <a class="btn btn-block btn-success btn-sm">儲存</a>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-4">名稱</th>
                                            <th class="col-sm-8">功能</th>
                                        </tr>
                                    </thead>
                                    <tbody v-for="(level_3_obj, level_3_key) in category_level_3">
                                        <tr>
                                            <td style="vertical-align:middle">
                                                <i class="fa fa-list"></i>
                                                @{{ level_3_obj . category_name }}
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <button type="button" class="btn btn-warning">編輯</button>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="button" class="btn btn-danger">刪除</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('Backend.WebCategoryHierarchy.addcategory')
        </div>
    </div>
@endsection
@section('js')
    <script>
        var requisitions = Vue.extend({
            data: function() {
                return {
                    //list 
                    category_level_1: @json($category_level_1),
                    category_level_2: [],
                    category_level_3: [],
                    //view title 
                    category_level_2_title: '',
                    category_level_3_title: '',
                    //點擊顯示的物件 讓子表去拿父表物件 
                    category_level_1_obj: [],
                    category_level_2_obj: [],
                    //暫存的新增
                    addCategory: {
                        show_title: '',
                        category_level: '',
                        parent_id: '',
                        category_name: '',
                    },
                    //
                    msg: {
                        receiver_name: ''
                    },
                }
            },
            methods: {
                test() {
                    console.log(this.addCategory);
                },
                GetCategory(obj, level) { //取得子分類
                    var dataFunction = this;
                    var req = async () => {
                        const response = await axios.post('/backend/web_category_hierarchy/ajax', {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            type: 'getRequisitionsPurchase',
                            id: obj.id,
                            level: level,
                            type: 'GetCategory',
                        });
                        switch (level) {
                            case '1':
                                dataFunction.category_level_2 = response.data.result;
                                dataFunction.category_level_2_title = obj.category_name;
                                dataFunction.category_level_1_obj = obj;
                                break;
                            case '2':
                                dataFunction.category_level_3 = response.data.result;
                                dataFunction.category_level_3_title = obj.category_name;
                                dataFunction.category_level_2_obj = obj;
                                break;
                            default:
                                break;
                        }
                        // console.log(response.data);
                    }
                    req();
                },
                addCategoryModelShow(level) {
                    var addCategory = this.addCategory;
                    this.msg.receiver_name = ''; // empty error msg 
                    addCategory.category_name = '';
                    addCategory.category_level = level;
                    switch (level) {
                        case '1':
                            addCategory.category_level = level;
                            addCategory.show_title = '大分類';
                            break;
                        case '2':
                            addCategory.category_level = level;
                            addCategory.show_title = this.category_level_2_title + '的中分類';
                            addCategory.parent_id = this.category_level_1_obj.id;
                            break;
                        case '3':
                            // addCategory.show_title = '小分類' ;
                            addCategory.show_title = this.category_level_2_title + ' > ' + this
                                .category_level_3_title + '的小分類';
                            addCategory.parent_id = this.category_level_2_obj.id;
                            break;
                    }
                },
                addCategoryToList() {
                    var checkstatus = true;
                    if (this.addCategory.category_name == '') {
                        checkstatus = false;
                        this.msg.receiver_name = '不能為空喔';
                    }
                    if (checkstatus) {
                        switch (this.addCategory.category_level) {
                            case '1':
                                this.category_level_1.push({
                                    id: '',
                                    category_level: this.addCategory.category_level,
                                    parent_id: this.addCategory.parent_id,
                                    category_name: this.addCategory.category_name
                                });
                                break;
                            case '2':
                                this.category_level_2.push({
                                    id: '',
                                    category_level: this.addCategory.category_level,
                                    parent_id: this.addCategory.parent_id,
                                    category_name: this.addCategory.category_name
                                });
                                break;
                            case '3':
                                this.category_level_3.push({
                                    id: '',
                                    category_level: this.addCategory.category_level,
                                    parent_id: this.addCategory.parent_id,
                                    category_name: this.addCategory.category_name
                                });
                                break;
                            default:
                                break;
                        }

                        $('.hidden-model').click();
                    }
                }
            },
            mounted: function() {

            },
            computed: {

            },
        });

        new requisitions().$mount('#web_category_hierarchy');
    </script>
@endsection
