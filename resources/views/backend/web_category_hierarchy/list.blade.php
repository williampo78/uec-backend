@extends('backend.layouts.master')

@section('title', '分類階層管理')

@section('css')
    <style>
        h4 {
            font-weight: bold;
        }

        .function-container {
            display: flex;
            flex-direction: row;
            justify-content: space-around;
        }

        .table > tbody > tr > td {
            vertical-align: middle;
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
                                    <button
                                        type="button"
                                        class="btn btn-block btn-warning btn-sm"
                                        v-show="auth.create"
                                        @click="showCreateModal(1)"
                                    >
                                        新增大類
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                    <button
                                        type="button"
                                        class="btn btn-block btn-success btn-sm"
                                        v-show="auth.update"
                                        :disabled="isSaveSortButtonDisabled(1)"
                                        @click="updateSort(1)"
                                    >
                                        儲存
                                    </button>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="col-sm-5">名稱</th>
                                        <th class="col-sm-7">功能</th>
                                    </tr>
                                </thead>
                                <tbody
                                    is="draggable"
                                    tag="tbody"
                                    :list="level[1].categories"
                                    v-bind="dragOptions"
                                >
                                    <tr
                                        v-for="(category, index) in level[1].categories"
                                        :key="index"
                                    >
                                        <td>
                                            <i class="fa-solid fa-list fa-xl draggable-handle"></i>
                                            @{{ category.categoryName }}
                                        </td>
                                        <td>
                                            <div class="function-container">
                                                <button
                                                    type="button"
                                                    class="btn btn-primary"
                                                    @click="getCategories(category)"
                                                >
                                                    展中類
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-warning"
                                                    v-show="auth.update"
                                                    @click="showEditModal(category)"
                                                >
                                                    編輯
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-danger"
                                                    @click="deleteCategory(category, index)"
                                                    v-show="auth.delete"
                                                >
                                                    刪除
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-4" v-show="showLevel > 1">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4>【<span class="text-primary">@{{ level[2].title }}</span>】的中分類</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <button
                                        type="button"
                                        class="btn btn-block btn-warning btn-sm"
                                        v-show="auth.create"
                                        @click="showCreateModal(2)"
                                    >
                                        新增中類
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                    <button
                                        type="button"
                                        class="btn btn-block btn-success btn-sm"
                                        v-show="auth.update"
                                        :disabled="isSaveSortButtonDisabled(2)"
                                        @click="updateSort(2)"
                                    >
                                        儲存
                                    </button>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="col-sm-5">名稱</th>
                                        <th class="col-sm-7">功能</th>
                                    </tr>
                                </thead>
                                <tbody
                                    is="draggable"
                                    tag="tbody"
                                    :list="level[2].categories"
                                    v-bind="dragOptions"
                                >
                                    <tr
                                        v-for="(category, index) in level[2].categories"
                                        :key="index"
                                    >
                                        <td>
                                            <i class="fa-solid fa-list fa-xl draggable-handle"></i>
                                            @{{ category.categoryName }}
                                        </td>
                                        <td>
                                            <div class="function-container">
                                                <button
                                                    type="button"
                                                    class="btn btn-primary"
                                                    v-show="maxLevel > 2"
                                                    @click="getCategories(category)"
                                                >
                                                    展小類
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-warning"
                                                    v-show="auth.update"
                                                    @click="showEditModal(category)"
                                                >
                                                    編輯
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-danger"
                                                    v-show="auth.delete"
                                                    @click="deleteCategory(category, index)"
                                                >
                                                    刪除
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-4" v-show="showLevel > 2">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4>【<span class="text-primary">@{{ level[3].title }}</span>】的小分類</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <button
                                        type="button"
                                        class="btn btn-block btn-warning btn-sm"
                                        v-show="auth.create"
                                        @click="showCreateModal(3)"
                                    >
                                        新增小類
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                    <button
                                        type="button"
                                        class="btn btn-block btn-success btn-sm"
                                        v-show="auth.update"
                                        :disabled="isSaveSortButtonDisabled(3)"
                                        @click="updateSort(3)"
                                    >
                                        儲存
                                    </button>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="col-sm-5">名稱</th>
                                        <th class="col-sm-7">功能</th>
                                    </tr>
                                </thead>
                                <tbody
                                    is="draggable"
                                    tag="tbody"
                                    :list="level[3].categories"
                                    v-bind="dragOptions"
                                >
                                    <tr
                                        v-for="(category, index) in level[3].categories"
                                        :key="index"
                                    >
                                        <td>
                                            <i class="fa-solid fa-list fa-xl draggable-handle"></i>
                                            @{{ category.categoryName }}
                                        </td>
                                        <td>
                                            <div class="function-container">
                                                <button
                                                    type="button"
                                                    class="btn btn-warning"
                                                    v-show="auth.update"
                                                    @click="showEditModal(category)"
                                                >
                                                    編輯
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-danger"
                                                    v-show="auth.delete"
                                                    @click="deleteCategory(category, index)"
                                                >
                                                    刪除
                                                </button>
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
            computed: {
                dragOptions() {
                    return {
                        animation: 200,
                        handle: ".draggable-handle",
                        ghostClass: "draggable-ghost",
                    };
                },
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
                        formData.append("is_icon_deleted", !this.modal.categoryForm.icon.showDeleteButton);
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
                deleteCategory(category, index) {
                    let alias = '';
                    switch (category.categoryLevel) {
                        case 1:
                            alias = '大';
                            break;

                        case 2:
                            alias = '中';
                            break;

                        case 3:
                            alias = '小';
                            break;
                    }

                    if (confirm(`確定要刪除${alias}分類 ${category.categoryName}`)) {
                        axios({
                            method: "delete",
                            url: `${BASE_URI}/${category.id}`,
                        })
                        .then((response) => {
                            this.level[category.categoryLevel].categories.splice(index, 1);
                            this.showLevel = category.categoryLevel;
                            alert('刪除成功');
                        })
                        .catch((error) => {
                            if (error.response) {
                                let data = error.response.data;
                                let errorMessage = '刪除失敗: ';

                                switch (data.code) {
                                    // 子分類未刪除
                                    case 'E100':
                                        errorMessage += data.message;
                                        alert(errorMessage);
                                        break;

                                    // 商品未刪除
                                    case 'E101':
                                        errorMessage += data.message;
                                        alert(errorMessage);
                                        break;

                                    default:
                                        alert('刪除失敗');
                                        break;
                                }
                            }
                        });
                    }
                },
                updateSort(level) {
                    let categoryIds = this.level[level].categories.map(category => category.id);

                    axios({
                        method: "put",
                        url: `${BASE_URI}/sort`,
                        data: {
                            parent_id: this.level[level].parentId,
                            category_ids: categoryIds,
                        },
                    })
                    .then((response) => {
                        alert('儲存成功');
                    })
                    .catch((error) => {
                        if (error.response) {
                            let data = error.response.data;
                            alert('儲存失敗');
                        }
                    });
                },
                isSaveSortButtonDisabled(level) {
                    return this.level[level].categories.length < 2;
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
