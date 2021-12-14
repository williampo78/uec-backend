@extends('Backend.master')
@section('title', '商品主檔 - 新增商城資訊')
@section('content')
    <style>



    </style>
    <div class="sysinfo">
        <div class="sysinfo-title theme-color">基本檔</div>
        <div class="sysinfo-content">
            <ul>
                <a href="#page-1">
                    <li class="sysinfo-li sysinfo-activie" id="click-page-1">
                        前台資料
                    </li>
                </a>
                <a href="#page-2">
                    <li class="sysinfo-li" id="click-page-2">
                        規格
                    </li>
                </a>
                {{-- <li></li> --}}
            </ul>
        </div>
    </div>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><i class="fa fa-list"></i>商品主檔 - 新增商城資訊</h1>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">請輸入下列欄位資料</div>
            <div class="panel-body" id="CategoryHierarchyContentInput">
                <form role="form" id="new-form" method="POST" action="{{ route('products.store') }}"
                    enctype="multipart/form-data" novalidaten="ovalidate">
                    @csrf
                    <div id="page-1" class="form-horizontal">

                        <div class="row ">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">庫存類型</label><span class="redtext">*</span>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="stock_type" value="A"
                                                {{ $products->stock_type == 'A' ? 'checked' : 'disabled' }}>
                                            買斷
                                            [A]
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="stock_type" value="B"
                                                {{ $products->stock_type == 'B' ? 'checked' : 'disabled' }}>
                                            寄售
                                            [B]
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="radio-inline">
                                            <input type="radio" name="stock_type" value="T"
                                                {{ $products->stock_type == 'T' ? 'checked' : 'disabled' }}>
                                            轉單[T]
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label ">商品序號</label><span class="redtext">*</span>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="product_no"
                                            value="{{ $products->product_no }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">供應商<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="product_no"
                                            value="{{ $products->supplier_name }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class=" form-group">
                                    <div class="col-sm-2 ">
                                        <label class="control-label">商品名稱<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="product_name"
                                            value="{{ $products->product_name }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">
                                            <a href="{{ route('products.show', $products->id) }}"
                                                target="_blank">查看基本資訊</a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12" id="category_products">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">前台分類<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-11">
                                        <button class="btn btn-large btn-warning btn-sm" type="button" data-toggle="modal"
                                            data-target="#model_category">新增分類</button>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="col-sm-11">名稱</th>
                                                <th class="col-sm-1">功能</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(Category, CategoryKey) in CategoryHierarchyProducts">
                                                <td style="vertical-align:middle">
                                                    <i class="fa fa-list"></i>
                                                    @{{ Category . category_name }}
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger"
                                                        @click="DelCategory(Category.web_category_hierarchy_id,CategoryKey)"
                                                        v-show="RoleAuthJson.auth_delete">刪除</button>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">關聯關鍵字</label>
                                    </div>
                                    <div class="col-sm-11">
                                        <input class="form-control" name="keywords" value="{{ $products->keywords }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">關聯性商品<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-10">
                                        {{-- related_products table --}}
                                        <button class="btn btn-large btn-warning btn-sm" type="button" data-toggle="modal"
                                            data-target="#model_related_products">新增商品</button>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="col-sm-11">名稱</th>
                                                <th class="col-sm-1">功能</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="vertical-align:middle">
                                                    <i class="fa fa-list"></i>
                                                    名稱
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger"
                                                        @click="DelCategory(level_1_obj.id)"
                                                        v-show="RoleAuthJson.auth_delete">刪除</button>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">每單限購數量<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-2">
                                        <input class="form-control" name="order_limited_qty"
                                            value="{{ $products->order_limited_qty }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">促銷小標生效時間
                                        </label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input class="form-control" name="promotion_desc"
                                            value="{{ $products->promotion_desc }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">促銷小標</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class='input-group date'>
                                            <input type='text' class="form-control" name="promotion_start_at"
                                                id="promotion_start_at" value="{{ $products->promotion_start_at }}" />
                                        </div>
                                    </div>
                                    <div class="col-sm-1" style="padding: 0px;width: 2%;">
                                        <label class="control-label">~</label>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class='input-group date'>
                                            <input type='text' class="form-control" name="promotion_end_at"
                                                id="promotion_end_at" value="{{ $products->promotion_end_at }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">商品內容<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-11">
                                        <textarea id="description" name="description" placeholder="請在這裡填寫內容"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">商品規格<span class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-11">
                                        <textarea id="specification" name="specification" placeholder="請在這裡填寫內容"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">Google Shop圖檔<span
                                                class="redtext">*</span></label>
                                    </div>
                                    <div class="col-sm-11">
                                        <input type="file" name="google_shop_photo_name">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">Item圖示<span class="redtext">*</span></label>
                                        {{-- product_items --}}
                                    </div>
                                    <div class="col-sm-11">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="col-sm-2">spac_1</th>
                                                    <th class="col-sm-2">spac_2</th>
                                                    <th class="col-sm-2">Item圖示 *</th>
                                                    <th class="col-sm-2">預覽</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="vertical-align:middle">
                                                        spac_1_name
                                                    </td>
                                                    <td style="vertical-align:middle">
                                                        spac_2_name
                                                    </td>
                                                    <td>
                                                        <input type="file">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">網頁標題</label>
                                    </div>
                                    <div class="col-sm-11">
                                        <input class="form-control" name="meta_title"
                                            value="{{ $products->meta_title }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label class="control-label">網頁描述</label>
                                    </div>
                                    <div class="col-sm-11">
                                        <input class="form-control" name="mata_description"
                                            value="{{ $products->mata_description }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-2">
                                        <label class="control-label">網頁標籤(以半形逗號分隔)</label>
                                    </div>
                                    <div class="col-sm-10">
                                        <input class="form-control" name="mata_keywords"
                                            value="{{ $products->mata_keywords }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-large btn-primary" type="button" id="save_data">儲存</button>
                </form>
                @include('Backend.ProductsMall.model_category')
                @include('Backend.ProductsMall.model_related_products')
            </div>
        </div>

    </div>

@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('#promotion_start_at').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss',
            });
            $('#promotion_end_at').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss',
            });
            ClassicEditor.create(document.querySelector('#description'), {
                ckfinder: {
                    uploadUrl: "/ckfinder/connector?command=QuickUpload&type=Images&responseType=json&_token=" +
                        document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    }

                },
            })
            ClassicEditor.create(document.querySelector('#specification'), {
                ckfinder: {
                    uploadUrl: "/ckfinder/connector?command=QuickUpload&type=Images&responseType=json&_token=" +
                        document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    }
                },
            })
        });
        var CategoryHierarchyContentInput = Vue.extend({
            data: function() {
                return {
                    CategoryHierarchyProducts: @json($web_category_hierarchy), //該商品原有的分類
                    CategoryHierarchyContent:  @json($category_hierarchy_content), //所有分類的List
                    CategoryList: [] , //顯示的分類列表
                    SelectCategoryName: '',
                }
            },
            mounted() {

            },
            created() {
                console.log(this.CategoryHierarchyProducts) ; 
                console.log(this.CategoryHierarchyContent) ; 
                this.CategoryListFilter(); // 先將原先的分類拔除
            },
            methods: {
                DelCategory(id, key) {
                    // this.$delete(this.images, index);
                    this.CategoryListFilter();
                },
                addContentToProductsCategory(id, key) {
                    console.log('ADD');
                },
                CategoryListFilter() {
                    let vm = this;
                    let isset = [];
                    let list  = [];
                    this.CategoryHierarchyContent.map(function(value, key) {
                        isset = vm.CategoryHierarchyProducts.filter(data => data.web_category_hierarchy_id === value.id);
                        console.log(isset) ; 
                        if(isset.length == 0){
                            list.push(value) ; 
                        }
                        if (vm.SelectCategoryName !== '') {
                            list = list.filter(data =>
                                data.name.toLowerCase().includes(vm.SelectCategoryName.toLowerCase())
                            )
                        };
                    })
                    this.CategoryList = list ; 
                },
            },
            computed: {},
            watch: {
                SelectCategoryName() {
                    return this.CategoryListFilter();
                }
            },
        })
        new CategoryHierarchyContentInput().$mount('#CategoryHierarchyContentInput');
    </script>
@endsection
