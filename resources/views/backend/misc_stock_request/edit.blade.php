@extends('backend.layouts.master')

@section('title', '進貨退出單 編輯資料')

@section('css')
    <style>
        .modal-dialog {
            max-width: 100%;
        }
    </style>
@endsection

@section('content')
    <div id="app" v-cloak>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header"><i class="fa-solid fa-pencil"></i> 進貨退出單 編輯資料</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">請輸入下列欄位資料</div>
                        <div class="panel-body">
                            <form id="edit-form">
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
                                                v-model="form.warehouseId" name="warehouseId" @select2-selecting="onWarehouseSelecting">
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
                                                v-model="form.tax" name="tax" disabled>
                                                <option disabled value=""></option>
                                            </select2>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">申請稅額</label>
                                            <input type="text" class="form-control" :value="expectedTaxAmountForDisplay" disabled>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">申請總金額</label>
                                            <input type="text" class="form-control" :value="expectedAmountForDisplay" disabled>
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
                                                <td>@{{ item.unitPriceForDisplay }}</td>
                                                <td>@{{ item.stockQty }}</td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="number" class="form-control item-expected-qty"
                                                            :name="`items[${index}][expectedQty]`" v-model="item.expectedQty" min="1" :max="item.stockQty">
                                                    </div>
                                                </td>
                                                <td>@{{ expectedSubtotal(item) }}</td>
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
                                            @if ($share_role_auth['auth_update'])
                                                <button class="btn btn-success" type="button" :disabled="saveButton.isDisabled" @click="saveAsDraft">
                                                    <i class="fa-solid fa-floppy-disk"></i> 儲存草稿
                                                </button>

                                                <button class="btn btn-success" type="button" :disabled="saveButton.isDisabled" @click="saveAsReview">
                                                    <i class="fa-solid fa-floppy-disk"></i> 儲存並核單
                                                </button>
                                            @endif

                                            <button class="btn btn-danger" type="button" @click="cancel">
                                                <i class="fa-solid fa-ban"></i> 取消
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <product-item-modal :modal="modal.productItem" @save="saveProductItems"></product-item-modal>
    </div>
@endsection

@section('js')
    <script src="{{ mix('js/misc-stock-request/main.js') }}"></script>
    <script>
        const BASE_URI = '/backend/misc-stock-requests';

        let vm = new Vue({
            el: "#app",
            data: {
                form: {
                    saveType: "",
                    requestId: "",
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
                modal: {
                    productItem: {
                        id: "product-item-modal",
                        title: "新增品項",
                        excludeProductItemIds: [],
                        warehouseId: "",
                    },
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

                if (payload.miscStockRequest) {
                    this.form.requestId = payload.miscStockRequest.id;
                    this.form.requestNo = payload.miscStockRequest.requestNo;
                    this.form.warehouseId = String(payload.miscStockRequest.warehouseId);
                    this.form.tax = String(payload.miscStockRequest.tax);
                    this.form.remark = payload.miscStockRequest.remark;
                    this.form.shipToName = payload.miscStockRequest.shipToName;
                    this.form.shipToMobile = payload.miscStockRequest.shipToMobile;
                    this.form.shipToAddress = payload.miscStockRequest.shipToAddress;

                    if (Array.isArray(payload.miscStockRequest.items) && payload.miscStockRequest.items.length) {
                        payload.miscStockRequest.items.forEach(item => {
                            this.form.items.push({
                                productItemId: item.productItemId,
                                productNo: item.productNo,
                                productName: item.productName,
                                itemNo: item.itemNo,
                                spec1Value: item.spec1Value,
                                spec2Value: item.spec2Value,
                                unitPrice: item.unitPrice,
                                unitPriceForDisplay: item.unitPrice != null ? item.unitPrice.toLocaleString('en-US') : "",
                                stockQty: item.stockQty,
                                expectedQty: item.expectedQty,
                                supplierId: item.supplierId,
                                supplierName: item.supplierName,
                            });
                        });
                    }
                }
            },
            mounted() {
                let self = this;

                // 驗證表單
                $("#edit-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        self.saveButton.isDisabled = true;
                        // form.submit();
                        self.updateRequest();
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
            computed: {
                expectedAmountForDisplay() {
                    return this.form.expectedAmount != null ? this.form.expectedAmount.toLocaleString('en-US') : "";
                },
                expectedTaxAmountForDisplay() {
                    return this.form.expectedTaxAmount != null ? this.form.expectedTaxAmount.toLocaleString('en-US') : "";
                },
            },
            watch: {
                "form.warehouseId"(warehouseId) {
                    this.modal.productItem.warehouseId = warehouseId;
                },
                "form.items": {
                    handler(items) {
                        let expectedQty = 0;
                        let expectedAmount = 0;

                        items.forEach(item => {
                            if (item.expectedQty) {
                                let itemUnitPrice = parseFloat(item.unitPrice);
                                let itemExpectedQty = parseInt(item.expectedQty);

                                expectedQty += itemExpectedQty;
                                expectedAmount += _.round(itemUnitPrice * itemExpectedQty);
                            }
                        });

                        this.form.expectedQty = expectedQty;
                        this.form.expectedTaxAmount = this.calculateTaxAmount(this.form.tax, expectedAmount);
                        this.form.expectedAmount = expectedAmount;
                    },
                    deep: true,
                },
            },
            methods: {
                addItem() {
                    if (!this.form.warehouseId) {
                        alert("尚未指定「庫別」，不允許新增商品明細！");
                        return;
                    }

                    this.modal.productItem.excludeProductItemIds = [];
                    this.form.items.forEach(item => {
                        this.modal.productItem.excludeProductItemIds.push(item.productItemId);
                    });

                    $('#product-item-modal').modal('show');
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

                    if (this.form.items.some(item => item.unitPrice == null)) {
                        alert("商品明細中，不可存在無單價的商品");
                        return;
                    }

                    if (this.form.items.some(item => parseInt(item.stockQty) < 1)) {
                        alert("商品明細中，不可存在可退量小於1的商品");
                        return;
                    }

                    $(`.item-expected-qty`).each(function() {
                        $(this).rules("add", {
                            required: true,
                            digits: true,
                            max: function (element) {
                                return parseInt(element.getAttribute("max"));
                            },
                            min: 1,
                            messages: {
                                min: "只可輸入正整數",
                                digits: "只可輸入正整數",
                            },
                        });
                    });

                    this.$nextTick(() => {
                        $("#edit-form").submit();
                    });
                },
                cancel() {
                    if (confirm("確定放棄存檔？")) {
                        window.location.href = BASE_URI;
                    }
                },
                // 計算申請小計
                expectedSubtotal(item) {
                    let subtotal = 0;

                    if (item.expectedQty) {
                        subtotal = _.round(parseFloat(item.unitPrice) * parseInt(item.expectedQty));
                    }

                    return subtotal.toLocaleString('en-US');
                },
                // 儲存商品明細
                saveProductItems(productItems) {
                    productItems.forEach(productItem => {
                        this.form.items.push({
                            productItemId: productItem.id,
                            productNo: productItem.productNo,
                            productName: productItem.productName,
                            itemNo: productItem.itemNo,
                            spec1Value: productItem.spec1Value,
                            spec2Value: productItem.spec2Value,
                            unitPrice: productItem.unitPrice,
                            unitPriceForDisplay: productItem.unitPriceForDisplay,
                            stockQty: productItem.stockQty,
                            expectedQty: "",
                            supplierId: productItem.supplierId,
                            supplierName: productItem.supplierName,
                        });
                    });
                },
                // 計算稅額
                calculateTaxAmount(tax, amount) {
                    if (!tax || !amount) {
                        return;
                    }

                    let nontaxAmount = 0;

                    // 計算未稅金額
                    switch (tax) {
                        // 免稅
                        case '0':
                            nontaxAmount = amount;
                            break;

                        // 應稅
                        // case '1':
                        //     break;

                        // 應稅內含
                        case '2':
                            nontaxAmount = _.round(amount / ((100 + 5) / 100));
                            break;

                        // 零稅率
                        case '3':
                            nontaxAmount = _.round(amount / ((100 + 0) / 100));
                            break;

                        default:
                            nontaxAmount = amount;
                            break;
                    }

                    return _.round(amount - nontaxAmount);
                },
                // 選擇庫別
                onWarehouseSelecting(event) {
                    let errorMessage = "";
                    // 已有商品明細
                    if (this.form.items.length) {
                        errorMessage += "請先刪除「商品明細」，才能切換「庫別」！\n"
                    }

                    if (errorMessage) {
                        event.preventDefault();
                        alert(errorMessage);
                    }
                },
                updateRequest() {
                    axios({
                        method: "put",
                        url: `${BASE_URI}/${this.form.requestId}`,
                        data: {
                            saveType: this.form.saveType,
                            warehouseId: this.form.warehouseId,
                            remark: this.form.remark,
                            shipToName: this.form.shipToName,
                            shipToMobile: this.form.shipToMobile,
                            shipToAddress: this.form.shipToAddress,
                            items: this.form.items,
                        },
                    })
                    .then((response) => {
                        if (this.form.saveType == 'draft') {
                            alert('儲存草稿成功！');
                        } else {
                            alert('儲存成功，進貨退出單送審中！');
                        }

                        window.location.href = BASE_URI;
                    })
                    .catch((error) => {
                        console.log(error);

                        if (error.response) {
                            let data = error.response.data;
                            alert(data.message);
                        }
                    })
                    .finally(() => {
                        this.saveButton.isDisabled = false;
                    });
                },
            }
        });
    </script>
@endsection
