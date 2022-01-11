@extends('Backend.master')
@section('title', '商品主檔 - 新增商城資訊')
@section('content')
    <style>
        .ondragover {
            background: #b7e0fb !important;
            transition: background-color 0.5s;
            /* background: #ce1f59 !important; */
        }

        .elements-box>tr>td>* {
            pointer-events: none;
        }

        .modal-dialog {
            max-width: 100%;
        }

    </style>
    <div class="sysinfo">
        <div class="sysinfo-title theme-color">基本檔</div>
        <div class="sysinfo-content">
            <ul class="navigation">
                <a href="#page-1">
                    <li class="sysinfo-li" id="click-page-1">
                        前台資料
                    </li>
                </a>
                <a href="#page-2">
                    <li class="sysinfo-li" id="click-page-2">
                        商品介紹
                    </li>
                </a>
                <a href="#page-3">
                    <li class="sysinfo-li" id="click-page-3">
                        媒體檔
                    </li>
                </a>
                <a href="#page-4">
                    <li class="sysinfo-li" id="click-page-4">
                        SEO
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
            <div class="panel-body" id="CategoryHierarchyContentInput" v-cloak>
                <form role="form" id="new-form" method="POST" action="{{ route('product_small.update', $products->id) }}"
                    enctype="multipart/form-data" novalidaten="ovalidate">
                    @csrf
                    @method('PUT')
                    <div class="form-horizontal">
                        <section id="page-1">
                            <div class="row">
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
                                            <input class="form-control" name="supplier_name"
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
                                            <button class="btn btn-large btn-warning btn-sm" type="button"
                                                data-toggle="modal" data-target="#model_category">新增分類</button>
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
                                                <tr class="CategoryHierarchyProducts" v-for="(Category, CategoryKey) in CategoryHierarchyProducts"
                                                    @dragstart="drag" @dragover='dragover' @dragleave='dragleave'
                                                    @drop="drop" draggable="true" :data-index="CategoryKey"
                                                    :data-type="'Category'">
                                                    <td style="vertical-align:middle">
                                                        <i class="fa fa-list"></i>
                                                        @{{ Category . category_name }}
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger"
                                                            @click="Del(Category,CategoryKey,'Category')"
                                                            v-show="RoleAuthJson.auth_delete">刪除</button>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                        <span id="CategoryHierarchyProducts_error_msg" style="display: none" class="redtext">必須填寫</span>
                                    </div>
                                    <textarea name="CategoryHierarchyProducts_Json" style="display: none" cols="30"
                                        rows="10">@{{ CategoryHierarchyProducts }}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-1">
                                            <label class="control-label">關聯關鍵字</label>
                                        </div>
                                        <div class="col-sm-11">
                                            <input class="form-control" name="keywords"
                                                value="{{ $products->keywords }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-1">
                                            <label class="control-label">關聯性商品</label>
                                        </div>
                                        <div class="col-sm-10">
                                            {{-- related_products table --}}
                                            <button class="btn btn-large btn-warning btn-sm" type="button"
                                                data-toggle="modal" data-target="#model_related_products">新增商品</button>
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
                                                <tr v-for="(Product, key) in RelatedProducts" @dragstart="drag"
                                                    @dragover='dragover' @dragleave='dragleave' @drop="drop"
                                                    draggable="true" :data-index="key" :data-type="'Products'">
                                                    <td style="vertical-align:middle">
                                                        <i class="fa fa-list"></i>
                                                        @{{ Product . product_name }}
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger"
                                                            @click="Del(Product ,key ,'Products')">刪除</button>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                    <textarea name="RelatedProducts_Json" style="display: none" id="" cols="30"
                                        rows="10">@{{ RelatedProducts }}</textarea>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-1">
                                            <label class="control-label">每單限購數量<span
                                                    class="redtext">*</span></label>
                                        </div>
                                        <div class="col-sm-2">
                                            <input class="form-control" name="order_limited_qty"
                                                value="{{ $products->order_limited_qty }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <hr>
                        <section id="page-2">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-1">
                                            <label class="control-label">促銷小標</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class='input-group date'>
                                                <input type='text' class="form-control" name="promotion_start_at"
                                                    id="promotion_start_at"
                                                    value="{{ $products->promotion_start_at }}" />
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
                                            <label class="control-label">商品內容<span
                                                    class="redtext">*</span></label>
                                        </div>
                                        <div class="col-sm-11">
                                            <textarea id="description" name="description" placeholder="請在這裡填寫內容">{{ $products->description }}</textarea>
                                            <span id="description_error_msg" style="display: none" class="redtext">必須填寫</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-1">
                                            <label class="control-label">商品規格<span
                                                    class="redtext">*</span></label>
                                        </div>
                                        <div class="col-sm-11">
                                            <textarea id="specification" name="specification" placeholder="請在這裡填寫內容"
                                                accept=".jpg,.jpeg,.png">{{ $products->specification }}</textarea>
                                                <span id="specification_error_msg" style="display: none" class="redtext">必須填寫</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <hr>
                        <section id="page-3">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-1">
                                            <label class="control-label">Google Shop圖檔
                                            </label>
                                        </div>
                                        <div class="col-sm-2">
                                            <input type="file" name="google_shop_photo_name" accept=".jpg,.jpeg,.png"
                                                @change="google_shop">
                                            <input type="hidden" name="google_shop_photo_name_old"
                                                value="{{ $products->google_shop_photo_name }}">
                                        </div>
                                        <div class="col-sm-3">
                                            <img :ref="'GoogleShopPhoto'"
                                                src="{{ $products->google_shop_photo_name !== null ? config('filesystems.disks.s3.url') . $products->google_shop_photo_name : asset('asset/img/default_item.png') }} "
                                                style="max-width:100%;">
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-1">
                                            <label class="control-label">Item圖示<span
                                                    class="redtext">*</span></label>
                                            {{-- product_items --}}
                                        </div>
                                        <div class="col-sm-11">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="col-sm-1">規格1</th>
                                                        <th class="col-sm-1">規格2</th>
                                                        <th class="col-sm-1">Item圖示</th>
                                                        <th class="col-sm-1">功能</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(Item , key ) in ProductsItem">
                                                        <td style="vertical-align:middle">
                                                            @{{ Item . spec_1_value }}
                                                        </td>
                                                        <td style="vertical-align:middle">
                                                            @{{ Item . spec_2_value }}
                                                        </td>
                                                        <td>
                                                            <div v-if="Item.photo_name">
                                                                <img  :src="file_cdn + Item.photo_name" style="max-width:100%;">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button type="button" data-toggle="modal" data-target="#item_photo_list" class="btn btn-large btn-warning btn-sm" @click="frombtn(Item,key)">選擇圖片</button>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                            <textarea name="ProductsItem_Json" style="display:none" cols="30"
                                                rows="10">@{{ ProductsItem }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <hr>
                        <section id="page-4">
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
                        </section>
                        <button class="btn btn-large btn-success" type="button" id="save_data">
                            <i class="fa fa-save"></i>
                            儲存
                        </button>
                        <a class="btn btn-danger" href="{{ URL::previous() }}"><i class="fa fa-ban"></i>
                            取消</a>
                </form>
                @include('Backend.ProductsMall.model_category')
                @include('Backend.ProductsMall.model_related_products')
                @include('Backend.ProductsMall.model_photo_list')

            </div>
        </div>

    </div>

@endsection
@section('js')
    <script>
        // Get all sections that have an ID defined
        $(document).ready(function() {
            $('#promotion_start_at').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss',
            });
            $('#promotion_end_at').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss',
            });
            var ck_description;
            var ck_specification;
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
                .then(editor => {
                    ck_description = editor; // Save for later use.
                }).catch(error => {
                    console.error(error);
                });

            ClassicEditor.create(document.querySelector('#specification'), {
                ckfinder: {
                    uploadUrl: "/ckfinder/connector?command=QuickUpload&type=Images&responseType=json&_token=" +
                        document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    }
                },
            }).then(editor => {
                ck_specification = editor; // Save for later use.
            }).catch(error => {
                console.error(error);
            });

            $("#new-form").validate({
                submitHandler: function(form) {
                    if(ck_description.getData().trim().length == 0 ){
                        $('#description_error_msg').show() ;
                        alert('商品內容不能為空') ;
                        return false ;
                    }else{
                        $('#description_error_msg').hide() ;
                    }
                    
                    if(ck_specification.getData().trim().length == 0 ){
                        $('#specification_error_msg').show() ;
                        alert('商品規格不能為空') ;
                        return false ;
                    }else{
                        $('#specification_error_msg').hide() ;
                    }

                    if($('.CategoryHierarchyProducts').length == 0){
                        alert('至少要新增一項分類') ;
                        $('#CategoryHierarchyProducts_error_msg').show() ;
                        return false ;
                    }else{
                        $('#CategoryHierarchyProducts_error_msg').hide() ;
                    }
                    $('#save_data').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    product_name: {
                        required: true,
                    },
                    order_limited_qty: {
                        required: true,
                        digits: true,
                    },
                },
                errorClass: "help-block",
                errorElement: "span",
                errorPlacement: function(error, element) {
                    if (element.parent('.input-group').length || element.is(':radio')) {
                        error.insertAfter(element.parent());
                        return;
                    }

                    if (element.is('select')) {
                        element.parent().append(error);
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
            $(document).on("click", "#save_data", function() {
                $("#new-form").submit()
            })

        });
        var CategoryHierarchyContentInput = Vue.extend({
            data: function() {
                return {
                    CategoryHierarchyProducts: @json($web_category_hierarchy), //該商品原有的分類
                    CategoryHierarchyContent: @json($category_hierarchy_content), //所有分類的List
                    CategoryList: [], //顯示的分類列表
                    SelectCategoryName: '',
                    products: @json($products),
                    select_req: {
                        product_no: '',
                        product_name: '',
                        selling_price_min: '',
                        selling_price_max: '',
                    },
                    result_products: [],
                    RelatedProducts: @json($related_products),
                    ProductsItem: @json($products_item),
                    DefaultassetImg: '{{ asset('asset/img/default_item.png') }}',
                    file_cdn: @json(config('filesystems.disks.s3.url')),
                    product_photos:@json($product_photos),
                    ready_photo : [] ,
                }
            },
            mounted() {
                const sections = document.querySelectorAll("section[id]");
                // Add an event listener listening for scroll
                window.addEventListener("scroll", navHighlighter);

                function navHighlighter() {
                    // Get current scroll position
                    let scrollY = window.pageYOffset;

                    sections.forEach(current => {
                        const sectionHeight = current.offsetHeight;
                        const sectionTop = current.offsetTop - 50;
                        sectionId = current.getAttribute("id");
                        if (
                            scrollY > sectionTop &&
                            scrollY <= sectionTop + sectionHeight
                        ) {
                            document.querySelector("a[href*=" + sectionId + "] li").classList.add(
                                "sysinfo-activie");
                        } else {
                            document.querySelector("a[href*=" + sectionId + "] li").classList.remove(
                                "sysinfo-activie");
                        }
                    });
                }
            },
            created() {
                let vm = this;
                this.CategoryHierarchyProducts.map(function(value, key) {
                    isset = vm.CategoryHierarchyContent.filter(data => data.id === value
                        .web_category_hierarchy_id);
                    value.category_name = isset[0].name;
                    value.status = 'old';
                })
                // console.log(this.ProductsItem) ; 
                // this.ProductsItem.map(function(value, key) {
                //     if (value.photo_name == null) {
                //         value.imgesUrl = vm.DefaultassetImg;
                //     } else {
                //         value.imgesUrl = vm.file_cdn + value.photo_name;
                //     }
                // })
                this.CategoryListFilter(); // 先將原先的分類拔除
            },
            methods: {
                Del(obj, key, type) {
                    if (confirm('確定要刪除嗎?')) {
                        if (type == 'Category') {
                            if (obj.status == 'old') {
                                axios.post('/backend/product_small/ajax', {
                                        product_id: this.products.id,
                                        category_id: obj.web_category_hierarchy_id,
                                        _token: '{{ csrf_token() }}',
                                        type: 'DelCategoryInProduct',
                                    })
                                    .then(function(response) {
                                        console.log(response);
                                    })
                                    .catch(function(error) {
                                        console.log(error);
                                    });
                            }
                            this.$delete(this.CategoryHierarchyProducts, key);
                            this.CategoryListFilter();
                        } else if (type == 'Products') {
                            if (obj.id !== '') {
                                axios.post('/backend/product_small/ajax', {
                                        id: obj.id,
                                        _token: '{{ csrf_token() }}',
                                        type: 'DelRelatedProducts',
                                    })
                                    .then(function(response) {
                                        console.log(response);
                                    })
                                    .catch(function(error) {
                                        console.log(error);
                                    });
                            }
                            this.$delete(this.RelatedProducts, key);
                        }
                    }

                },
                addContentToProductsCategory(obj, key) {
                    let sort = this.CategoryHierarchyProducts.length;
                    this.CategoryHierarchyProducts.push({
                        category_name: obj.name,
                        status: 'new',
                        web_category_hierarchy_id: obj.id
                    })
                    this.CategoryListFilter();
                },
                CategoryListFilter() {
                    let vm = this;
                    let isset = [];
                    let list = [];
                    this.CategoryHierarchyContent.map(function(value, key) {
                        isset = vm.CategoryHierarchyProducts.filter(data => data
                            .web_category_hierarchy_id === value.id);
                        if (isset.length == 0) {
                            list.push(value);
                        }
                        if (vm.SelectCategoryName !== '') {
                            list = list.filter(data =>
                                data.name.toLowerCase().includes(vm.SelectCategoryName.toLowerCase())
                            )
                        };
                    })
                    this.CategoryList = list;
                },
                productsGetAjax() {
                    var create_start_date = $('input[name="create_start_date"]').val();
                    var create_end_date = $('input[name="create_end_date"]').val();
                    var select_start_date = $('input[name="select_start_date"]').val();
                    var select_end_date = $('input[name="select_end_date"]').val();
                    var filter_product_id = [];
                    this.RelatedProducts.find((todo, index) => {
                        filter_product_id.push(todo.related_product_id);
                    })
                    var req = async () => {
                        const response = await axios.post('/backend/web_category_products/ajax', {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            type: 'getProductsList',
                            product_no: this.select_req.product_no,
                            product_name: this.select_req.product_name,
                            selling_price_min: this.select_req.selling_price_min,
                            selling_price_max: this.select_req.selling_price_max,
                            create_start_date: create_start_date,
                            create_end_date: create_end_date,
                            select_start_date: select_start_date,
                            select_end_date: select_end_date,
                            filter_product_id: filter_product_id, //排除掉 ID
                        });
                        this.result_products = response.data.result.data;
                    }

                    req();
                },
                productsForCategory() {
                    let list = [];
                    var findthis = this.result_products.find((todo, index) => {
                        if (todo.check_use == 1) {
                            this.RelatedProducts.push({
                                id: '',
                                product_id: todo.id,
                                related_product_id: todo.id,
                                product_name: todo.product_name,
                                product_no: todo.product_no,
                                supplier_id: todo.supplier_id,
                            });
                            todo.del = 1;
                        } else {
                            todo.del = 0;
                        }
                    })
                    let new_array = this.result_products.filter(function(obj) {
                        return obj.del == 0;
                    });
                    this.result_products = new_array;
                },
                check_all(act) {
                    var status = '';
                    if (act == 'allon') {
                        status = 1;
                    } else if (act == 'alloff') {
                        status = 0;
                    }
                    var findthis = this.result_products.find((todo, index) => {
                        todo.check_use = status;
                    })
                },
                drag(eve) {
                    eve.dataTransfer.setData("text/index", eve.target.dataset.index);
                    eve.dataTransfer.setData("text/type", eve.target.dataset.type);
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
                    var type = eve.dataTransfer.getData("text/type");
                    let targetIndex = eve.target.parentNode.dataset.index;
                    let targetType = eve.target.parentNode.dataset.type;
                    if (targetType !== type) {
                        alert('不能跨分類喔!');
                    } else {
                        switch (type) {
                            case 'Category':
                                var item = this.CategoryHierarchyProducts[index];
                                this.CategoryHierarchyProducts.splice(index, 1);
                                this.CategoryHierarchyProducts.splice(targetIndex, 0, item);
                                break;
                            case 'Products':
                                var item = this.RelatedProducts[index];
                                this.RelatedProducts.splice(index, 1);
                                this.RelatedProducts.splice(targetIndex, 0, item);
                                break;
                            default:
                                break;
                        }
                    }

                },
                google_shop(e) {
                    let file = e.target.files[0];
                    let type = e.target.files[0].type;
                    if (type !== 'image/jpeg' && type !== 'image/png') {
                        alert('格式錯誤');
                        e.target.value = '';
                    } else {
                        this.$refs.GoogleShopPhoto.src = URL.createObjectURL(file);
                    }
                },
                frombtn(item , key) {
                    this.ready_photo[0] = item ;
                    this.ready_photo[1] = key ;  
                    console.log(this.ready_photo) ; 
                },
                AddPhoto(photo , key){
                    ProductsItemKey = this.ready_photo[1] ;
                    this.ProductsItem[ProductsItemKey].photo_name = photo.photo_name ; 
                }
                // item_photo(e, key) {
                //     let file = e.target.files[0];
                //     let type = e.target.files[0].type;
                //     if (type !== 'image/jpeg' && type !== 'image/png') {
                //         alert('格式錯誤');
                //         e.target.value = '';
                //         this.$refs.img[key].src = URL.createObjectURL(file);
                //     } else {
                //         this.$refs.img[key].src = URL.createObjectURL(file);
                //     }
                // }
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
