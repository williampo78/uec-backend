@extends('backend.master')

@section('title', '進貨退出單 新增資料')

@section('content')
    <div id="app" v-cloak>
        @if ($errors->any())
            <div ref="errorMessage" style="display: none;">
                {{ $errors->first('message') }}
            </div>
        @endif

        <div id="page-wrapper">
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header"><i class="fa-solid fa-plus"></i> 進貨退出單 新增資料</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">請輸入下列欄位資料</div>
                        <div class="panel-body">
                            <form id="create-form" method="post" action="{{ route('misc_stock_requests.store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">退出單號</label>
                                            <input type="text" class="form-control" :value="form.requestNo" disabled>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">庫別 <span class="text-red">*</span></label>
                                            <select2 class="form-control" :options="warehouses"
                                                v-model="form.warehouseId" name="warehouseId">
                                                <option disabled value=""></option>
                                            </select2>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">申請總量</label>
                                            <input type="text" class="form-control" :value="form.expectedQty" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">稅別 <span class="text-red">*</span></label>
                                            <select2 class="form-control" :options="taxes"
                                                v-model="form.tax" name="tax">
                                                <option disabled value=""></option>
                                            </select2>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">申請稅額</label>
                                            <input type="text" class="form-control" :value="form.expectedTaxAmount" disabled>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">申請總金額</label>
                                            <input type="text" class="form-control" :value="form.expectedAmount" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label">備註</label>
                                            <textarea class="form-control" rows="5" name="remark" v-model="form.remark"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <hr style="border-top: 1px solid gray;">

                                <h4>物流資訊</h4>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">收件人</label>
                                            <input type="text" class="form-control" name="shipToName" v-model="form.shipToName">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">聯絡電話</label>
                                            <input type="text" class="form-control" name="shipToMobile" v-model="form.shipToMobile">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label">地址</label>
                                            <input type="text" class="form-control" name="shipToAddress" v-model="form.shipToAddress">
                                        </div>
                                    </div>
                                </div>
                                <hr style="border-top: 1px solid gray;">

                                <h4>商品明細</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">商品序號</th>
                                                <th class="text-nowrap">商品名稱</th>
                                                <th class="text-nowrap">Item編號</th>
                                                <th class="text-nowrap">規格一</th>
                                                <th class="text-nowrap">規格二</th>
                                                <th class="text-nowrap">單價</th>
                                                <th class="text-nowrap">可退量</th>
                                                <th class="text-nowrap">申請量 <span class="text-red">*</span></th>
                                                <th class="text-nowrap">申請小計</th>
                                                <th class="text-nowrap">供應商</th>
                                                <th class="text-nowrap">功能</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in form.items" :key="index">
                                                <td>@{{ item.productNo }}</td>
                                                <td>@{{ item.productName }}</td>
                                                <td>@{{ item.itemNo }}</td>
                                                <td>@{{ item.spec1Value }}</td>
                                                <td>@{{ item.spec2Value }}</td>
                                                <td>@{{ item.sellingPrice }}</td>
                                                <td>@{{ item.stockQty }}</td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="number" class="form-control item-expected-qty"
                                                            :name="`items[${index}][expectedQty]`" v-model="item.expectedQty" min="1">
                                                    </div>
                                                </td>
                                                <td>@{{ item.expectedSubtotal }}</td>
                                                <td>@{{ item.supplierName }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-danger" @click="deleteItem(index)">
                                                        <i class="fa-solid fa-trash-can"></i> 刪除
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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

                                            @if ($share_role_auth['auth_create'])
                                                <button class="btn btn-success" type="button" :disabled="saveButton.isDisabled" @click="saveAsDraft">
                                                    <i class="fa-solid fa-floppy-disk"></i> 儲存草稿
                                                </button>

                                                <button class="btn btn-success" type="button" :disabled="saveButton.isDisabled" @click="saveAsReview">
                                                    <i class="fa-solid fa-floppy-disk"></i> 儲存並核單
                                                </button>
                                            @endif

                                            <a href="{{ route('misc_stock_requests') }}" class="btn btn-danger">
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
                    warehouseId: "",
                    expectedQty: "",
                    tax: "2",
                    expectedTaxAmount: "",
                    expectedAmount: "",
                    remark: "",
                    shipToName: "",
                    shipToMobile: "",
                    shipToAddress: "",
                    items: [],
                },
                saveButton: {
                    isDisabled: false,
                },
                warehouses: [],
                taxes: [],
            },
            created() {
                let payload = @json($payload);

                if (Array.isArray(payload.warehouses) && payload.warehouses.length) {
                    payload.warehouses.forEach(warehouse => {
                        this.warehouses.push({
                            text: warehouse.name,
                            id: warehouse.id,
                        });
                    });
                }

                if (payload.taxes) {
                    Object.entries(payload.taxes).forEach(([key, tax]) => {
                        this.taxes.push({
                            text: tax,
                            id: key,
                        });
                    });
                }
            },
            mounted() {
                let self = this;

                if (this.$refs.errorMessage) {
                    alert(this.$refs.errorMessage.innerText.trim());
                }

                // 驗證表單
                $("#create-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        self.saveButton.isDisabled = true;
                        form.submit();
                    },
                    rules: {
                        warehouseId: {
                            required: true,
                        },
                        tax: {
                            required: true,
                        },
                        remark: {
                            maxlength: 150,
                        },
                        shipToName: {
                            maxlength: 30,
                        },
                        shipToMobile: {
                            maxlength: 30,
                        },
                        shipToAddress: {
                            maxlength: 200,
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
                saveAsDraft() {
                    if (confirm("確定要儲存為草稿？")) {
                        this.form.saveType = "draft";
                        this.submitForm();
                    }
                },
                saveAsReview() {
                    if (confirm("單據送審後無法再修改，確定要送審？")) {
                        this.form.saveType = "review";
                        this.submitForm();
                    }
                },
                submitForm() {
                    if (Array.isArray(this.form.items) && !this.form.items.length) {
                        alert("至少需有一筆商品明細");
                        return;
                    }

                    $(`.item-expected-qty`).each(function() {
                        $(this).rules("add", {
                            required: true,
                            digits: true,
                            min: 1,
                            messages: {
                                min: "只可輸入正整數",
                                digits: "只可輸入正整數",
                            },
                        });
                    });

                    this.$nextTick(() => {
                        $("#create-form").submit();
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
