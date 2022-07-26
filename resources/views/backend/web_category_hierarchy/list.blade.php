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
                                    <h4>@{{ level[1].title }}</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-block btn-warning btn-sm" v-show="auth.create"
                                        @click="showCreateModal(1)">
                                        新增大類
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-block btn-success btn-sm" v-show="auth.update"
                                        @click="SaveSort('1')">
                                        儲存
                                    </button>
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
                                    <tr
                                        v-for="(category, index) in level[1].categories"
                                        :key="index"
                                        @dragstart="drag"
                                        @dragover='dragover'
                                        @dragleave='dragleave'
                                        @drop="drop"
                                        draggable="true"
                                        :data-index="index"
                                        :data-level="'1'"
                                    >
                                        <td style="vertical-align:middle">
                                            <i class="fa-solid fa-list"></i>
                                            @{{ category.categoryName }}
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <button type="button" class="btn btn-primary"
                                                        @click="getCategories(category)">
                                                        展中類
                                                    </button>
                                                </div>
                                                <div class="col-sm-4">
                                                    <button type="button" class="btn btn-warning" v-show="auth.update"
                                                        @click="showEditModal(category)">
                                                        編輯
                                                    </button>
                                                </div>
                                                <div class="col-sm-4">
                                                    <button type="button" class="btn btn-danger"
                                                        @click="deleteCategory(category)"
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
                        <div class="col-sm-4" v-show="showLevel > 1">
                            <div class="row">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4>【<span class="text-primary">@{{ level[2].title }}</span>】的中分類</h4>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-block btn-warning btn-sm" v-show="auth.create"
                                        @click="showCreateModal(2)">
                                        新增中類
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-block btn-success btn-sm" v-show="auth.update"
                                        @click="SaveSort('2')">
                                        儲存
                                    </button>
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
                                    <tr
                                        v-for="(category, index) in level[2].categories"
                                        :key="index"
                                        @dragstart="drag"
                                        @dragover='dragover'
                                        @dragleave='dragleave'
                                        @drop="drop"
                                        :data-index="index"
                                        :data-level="'2'"
                                    >
                                        <td style="vertical-align:middle"
                                            :data-index="index"
                                            :data-level="'2'"
                                            draggable="true"
                                        >
                                            <i class="fa-solid fa-list"></i>
                                            @{{ category.categoryName }}
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-5"
                                                    v-show="maxLevel > 2">
                                                    <button type="button" class="btn btn-primary"
                                                        @click="getCategories(category)">
                                                        展小類
                                                    </button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-warning" v-show="auth.update"
                                                        @click="showEditModal(category)">
                                                        編輯
                                                    </button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-danger"
                                                        v-show="auth.delete"
                                                        @click="deleteCategory(category)">
                                                        刪除
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-4" v-show="showLevel > 2">
                            <div class="row">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4>【<span class="text-primary">@{{ level[3].title }}</span>】的小分類</h4>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-block btn-warning btn-sm" v-show="auth.create"
                                        @click="showCreateModal(3)">
                                        新增小類
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-block btn-success btn-sm" v-show="auth.update"
                                        @click="SaveSort('2')">
                                        儲存
                                    </button>
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
                                    <tr
                                        v-for="(category, index) in level[3].categories"
                                        :key="index"
                                        @dragstart="drag"
                                        @dragover='dragover'
                                        @dragleave='dragleave'
                                        @drop="drop"
                                        draggable="true"
                                        :data-index="index"
                                        :data-level="'3'"
                                    >
                                        <td style="vertical-align:middle">
                                            <i class="fa-solid fa-list"></i>
                                            @{{ category.categoryName }}
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-warning"
                                                        v-show="auth.update"
                                                        @click="showEditModal(category)">
                                                        編輯
                                                    </button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-danger"
                                                        v-show="auth.delete"
                                                        @click="deleteCategory(category)">
                                                        刪除
                                                    </button>
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
        @include('backend.web_category_hierarchy.modal.category-form')
    </div>
@endsection
@section('js')
    <script>
        const DEFAULT_IMAGE_URL = '/asset/img/default_item.png';
        const BASE_URI = '/backend/web_category_hierarchy';
        const ICON_MIME = 'image/jpeg,image/png';

        let vm = new Vue({
            el: "#app",
            data: {
                maxLevel: 1,
                showLevel: 1,
                auth: {},
                level: {
                    "1": {
                        categories: [],
                        title: "大分類",
                        parentId: null,
                    },
                    "2": {
                        categories: [],
                        title: "",
                        parentId: null,
                    },
                    "3": {
                        categories: [],
                        title: "",
                        parentId: null,
                    },
                },
                modal: {
                    categoryForm: {
                        id: "category-form-modal",
                        title: "",
                        mode: "",
                        levelTitle: "",
                        categoryLevel: "",
                        originalCategoryName: "",
                        categoryName: "",
                        grossMarginThreshold: "",
                        originalCategoryShortName: "",
                        categoryShortName: "",
                        parentId: null,
                        categoryId: "",
                        icon: {
                            url: "",
                            width: "",
                            height: "",
                            showInputFile: true,
                            showDeleteButton: false,
                        },
                    },
                },
                categoryFormValidator: {},
            },
            created() {
                this.ICON_MIME = ICON_MIME;
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

                if (!_.isEmpty(payload.categories)) {
                    payload.categories.forEach(category => {
                        this.level[1].categories.push({
                            id: category.id,
                            categoryName: category.category_name,
                            categoryLevel: category.category_level,
                            grossMarginThreshold: category.gross_margin_threshold,
                            categoryShortName: category.category_short_name,
                            iconName: category.icon_name,
                        });
                    });
                }
            },
            methods: {
                getCategories(category) {
                    let childLevel = category.categoryLevel + 1;

                    axios({
                        method: "get",
                        url: `${BASE_URI}/categories`,
                        params: {
                            parent_id: category.id,
                        },
                    })
                    .then((response) => {
                        let payload = response.data.payload;

                        this.level[childLevel].categories = [];
                        if (!_.isEmpty(payload.categories)) {
                            payload.categories.forEach(category => {
                                this.level[childLevel].categories.push({
                                    id: category.id,
                                    categoryName: category.category_name,
                                    categoryLevel: category.category_level,
                                    grossMarginThreshold: category.gross_margin_threshold,
                                    categoryShortName: category.category_short_name,
                                    iconName: category.icon_name,
                                });
                            });
                        }

                        this.level[childLevel].title = payload.title;
                        this.level[childLevel].parentId = category.id;
                        this.showLevel = childLevel;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                },
                showCreateModal(level) {
                    this.modal.categoryForm.title = "新增分類";
                    this.modal.categoryForm.mode = "create";
                    this.modal.categoryForm.categoryLevel = level;
                    this.modal.categoryForm.categoryName = "";
                    this.modal.categoryForm.grossMarginThreshold = "";
                    this.modal.categoryForm.categoryShortName = "";
                    this.modal.categoryForm.icon.url = DEFAULT_IMAGE_URL;
                    this.modal.categoryForm.icon.showInputFile = true;
                    this.modal.categoryForm.icon.showDeleteButton = false;
                    this.$refs.icon.value = "";
                    this.resetCategoryFormValidator();

                    this.modal.categoryForm.levelTitle = this.level[level].title;
                    this.modal.categoryForm.parentId = this.level[level].parentId;

                    $(`#${this.modal.categoryForm.id}`).modal('show');
                },
                showEditModal(category) {
                    this.modal.categoryForm.title = "編輯分類";
                    this.modal.categoryForm.mode = "edit";
                    this.modal.categoryForm.categoryLevel = category.categoryLevel;
                    this.modal.categoryForm.originalCategoryName = category.categoryName;
                    this.modal.categoryForm.categoryName = "";
                    this.modal.categoryForm.grossMarginThreshold = category.grossMarginThreshold;
                    this.modal.categoryForm.originalCategoryShortName = category.categoryShortName;
                    this.modal.categoryForm.categoryShortName = "";
                    this.modal.categoryForm.categoryId = category.id;

                    if (category.iconName) {
                        this.modal.categoryForm.icon.url = category.iconName;
                        this.modal.categoryForm.icon.showInputFile = false;
                        this.modal.categoryForm.icon.showDeleteButton = true;
                    } else {
                        this.modal.categoryForm.icon.url = DEFAULT_IMAGE_URL;
                        this.modal.categoryForm.icon.showInputFile = true;
                        this.modal.categoryForm.icon.showDeleteButton = false;
                    }

                    this.$refs.icon.value = "";
                    this.resetCategoryFormValidator();

                    this.modal.categoryForm.levelTitle = this.level[category.categoryLevel].title;
                    this.modal.categoryForm.parentId = this.level[category.categoryLevel].parentId;

                    $(`#${this.modal.categoryForm.id}`).modal('show');
                },
                onIconChange(event) {
                    this.modal.categoryForm.icon.url = "";
                    this.modal.categoryForm.icon.width = "";
                    this.modal.categoryForm.icon.height = "";

                    const file = event.target.files[0];

                    if (!file || file.type.indexOf('image/') !== 0) {
                        this.modal.categoryForm.icon.showDeleteButton = false;
                        return;
                    }

                    let reader = new FileReader();

                    reader.readAsDataURL(file);
                    reader.onload = (event) => {
                        let img = new Image();
                        img.onload = () => {
                            this.modal.categoryForm.icon.width = img.width;
                            this.modal.categoryForm.icon.height = img.height;
                        }
                        img.src = event.target.result;
                        this.modal.categoryForm.icon.url = event.target.result;
                        this.modal.categoryForm.icon.showDeleteButton = true;
                    }

                    reader.onerror = (event) => {
                        console.error(event);
                    }
                },
                deleteIcon() {
                    if (confirm('確定要刪除嗎?')) {
                        this.modal.categoryForm.icon.url = DEFAULT_IMAGE_URL;
                        this.modal.categoryForm.icon.showInputFile = true;
                        this.modal.categoryForm.icon.showDeleteButton = false;
                        this.$refs.icon.value = "";
                    };
                },
                resetCategoryFormValidator() {
                    this.categoryFormValidator.resetForm();
                    $("#category-form").find(".has-error").removeClass("has-error");
                },
                submitCategoryForm() {
                    $('#category-form').submit();
                },
                submitHandler() {
                    if (this.modal.categoryForm.mode == 'create') {
                        this.createCategory();
                    } else {
                        this.updateCategory();
                    }
                },
                createCategory() {
                    let formData = new FormData();
                    formData.append("category_name", this.modal.categoryForm.categoryName);
                    formData.append("category_level", this.modal.categoryForm.categoryLevel);
                    formData.append("parent_id", this.modal.categoryForm.parentId ? this.modal.categoryForm.parentId : '');

                    if (this.modal.categoryForm.categoryLevel < 2) {
                        formData.append("gross_margin_threshold", this.modal.categoryForm.grossMarginThreshold);
                        formData.append("category_short_name", this.modal.categoryForm.categoryShortName);
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
                        let category = response.data.payload;

                        this.level[this.modal.categoryForm.categoryLevel].categories.push({
                            id: category.id,
                            categoryName: category.category_name,
                            categoryLevel: category.category_level,
                            grossMarginThreshold: category.gross_margin_threshold,
                            categoryShortName: category.category_short_name,
                            iconName: category.icon_name,
                        });

                        alert('新增成功');
                    })
                    .catch((error) => {
                        if (error.response) {
                            let data = error.response.data;
                            alert('新增失敗');
                        }
                    })
                    .finally(() => {
                        $(`#${this.modal.categoryForm.id}`).modal('hide');
                    });
                },
                updateCategory() {
                    let formData = new FormData();
                    formData.append("_method", "put");
                    formData.append("category_name", this.modal.categoryForm.categoryName);

                    if (this.modal.categoryForm.categoryLevel < 2) {
                        formData.append("gross_margin_threshold", this.modal.categoryForm.grossMarginThreshold);
                        formData.append("category_short_name", this.modal.categoryForm.categoryShortName);
                        formData.append("icon_name", this.$refs.icon.files[0] ? this.$refs.icon.files[0] : '');
                        formData.append("isIconDeleted", !this.modal.categoryForm.icon.showDeleteButton);
                    }

                    axios({
                        method: "post",
                        url: `${BASE_URI}/${this.modal.categoryForm.categoryId}`,
                        data: formData,
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then((response) => {
                        let category = response.data.payload;

                        const index = this.level[this.modal.categoryForm.categoryLevel].categories.findIndex(originalCategory => originalCategory.id == category.id);
                        this.$set(this.level[this.modal.categoryForm.categoryLevel].categories, index, {
                            id: category.id,
                            categoryName: category.category_name,
                            categoryLevel: category.category_level,
                            grossMarginThreshold: category.gross_margin_threshold,
                            categoryShortName: category.category_short_name,
                            iconName: category.icon_name,
                        });

                        let childLevel = this.modal.categoryForm.categoryLevel + 1;
                        this.level[childLevel].title = category.category_name;

                        alert('更新成功');
                    })
                    .catch((error) => {
                        if (error.response) {
                            let data = error.response.data;
                            alert('更新失敗');
                        }
                    })
                    .finally(() => {
                        $(`#${this.modal.categoryForm.id}`).modal('hide');
                    });
                },
                deleteCategory(category) {
                    var DelAjax = async () => {
                        const response = await axios.post('/backend/web_category_hierarchy/ajax', {
                            type: 'DelCategory',
                            id: category.id,
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
                    }
                    var Sure = confirm('你確定要刪除該分類嗎?');
                    if (Sure) {
                        DelAjax();
                    }
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
                                var item = this.level[1].categories[index];
                                this.level[1].categories.splice(index, 1)
                                this.level[1].categories.splice(targetIndex, 0, item)
                                break;
                            case '2':
                                var item = this.level[2].categories[index];
                                this.level[2].categories.splice(index, 1)
                                this.level[2].categories.splice(targetIndex, 0, item)
                                break;
                            case '3':
                                var item = this.level[3].categories[index];
                                this.level[3].categories.splice(index, 1)
                                this.level[3].categories.splice(targetIndex, 0, item)
                                break;
                            default:
                                break;
                        }
                    }

                },
                SaveSort(level) {
                    switch (level) {
                        case '1':
                            var InData = this.level[1].categories;
                            break;
                        case '2':
                            var InData = this.level[2].categories;
                            break;
                        case '3':
                            var InData = this.level[3].categories;
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
                this.categoryFormValidator = $("#category-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        self.submitHandler();
                    },
                    rules: {
                        categoryName: {
                            required: {
                                depends: function (element) {
                                    return self.modal.categoryForm.mode == "create";
                                }
                            },
                        },
                        grossMarginThreshold: {
                            required: true,
                            max: 100,
                            min: 0,
                            step: 0.01,
                            number: true,
                        },
                        categoryShortName: {
                            required: {
                                depends: function (element) {
                                    return self.modal.categoryForm.mode == "create";
                                }
                            },
                        },
                        icon: {
                            accept: this.ICON_MIME,
                            filesize: [1, 'MB'],
                            minImageWidth: 96,
                            minImageHeight: 96,
                            imageRatio: [1, 1],
                        },
                    },
                    messages: {
                        icon: {
                            accept: "檔案類型錯誤",
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
        });
    </script>
@endsection
