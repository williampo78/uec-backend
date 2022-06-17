@extends('backend.master')

@section('title', '編輯寄售商品入庫申請')

@section('content')
    <div id="app" v-cloak>
        @if ($errors->any())
            <div ref="errorMessage" style="display: none;">
                {{ $errors->first('message') }}
            </div>
        @endif

        <div id="page-wrapper">
            <!-- 表頭名稱 -->
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header"><i class="fa-solid fa-plus"></i> 編輯寄售商品入庫申請</h1>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">請輸入下列欄位資料</div>
                        <div class="panel-body">
                            <form id="edit-form" method="post" action="{{ route('buyout_stock_in_requests.update', $buyoutStockInRequest['id']) }}">
                                @method('PUT')
                                @csrf
                                <div class="form-horizontal">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label class="control-label">申請單號</label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <input type="text" class="form-control" :value="form.requestNo" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label class="control-label">申請時間</label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <input type="text" class="form-control" :value="form.requestDate" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label class="control-label">供應商 <span style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <select2 class="form-control" :options="suppliers" v-model="form.supplierId" name="supplier_id" :disabled="isSupplier">
                                                        <option disabled value=""></option>
                                                    </select2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label class="control-label">預計入庫日 <span style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <div class="input-group" id="expected_date_flatpickr">
                                                        <input type="text" class="form-control" name="expected_date"
                                                            autocomplete="off" data-input v-model="form.expectedDate">
                                                        <span class="input-group-btn" data-toggle>
                                                            <button class="btn btn-default" type="button">
                                                                <i class="fa-solid fa-calendar-days"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label class="control-label">總申請量</label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <input type="text" class="form-control" :value="expectedQty()" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label class="control-label">備註</label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <textarea class="form-control" rows="5" name="remark" v-model="form.remark"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr style="border-top: 1px solid gray;">

                                <h4>品項</h4>
                                <table class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap">項次</th>
                                            <th class="text-nowrap">商品Item <span style="color: red;">*</span></th>
                                            <th class="text-nowrap">規格一</th>
                                            <th class="text-nowrap">規格二</th>
                                            <th class="text-nowrap">最小入庫量</th>
                                            <th class="text-nowrap">申請數量 <span style="color: red;">*</span></th>
                                            <th class="text-nowrap"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in form.items" :key="index">
                                            <input type="hidden" :name="`items[${index}][id]`" :value="item.id">
                                            <td>
                                                @{{ index + 1 }}
                                                <input type="hidden" :name="`items[${index}][seq_no]`" :value="index + 1">
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <select2 class="form-control item-product-item" :options="productItems" v-model="item.productItemId" :name="`items[${index}][product_item_id]`" @select2-change="changeProductItem($event, item)">
                                                        <option disabled value=""></option>
                                                    </select2>
                                                </div>
                                            </td>
                                            <td>@{{ item.spec1Value }}</td>
                                            <td>@{{ item.spec2Value }}</td>
                                            <td>@{{ item.minPurchaseQty }}</td>
                                            <td>
                                                <div class="form-group">
                                                    <input type="number" class="form-control item-qty"
                                                        :name="`items[${index}][qty]`" :min="item.minPurchaseQty" :step="item.qtyStep"
                                                        v-model="item.qty">
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger" @click="deleteItem(index)">
                                                    <i class="fa-solid fa-trash-can"></i> 刪除
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <button type="button" class="btn btn-warning" @click="addItem">
                                            <i class="fa-solid fa-plus"></i> 新增品項
                                        </button>
                                    </div>
                                </div>
                                <hr style="border-top: 1px solid gray;">

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="hidden" name="save_type" :value="form.saveType">

                                            @if ($share_role_auth['auth_update'])
                                                <button class="btn btn-success" type="button" :disabled="saveButton.isDisabled" @click="saveAsDraft">
                                                    <i class="fa-solid fa-floppy-disk"></i> 儲存草稿
                                                </button>

                                                <button class="btn btn-success" type="button" :disabled="saveButton.isDisabled" @click="saveAsReview">
                                                    <i class="fa-solid fa-floppy-disk"></i> 儲存並送審
                                                </button>
                                            @endif

                                            <a href="{{ route('buyout_stock_in_requests') }}" class="btn btn-danger">
                                                <i class="fa-solid fa-ban"></i> 取消
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        let vm = new Vue({
            el: "#app",
            data: {
                form: {
                    saveType: "",
                    requestNo: "",
                    requestDate: moment().format("YYYY-MM-DD HH:mm"),
                    supplierId: "",
                    expectedDate: "",
                    remark: "",
                    items: [{
                        productItemId: "",
                        spec1Value: "",
                        spec2Value: "",
                        minPurchaseQty: "",
                        qty: "",
                        qtyStep: 1,
                    }],
                },
                saveButton: {
                    isDisabled: false,
                },
                suppliers: [],
                isSupplier: false,
                productItems: [],
            },
            created() {
                let suppliers = @json($suppliers);
                let isSupplier = @json($isSupplier);
                let supplierId = @json($supplierId);
                let buyoutStockInRequest = @json($buyoutStockInRequest);

                if (suppliers) {
                    suppliers.forEach(supplier => {
                        this.suppliers.push({
                            text: supplier.name,
                            id: supplier.id,
                        });
                    });
                }

                this.isSupplier = isSupplier;
                if (isSupplier) {
                    this.form.supplierId = supplierId;
                }

                if (buyoutStockInRequest) {
                    this.form.requestNo = buyoutStockInRequest.request_no;
                    this.form.requestDate = buyoutStockInRequest.request_date;
                    this.form.supplierId = buyoutStockInRequest.supplier_id;
                    this.form.expectedDate = buyoutStockInRequest.expected_date;
                    this.form.remark = buyoutStockInRequest.remark;

                    if (Array.isArray(buyoutStockInRequest.items) && buyoutStockInRequest.items.length) {
                        this.form.items = [];

                        buyoutStockInRequest.items.forEach(item => {
                            this.form.items.push({
                                id: item.id,
                                productItemId: item.product_item_id,
                                qty: item.expected_qty,
                            });
                        });
                    }
                }
            },
            watch: {
                async "form.supplierId"(newValue, oldValue) {
                    let productItems = await this.getProductItems(newValue);

                    this.productItems = [];
                    productItems.forEach(productItem => {
                        this.productItems.push({
                            text: `${productItem.item_no}-${productItem.supplier_item_no}-${productItem.product_name}`,
                            id: productItem.id,
                            spec_1_value: productItem.spec_1_value,
                            spec_2_value: productItem.spec_2_value,
                            min_purchase_qty: productItem.min_purchase_qty,
                        });
                    });
                },
            },
            mounted() {
                let self = this;

                if (this.$refs.errorMessage) {
                    alert(this.$refs.errorMessage.innerText.trim());
                }

                let expectedDateFlatpickr = flatpickr("#expected_date_flatpickr", {
                    dateFormat: "Y-m-d",
                    minDate: moment().format("YYYY-MM-DD"),
                });

                // 驗證表單
                $("#edit-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        self.saveButton.isDisabled = true;
                        form.submit();
                    },
                    rules: {
                        supplier_id: {
                            required: true,
                        },
                        expected_date: {
                            required: true,
                        },
                        remark: {
                            maxlength: 150,
                        },
                    },
                    errorClass: "help-block",
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        if (element.closest(".input-group").length) {
                            element.closest(".input-group").parent().append(error);
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
            },
            methods: {
                addItem() {
                    this.form.items.push({
                        productItemId: "",
                        spec1Value: "",
                        spec2Value: "",
                        minPurchaseQty: "",
                        qty: "",
                        qtyStep: 1,
                    });
                },
                deleteItem(index) {
                    if (confirm('確定要刪除嗎？')) {
                        this.form.items.splice(index, 1);
                    }
                },
                changeProductItem(id, item) {
                    if (id) {
                        let productItem = this.productItems.find(productItem => {
                            return productItem.id == id;
                        });

                        item.spec1Value = productItem.spec_1_value;
                        item.spec2Value = productItem.spec_2_value;
                        item.minPurchaseQty = productItem.min_purchase_qty;
                        item.qtyStep = productItem.min_purchase_qty > 0 ? productItem.min_purchase_qty : 1;
                    } else {
                        item.spec1Value = "";
                        item.spec2Value = "";
                        item.minPurchaseQty = "";
                        item.qty = "";
                        item.qtyStep = 1;
                    }
                },
                saveAsDraft() {
                    this.form.saveType = "draft";
                    this.submitForm();
                },
                saveAsReview() {
                    if (confirm("送審後不可再修改，確定要送審？")) {
                        this.form.saveType = "review";
                        this.submitForm();
                    }
                },
                submitForm() {
                    if (Array.isArray(this.form.items) && !this.form.items.length) {
                        alert("至少須有一筆明細");
                        return;
                    }

                    $(`.item-product-item`).each(function() {
                        $(this).rules("add", {
                            required: true,
                            unique: ".item-product-item",
                        });
                    });

                    $(`.item-qty`).each(function() {
                        $(this).rules("add", {
                            required: true,
                            digits: true,
                            min: function (element) {
                                let min = parseInt(element.getAttribute("min"));
                                if (min && min > 0) {
                                    return min;
                                }

                                return 1;
                            },
                            step: function (element) {
                                return parseInt(element.getAttribute("step"));
                            },
                            messages: {
                                digits: "只可輸入正整數",
                            },
                        });
                    });

                    this.$nextTick(() => {
                        $("#edit-form").submit();
                    });
                },
                getProductItems(supplierId) {
                    return axios({
                        method: "get",
                        url: "/backend/buyout-stock-in-requests/product-items/",
                        params: {
                            supplier_id: supplierId
                        },
                    })
                    .then(function(response) {
                        return response.data;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
                },
                expectedQty() {
                    let total = 0;

                    this.form.items.forEach(item => {
                        if (item.qty) {
                            total += parseInt(item.qty);
                        }
                    });

                    return total;
                },
            }
        });
    </script>
@endsection
