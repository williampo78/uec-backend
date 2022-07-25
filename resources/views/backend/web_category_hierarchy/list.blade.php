@extends('backend.layouts.master')

@section('title', '分類階層管理')

@section('css')
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

        .elements-box>tr>td>* {
            pointer-events: none;
        }
    </style>
@endsection

@section('content')
    <div id="app" v-cloak>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header"><i class="fa-solid fa-building-columns"></i> 分類階層管理</h1>
                </div>
            </div>
            <div class="row">
                <div class="panel panel-default" style="padding: 1.5rem;">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 style="font-weight:bold;">大分類</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <a class="btn btn-block btn-warning btn-sm" v-show="auth.create"
                                        @click="showCreateModal(1)">
                                        新增大類
                                    </a>
                                </div>
                                <div class="col-sm-3">
                                    <a class="btn btn-block btn-success btn-sm" v-show="auth.update"
                                        @click="SaveSort('1')">
                                        儲存
                                    </a>
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
                                    <tr v-for="(category, index) in level._1.categories" :key="index" @dragstart="drag"
                                        @dragover='dragover' @dragleave='dragleave' @drop="drop" draggable="true"
                                        :data-index="index" :data-level="'1'">
                                        <td style="vertical-align:middle">
                                            <i class="fa-solid fa-list"></i>
                                            @{{ category.categoryName }}
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <button type="button" class="btn btn-primary"
                                                        @click="GetCategory(category, '1')">
                                                        展中類
                                                    </button>
                                                </div>
                                                <div class="col-sm-4">
                                                    <button type="button" class="btn btn-warning" data-toggle="modal"
                                                        data-target="#addCategory" v-show="auth.update"
                                                        @click="showCreateModal('1', 'edit', category)">
                                                        編輯
                                                    </button>
                                                </div>
                                                <div class="col-sm-4">
                                                    <button type="button" class="btn btn-danger"
                                                        @click="DelCategory(category.id)"
                                                        v-show="auth.delete">
                                                        刪除
                                                    </button>
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
                                    <button class="btn btn-block btn-warning btn-sm" v-show="auth.create"
                                        data-toggle="modal" data-target="#addCategory"
                                        @click="showCreateModal(2)"
                                        :disabled="disabled.disabled_level_2 == 1">新增中類</button>
                                </div>
                                <div class="col-sm-3">
                                    <a class="btn btn-block btn-success btn-sm" v-show="auth.update"
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
                                        <td style="vertical-align:middle" :data-index="level_2_key"
                                            :data-level="'2'" draggable="true">
                                            <i class="fa-solid fa-list"></i>
                                            @{{ level_2_obj.category_name }}
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-5"
                                                    v-show="maxLevel > 2">
                                                    <button type="button" class="btn btn-primary"
                                                        @click="GetCategory(level_2_obj,'2')">展小類</button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-warning" data-toggle="modal"
                                                        data-target="#addCategory" v-show="auth.update"
                                                        @click="showCreateModal('2','edit',level_2_obj)">編輯
                                                    </button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-danger"
                                                        v-show="auth.delete"
                                                        @click="DelCategory(level_2_obj.id)">刪除</button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-4" v-show="maxLevel > 2">
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
                                    <a class="btn btn-block btn-warning btn-sm" v-show="auth.create"
                                        data-toggle="modal" data-target="#addCategory"
                                        @click="showCreateModal(3)"
                                        :disabled="disabled.disabled_level_3 == 1">新增小類</a>
                                </div>
                                <div class="col-sm-3">
                                    <a class="btn btn-block btn-success btn-sm" v-show="auth.update"
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
                                            @{{ level_3_obj.category_name }}
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-warning"
                                                        data-toggle="modal" data-target="#addCategory"
                                                        v-show="auth.update"
                                                        @click="showCreateModal('3','edit',level_3_obj)">編輯</button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-danger"
                                                        v-show="auth.delete"
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
        {{-- @include('backend.web_category_hierarchy.input_model_category') --}}
        @include('backend.web_category_hierarchy.modal.create')
        @include('backend.web_category_hierarchy.modal.edit')
    </div>
@endsection
@section('js')
    <script>
        const DEFAULT_IMAGE_URL = '/asset/img/default_item.png';
        const BASE_URI = '/backend/web_category_hierarchy';

        let vm = new Vue({
            el: "#app",
            data: {
                imagesFile: null,
                fileUrl: @json(env('AWS_URL')),
                category_level_2: [],
                category_level_3: [],
                // view title
                category_level_2_title: '',
                category_level_3_title: '',
                // 點擊顯示的物件 讓子表去拿父表物件
                category_level_1_obj: [],
                category_level_2_obj: [],
                // 暫存的新增
                addCategory: {
                    id: '',
                    show_title: '',
                    category_level: '',
                    parent_id: null,
                    category_name: '',
                    old_category_name: '',
                    content_type: 'P',
                    act: '',
                    gross_margin_threshold: 0, //毛利門檻
                    category_short_name: '', //(漢堡)短名稱
                    icon_name: null, //(漢堡呈現)分類icon圖檔名稱
                },
                disabled: {
                    disabled_level_2: 1,
                    disabled_level_3: 1,
                },

                maxLevel: 1,
                auth: {},
                level: {
                    _1: {
                        categories: [],
                        title: "大分類",
                    },
                    _2: {
                        categories: [],
                        title: "",
                        parentId: null,
                    },
                    _3: {
                        categories: [],
                        title: "",
                        parentId: null,
                    },
                },
                modal: {
                    create: {
                        id: "create-modal",
                        title: "新增分類",
                        levelTitle: "",
                        categoryLevel: "",
                        categoryName: "",
                        grossMarginThreshold: "",
                        categoryShortName: "",
                        parentId: null,
                        icon: {
                            url: "",
                            width: "",
                            height: "",
                            showInputFile: true,
                            showDeleteButton: false,
                        },
                    },
                    edit: {
                        id: "edit-modal",
                        title: "編輯分類",
                        levelTitle: "",
                        categoryLevel: "",
                    },
                },
                createValidator: {},
            },
            created() {
                let payload = @json($payload);

                if (payload.max_level) {
                    this.maxLevel = payload.max_level;
                }

                if (payload.auth) {
                    this.auth = {
                        create: payload.auth.auth_create,
                        update: payload.auth.auth_update,
                        delete: payload.auth.auth_delete,
                    };
                }

                if (!_.isEmpty(payload.level_1_categories)) {
                    payload.level_1_categories.forEach(category => {
                        this.level._1.categories.push({
                            id: category.id,
                            categoryName: category.category_name,
                        });
                    });
                }
            },
            methods: {
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
                showCreateModal(level) {
                    this.modal.create.categoryLevel = level;
                    this.modal.create.categoryName = "";
                    this.modal.create.grossMarginThreshold = "";
                    this.modal.create.categoryShortName = "";
                    this.modal.create.icon.url = DEFAULT_IMAGE_URL;
                    this.modal.create.icon.width = "";
                    this.modal.create.icon.height = "";
                    this.modal.create.icon.showInputFile = true;
                    this.modal.create.icon.showDeleteButton = false;
                    this.$refs.icon.value = "";
                    // reset 驗證器
                    this.createValidator.resetForm();
                    $("#create-form").find(".has-error").removeClass("has-error");

                    switch (level) {
                        case 1:
                            this.modal.create.levelTitle = this.level._1.title;
                            this.modal.create.parentId = null;
                            break;

                        case 2:
                            this.modal.create.levelTitle = this.level._2.title;
                            // this.modal.create.parentId = this.level._2.parentId;
                            break;

                        case 3:
                            this.modal.create.levelTitle = this.level._3.title;
                            // this.modal.create.parentId = this.level._3.parentId;
                            break;
                    }

                    $(`#${this.modal.create.id}`).modal('show');
                },
                showEditModal() {

                },
                onIconChange(event) {
                    this.modal.create.icon.url = "";
                    this.modal.create.icon.width = "";
                    this.modal.create.icon.height = "";

                    const file = event.target.files[0];

                    if (!file || file.type.indexOf('image/') !== 0) {
                        this.modal.create.icon.showDeleteButton = false;
                        return;
                    }

                    let reader = new FileReader();

                    reader.readAsDataURL(file);
                    reader.onload = (event) => {
                        let img = new Image();
                        img.onload = () => {
                            this.modal.create.icon.width = img.width;
                            this.modal.create.icon.height = img.height;
                        }
                        img.src = event.target.result;
                        this.modal.create.icon.url = event.target.result;
                        this.modal.create.icon.showDeleteButton = true;
                    }

                    reader.onerror = (event) => {
                        console.error(event);
                    }
                    // this.getImage(showPhotoSrc, file, function(callback) {
                    //     let status = true;
                    //     if (callback.file.size > 1048576) {
                    //         status = false;
                    //         alert('照片名稱:' + callback.file.name + '已經超出大小');
                    //     }
                    //     if (callback.file.type !== 'image/jpeg' && callback.file.type !== 'image/png') {
                    //         status = false;
                    //         alert('照片名稱:' + file.name + '格式錯誤');
                    //     }
                    //     if (callback.width < 96 && callback.height < 96) {
                    //         status = false;
                    //         alert('照片名稱:' + callback.file.name + '照片尺寸必須為96*96以上');
                    //     }
                    //     if (callback.width !== callback.height) {
                    //         status = false;
                    //         alert('照片名稱:' + callback.file.name + '照片比例必須為1:1');
                    //     }
                    // });
                },
                deleteIcon() {
                    if (confirm('確定要刪除嗎?')) {
                        this.modal.create.icon.url = DEFAULT_IMAGE_URL;
                        this.modal.create.icon.showInputFile = true;
                        this.modal.create.icon.showDeleteButton = false;
                        this.$refs.icon.value = "";
                    };
                },
                submitCreateForm() {
                    $('#create-form').submit();
                },
                createCategory() {
                    let formData = new FormData();
                    formData.append("category_name", this.modal.create.categoryName);
                    formData.append("category_level", this.modal.create.categoryLevel);
                    formData.append("parent_id", this.modal.create.parentId ? this.modal.create.parentId : '');

                    if (this.modal.create.categoryLevel < 2) {
                        formData.append("gross_margin_threshold", this.modal.create.grossMarginThreshold);
                        formData.append("category_short_name", this.modal.create.categoryShortName);
                        formData.append("icon_name", this.$refs.icon.files[0] ? this.$refs.icon.files[0] : '');
                    }

                    axios({
                        method: "post",
                        url: `${BASE_URI}`,
                        data: formData,
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then((response) => {
                        let payload = response.data.payload;

                        switch (this.modal.create.categoryLevel) {
                            case 1:
                                this.level._1.categories.push({
                                    id: payload.id,
                                    categoryName: payload.category_name,
                                });
                                break;

                            case 2:
                                this.level._2.categories.push({
                                    id: payload.id,
                                    categoryName: payload.category_name,
                                });
                                break;

                            case 3:
                                this.level._3.categories.push({
                                    id: payload.id,
                                    categoryName: payload.category_name,
                                });
                                break;
                        }

                        alert('新增成功');
                    })
                    .catch((error) => {
                        if (error.response) {
                            let data = error.response.data;
                            alert('新增失敗');
                        }
                    })
                    .finally(() => {
                        $(`#${this.modal.create.id}`).modal('hide');
                    });
                },
                drag(eve) {
                    eve.dataTransfer.setData("text/index", eve.target.dataset.index);
                    eve.dataTransfer.setData("text/level", eve.target.dataset.level);
                    $('tbody').addClass('elements-box')
                },
                dragover(eve) {
                    eve.preventDefault();
                    eve.target.parentNode.classList.add('ondragover');

                },
                dragleave(eve) {
                    eve.preventDefault();
                    eve.target.parentNode.classList.remove('ondragover');
                },
                drop(eve) {
                    eve.target.parentNode.classList.remove('ondragover');
                    $('tbody').removeClass('elements-box')
                    eve.target.parentNode.parentNode.classList.remove('elements-box');
                    var index = eve.dataTransfer.getData("text/index");
                    var level = eve.dataTransfer.getData("text/level");
                    let targetIndex = eve.target.parentNode.dataset.index;
                    let targetlevel = eve.target.parentNode.dataset.level;
                    if (targetlevel !== level) {
                        alert('不能跨分類喔!');
                    } else {
                        switch (level) {
                            case '1':
                                var item = this.level._1.categories[index];
                                this.level._1.categories.splice(index, 1)
                                this.level._1.categories.splice(targetIndex, 0, item)
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
                            var InData = this.level._1.categories;
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
                            if (response.status == 200) {
                                alert('儲存排序成功');
                            } else {
                                alert('儲存排序失敗');
                            }
                        }

                        SeveSortAjax();
                    }
                },
            },
            mounted() {
                let self = this;

                // 驗證表單
                this.createValidator = $("#create-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        self.createCategory();
                    },
                    rules: {
                        categoryName: {
                            required: true,
                        },
                        grossMarginThreshold: {
                            required: true,
                        },
                        categoryShortName: {
                            required: true,
                        },
                    },
                    errorClass: "help-block",
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        if (element.closest(".input-group").length) {
                            element.closest(".input-group").parent().append(error);
                            return;
                        }

                        error.insertAfter(element);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).closest(".form-group").addClass("has-error");
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
        });
    </script>
@endsection
