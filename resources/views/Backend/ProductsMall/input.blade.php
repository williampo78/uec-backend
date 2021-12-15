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
                                            <tr v-for="(Category, CategoryKey) in CategoryHierarchyProducts" @dragstart="drag"
                                            @dragover='dragover' @dragleave='dragleave' @drop="drop"  draggable="true"
                                            :data-index="CategoryKey" :data-type="'Category'">
                                                <td style="vertical-align:middle">
                                                    <i class="fa fa-list"></i>
                                                    @{{ Category . category_name }}
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger"
                                                        @click="DelCategory(Category,CategoryKey)"
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
                        <button class="btn btn-large btn-success" type="button" id="save_data">儲存</button>
                        <a class="btn btn-danger" href="{{ url('') }}"><i class="fa fa-ban"></i> 取消</a>
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
                    CategoryHierarchyContent: @json($category_hierarchy_content), //所有分類的List
                    CategoryList: [], //顯示的分類列表
                    SelectCategoryName: '',
                    products: @json($products),
                    select_req: {
                        // supplier_id :$('#supplier').val() ,
                        product_no: '',
                        product_name: '',
                        selling_price_min: '',
                        selling_price_max: '',
                    },
                    result_products:[] ,
                }
            },
            mounted() {

            },
            created() {
                let vm = this;
                this.CategoryHierarchyProducts.map(function(value, key) {
                    isset = vm.CategoryHierarchyContent.filter(data => data.id === value
                        .web_category_hierarchy_id);
                    value.category_name = isset[0].name;
                    value.status = 'old';
                })
                this.CategoryListFilter(); // 先將原先的分類拔除
            },
            methods: {
                DelCategory(obj, key) {
                    if (confirm('確定要刪除嗎?')) {
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
                    }
                    this.CategoryListFilter();
                    // this.CategoryListFilter();
                },
                addContentToProductsCategory(obj, key) {
                    console.log(this.products);

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
                    // this.category_products_list.find((todo, index) => {
                    //     filter_product_id.push(todo.product_id);
                    // })
                    // console.log(filter_product_id)  ;
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
                        console.log(response);
                        this.result_products = response.data.result.data;
                    }
                    req();
                },
                productsForCategory() {
                    let list = [];
                    let readyDel = [];
                    var findthis = this.result_products.find((todo, index) => {
                        if (todo.check_use == 1) {
                            this.category_products_list.push({
                                web_category_products_id: '',
                                agent_id: todo.agent_id,
                                created_date: todo.created_date,
                                end_launched_at: todo.end_launched_at,
                                gross_margin: todo.gross_margin,
                                product_id: todo.id,
                                item_cost: todo.item_cost,
                                launched_status: todo.launched_status,
                                launched_status_desc: todo.launched_status_desc,
                                product_name: todo.product_name,
                                product_no: todo.product_no,
                                selling_price: todo.selling_price,
                                start_launched_at: todo.start_launched_at,
                                stock_type: todo.stock_type,
                                supplier_id: todo.supplier_id,
                            });
                            readyDel.push(index);
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
                            case '2':
                             
                                break;
                            case '3':
                                // var item = this.category_level_3[index];
                                // this.category_level_3.splice(index, 1)
                                // this.category_level_3.splice(targetIndex, 0, item)
                                break;
                            default:
                                break;
                        }
                    }

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
