@extends('backend.master')
@section('title', '分類階層管理')
@section('content')
    <style>
        h4 {
            font-weight: bold;
        }

        h4 .title_color {
            color: darkturquoise;
        }

        .ondragover {
            background: #b7e0fb !important;
            transition: background-color 0.5s;
            /* background: #ce1f59 !important; */
        }
        .elements-box >tr > td > *{
            pointer-events: none;
        }
    </style>
    <!--列表-->
    <div id="page-wrapper">
        <!-- 表頭名稱 -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa-solid fa-building-columns"></i>分類階層管理</h1>
            </div>
        </div>
        <div class="row" id="web_category_hierarchy" v-cloak>
            {{-- <button type="button" @click="test()">TEST BTN</button> --}}
            <div>
                <div class="panel panel-default container-fluid">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4 style="font-weight:bold;">大分類</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <a class="btn btn-block btn-warning btn-sm" data-toggle="modal"
                                            v-show="RoleAuthJson.auth_create" data-target="#addCategory"
                                            @click="CategoryModelShow('1','add')">新增大類</a>
                                    </div>
                                    <div class="col-sm-3">
                                        <a class="btn btn-block btn-success btn-sm" v-show="RoleAuthJson.auth_update"
                                            @click="SaveSort('1')">儲存</a>
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
                                    <tbody>
                                        <tr v-for="(level_1_obj, level_1_key) in category_level_1 " @dragstart="drag"
                                            @dragover='dragover' @dragleave='dragleave' @drop="drop" draggable="true"
                                            :data-index="level_1_key" :data-level="'1'" >
                                            <td style="vertical-align:middle">
                                                <i class="fa-solid fa-list"></i>
                                                @{{ level_1_obj . category_name }}
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <button type="button" class="btn btn-primary"
                                                            @click="GetCategory(level_1_obj,'1')">展中類</button>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <button type="button" class="btn btn-warning" data-toggle="modal"
                                                            data-target="#addCategory" v-show="RoleAuthJson.auth_update"
                                                            @click="CategoryModelShow('1','edit',level_1_obj)">編輯</button>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <button type="button" class="btn btn-danger"
                                                            @click="DelCategory(level_1_obj.id)"
                                                            v-show="RoleAuthJson.auth_delete">刪除</button>
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
                                            <h4> 【<span class="title_color">@{{ category_level_2_title }}</span>】的中分類
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <button class="btn btn-block btn-warning btn-sm" v-show="RoleAuthJson.auth_create"
                                            data-toggle="modal" data-target="#addCategory"
                                            @click="CategoryModelShow('2','add')"
                                            :disabled="disabled.disabled_level_2 == 1">新增中類</button>
                                    </div>
                                    <div class="col-sm-3">
                                        <a class="btn btn-block btn-success btn-sm" v-show="RoleAuthJson.auth_update"
                                            @click="SaveSort('2')" :disabled="disabled.disabled_level_2 == 1">儲存</a>
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
                                    <tbody>
                                        <tr v-for="(level_2_obj, level_2_key) in category_level_2" @dragstart="drag"
                                            @dragover='dragover' @dragleave='dragleave' @drop="drop"
                                            :data-index="level_2_key" :data-level="'2'">
                                            <td style="vertical-align:middle" :data-index="level_2_key" :data-level="'2'" draggable="true">
                                                <i class="fa-solid fa-list"></i>
                                                @{{ level_2_obj . category_name }}
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-sm-5" v-show="UecConfig.web_category_hierarchy_levels == '3' ">
                                                        <button type="button" class="btn btn-primary"
                                                            @click="GetCategory(level_2_obj,'2')">展小類</button>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="button" class="btn btn-warning" data-toggle="modal"
                                                            data-target="#addCategory"
                                                            v-show="RoleAuthJson.auth_update"
                                                            @click="CategoryModelShow('2','edit',level_2_obj)">編輯
                                                        </button>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="button" class="btn btn-danger" v-show="RoleAuthJson.auth_delete"
                                                            @click="DelCategory(level_2_obj.id)">刪除</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-4" v-show="UecConfig.web_category_hierarchy_levels == '3' ">
                                <div class="row">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <h4> 【<span class="title_color">@{{ category_level_3_title }}</span>】的小分類
                                            </h4>
                                            <div v-if="category_level_3_title">
                                            </div>
                                            <div v-else>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <a class="btn btn-block btn-warning btn-sm" v-show="RoleAuthJson.auth_create"
                                            data-toggle="modal" data-target="#addCategory"
                                            @click="CategoryModelShow('3','add')"
                                            :disabled="disabled.disabled_level_3 == 1">新增小類</a>
                                    </div>
                                    <div class="col-sm-3">
                                        <a class="btn btn-block btn-success btn-sm" v-show="RoleAuthJson.auth_update"
                                            :disabled="disabled.disabled_level_3 == 1" @click="SaveSort('2')">儲存</a>
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
                                    <tbody>
                                        <tr v-for="(level_3_obj, level_3_key) in category_level_3" @dragstart="drag"
                                            @dragover='dragover' @dragleave='dragleave' @drop="drop" draggable="true"
                                            :data-index="level_3_key" :data-level="'3'">
                                            <td style="vertical-align:middle">
                                                <i class="fa-solid fa-list"></i>
                                                @{{ level_3_obj . category_name }}
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <button type="button" class="btn btn-warning" data-toggle="modal"
                                                            data-target="#addCategory"
                                                            v-show="RoleAuthJson.auth_update"
                                                            @click="CategoryModelShow('3','edit',level_3_obj)">編輯</button>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="button" class="btn btn-danger"
                                                        v-show="RoleAuthJson.auth_delete"
                                                            @click="DelCategory(level_3_obj.id)">刪除</button>
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
            @include('backend.web_category_hierarchy.input_model_category')
        </div>
    </div>
@endsection
@section('js')
    <script>
        var requisitions = Vue.extend({
            data: function() {
                return {
                    RoleAuthJson: RoleAuthJson, //腳色權限
                    UecConfig:UecConfig,//後臺設定
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
                        id: '',
                        show_title: '',
                        category_level: '',
                        parent_id: '',
                        category_name: '',
                        old_category_name: '',
                        content_type:'M',
                        act: ''
                    },
                    disabled: {
                        disabled_level_2: 1,
                        disabled_level_3: 1,
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
                GetCategory(obj, category_level) { //取得子分類
                    var dataFunction = this;
                    var req = async () => {
                        const response = await axios.post('/backend/web_category_hierarchy/ajax', {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            type: 'getRequisitionsPurchase',
                            id: obj.id,
                            category_level: category_level,
                            type: 'GetCategory',
                        });
                        switch (category_level) {
                            case '1':
                                this.category_level_3_title = '';
                                this.category_level_3 = [];
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
                DelCategory(id) {
                    var DelAjax = async () => {
                        const response = await axios.post('/backend/web_category_hierarchy/ajax', {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            type: 'DelCategory',
                            id: id,
                        });
                        if (response.data.result.Msg_Hierarchy !== '') {
                            alert(response.data.result.Msg_Hierarchy);
                        }
                        if (response.data.result.Msg_Products !== '') {
                            alert(response.data.result.Msg_Products);
                        }
                        if (response.data.result.status) {
                            alert(response.data.result.Msg);
                            history.go(0);
                        }
                        console.log(response);
                        // response.data.result.Msg_Hierarchy =
                        // alert(response.data.result.Msg) ;
                        // console.log() ;
                    }
                    var Sure = confirm('你確定要刪除該分類嗎?');
                    if (Sure) {
                        DelAjax();
                    }
                    // console.log(id) ;
                },
                CategoryModelShow(level, act, obj) {
                    // empty data
                    this.msg.receiver_name = '';
                    this.addCategory.category_name = '';
                    this.addCategory.id = '';
                    this.addCategory.category_level = level;
                    this.addCategory.act = act;

                    // 關閉驗證
                    $('#receiver_name').closest(".form-group").removeClass("has-error").find('span').hide();

                    if (act == 'edit') {
                        this.addCategory.old_category_name = obj.category_name;
                        this.addCategory.id = obj.id;
                    }
                    switch (level) {
                        case '1':
                            this.addCategory.category_level = level;
                            this.addCategory.show_title = '大分類';
                            this.addCategory.parent_id = null;
                            break;
                        case '2':
                            this.addCategory.category_level = level;
                            this.addCategory.show_title = this.category_level_2_title + '的中分類';
                            this.addCategory.parent_id = this.category_level_1_obj.id;
                            break;
                        case '3':
                            // addCategory.show_title = '小分類' ;
                            this.addCategory.show_title = this.category_level_2_title + ' > ' + this
                                .category_level_3_title + '的小分類';
                            this.addCategory.parent_id = this.category_level_2_obj.id;
                            break;
                    }
                },
                CategoryToList() { //新增編輯
                    var checkstatus = true;
                    var type = '';

                    // 提交給驗證器驗證
                    $('#productModal').submit();

                    if (this.addCategory.category_name == '') {
                        checkstatus = false;
                        // this.msg.receiver_name = '不能為空喔';
                    }

                    if (this.addCategory.act == 'add') {
                        type = 'AddCategory';
                    } else {
                        type = 'EditCategory';
                    }
                    // console.log('TEST');
                    var PostAjax = async () => {
                        const response = await axios.post('/backend/web_category_hierarchy/ajax', {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            type: type,
                            id: this.addCategory.id,
                            category_level: this.addCategory.category_level,
                            parent_id: this.addCategory.parent_id,
                            category_name: this.addCategory.category_name,
                            content_type:this.addCategory.category_level == this.UecConfig.web_category_hierarchy_levels ? this.addCategory.content_type : '' ,
                        });
                        switch (this.addCategory.category_level) {
                            case '1':
                                this.category_level_1 = response.data.result;
                                if (this.addCategory.act == 'edit') {
                                    this.category_level_2 = {};
                                    this.category_level_2_title = '';
                                    this.category_level_3 = {};
                                    this.category_level_3_title = '';
                                }
                                break;
                            case '2':
                                this.category_level_2 = response.data.result;
                                if (this.addCategory.act == 'edit') {
                                    this.category_level_3 = {};
                                    this.category_level_3_title = '';
                                }
                                break;
                            case '3':
                                this.category_level_3 = response.data.result;
                                break;
                            default:
                                break;
                        }
                        if(type == 'EditCategory'){
                            var act_msg = '編輯';
                        }else{
                            var act_msg = '新增';
                        }
                        if(response.status == 200){
                            alert(act_msg+'成功')
                        }else{
                            alert(act_msg +'失敗')
                        }
                    }
                    if (checkstatus) {
                        PostAjax();
                        $('.hidden-model').click();
                    }

                },
                drag(eve) {
                    eve.dataTransfer.setData("text/index", eve.target.dataset.index);
                    eve.dataTransfer.setData("text/level", eve.target.dataset.level);
                    $('tbody').addClass('elements-box')
                },
                dragover(eve) {
                    eve.preventDefault();
                    eve.target.parentNode.classList.add('ondragover') ;

                },
                dragleave(eve) {
                    eve.preventDefault();
                    eve.target.parentNode.classList.remove('ondragover');
                },
                drop(eve) {
                    eve.target.parentNode.classList.remove('ondragover') ;
                    $('tbody').removeClass('elements-box')
                    eve.target.parentNode.parentNode.classList.remove('elements-box') ;
                    var index = eve.dataTransfer.getData("text/index");
                    var level = eve.dataTransfer.getData("text/level");
                    let targetIndex = eve.target.parentNode.dataset.index;
                    let targetlevel = eve.target.parentNode.dataset.level;
                    if (targetlevel !== level) {
                        alert('不能跨分類喔!');
                    } else {
                        switch (level) {
                            case '1':
                                var item = this.category_level_1[index];
                                this.category_level_1.splice(index, 1)
                                this.category_level_1.splice(targetIndex, 0, item)
                                break;
                            case '2':
                                var item = this.category_level_2[index];
                                this.category_level_2.splice(index, 1)
                                this.category_level_2.splice(targetIndex, 0, item)
                                break;
                            case '3':
                                var item = this.category_level_3[index];
                                this.category_level_3.splice(index, 1)
                                this.category_level_3.splice(targetIndex, 0, item)
                                break;
                            default:
                                break;
                        }
                    }

                },
                SaveSort(level) {
                    switch (level) {
                        case '1':
                            var InData = this.category_level_1;
                            break;
                        case '2':
                            var InData = this.category_level_2;
                            break;
                        case '3':
                            var InData = this.category_level_3;
                            break;
                        default:
                            break;
                    }
                    if (InData.length > 0) {
                        let SeveSortAjax = async () => {
                            const response = await axios.post('/backend/web_category_hierarchy/ajax', {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                type: 'SortCategory',
                                JsonData: JSON.stringify(InData),
                            });
                            if(response.status == 200){
                                alert('儲存排序成功');
                            }else{
                                alert('儲存排序失敗');
                            }
                        }

                        SeveSortAjax();
                    }
                }

            },
            mounted: function() {
                // 驗證表單
                $("#productModal").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        // $('#btn-save').prop('disabled', true);
                        // form.submit();
                    },
                    rules: {
                        receiver_name: {
                            required: true,
                        },
                    },
                    errorClass: "help-block",
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        if (element.parent('.input-group').length) {
                            error.insertAfter(element.parent());
                            return;
                        }

                        if (element.closest(".form-group").length) {
                            element.closest(".form-group").append(error);
                            return;
                        }

                        error.insertAfter(element);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).closest(".form-group").addClass("has-error");
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).closest(".form-group").removeClass("has-error");
                    },
                    success: function(label, element) {
                        $(element).closest(".form-group").removeClass("has-error");
                    },
                });
            },
            watch: {
                //監聽是否要將新增儲存開放點擊
                category_level_2_title: function() {
                    if (this.category_level_2_title !== '') {
                        return this.disabled.disabled_level_2 = 0;
                    } else {
                        return this.disabled.disabled_level_2 = 1;
                    }
                },
                category_level_3_title: function() {
                    if (this.category_level_3_title !== '') {
                        return this.disabled.disabled_level_3 = 0;
                    } else {
                        return this.disabled.disabled_level_3 = 1;
                    }
                },
            },
            computed: {},
        });

        new requisitions().$mount('#web_category_hierarchy');
    </script>
@endsection
