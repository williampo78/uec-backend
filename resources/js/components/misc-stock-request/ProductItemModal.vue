<template>
    <div
        class="modal fade"
        :id="modal.id"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myModalLabel"
    >
        <div class="modal-dialog">
            <div class="modal-content panel-primary">
                <div class="modal-header panel-heading">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        {{ modal.title }}
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="panel panel-default">
                        <div class="panel-heading form-horizontal">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label"
                                                >商品序號</label
                                            >
                                        </div>
                                        <div class="col-sm-10">
                                            <input
                                                class="form-control"
                                                placeholder="模糊查詢"
                                                v-model="form.productNo"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label"
                                                >商品名稱</label
                                            >
                                        </div>
                                        <div class="col-sm-10">
                                            <input
                                                class="form-control"
                                                placeholder="模糊查詢"
                                                v-model="form.productName"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label"
                                                >供應商</label
                                            >
                                        </div>
                                        <div class="col-sm-10">
                                            <select2
                                                class="form-control"
                                                :options="suppliers"
                                                v-model="form.supplierId"
                                            >
                                                <option
                                                    disabled
                                                    value=""
                                                ></option>
                                            </select2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label"
                                                >筆數限制</label
                                            >
                                        </div>
                                        <div class="col-sm-4">
                                            <input
                                                type="number"
                                                class="form-control"
                                                max="100"
                                                min="0"
                                                readonly
                                                v-model="form.limit"
                                            />
                                        </div>
                                        <div class="col-sm-6 text-right">
                                            <button
                                                type="button"
                                                class="btn btn-warning"
                                                @click="search"
                                            >
                                                <i
                                                    class="fa-solid fa-magnifying-glass"
                                                ></i>
                                                查詢
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <p class="text-primary">※ 僅能查到「可退量」 > 0的品項</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <button
                                        type="button"
                                        class="btn btn-success"
                                        @click="save"
                                    >
                                        <i class="fa-solid fa-floppy-disk"></i>
                                        儲存
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-success"
                                        @click="saveAndClose"
                                        data-dismiss="modal"
                                    >
                                        <i class="fa-solid fa-floppy-disk"></i>
                                        儲存並關閉
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-danger"
                                        data-dismiss="modal"
                                    >
                                        <i class="fa-solid fa-ban"></i> 取消
                                    </button>
                                </div>
                            </div>
                            <hr style="border-top: 1px solid gray" />
                            <div class="row">
                                <div class="col-sm-12">
                                    <button
                                        type="button"
                                        class="btn btn-primary"
                                        @click="checkAll"
                                    >
                                        <i class="fa-solid fa-check"></i> 全勾選
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-primary"
                                        @click="cancelAll"
                                    >
                                        <i class="fa-solid fa-xmark"></i> 全取消
                                    </button>
                                </div>
                            </div>
                            <br />

                            <div class="table-responsive">
                                <table
                                    class="table table-striped table-bordered table-hover"
                                    style="width: 100%"
                                >
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap">項次</th>
                                            <th class="text-nowrap"></th>
                                            <th class="text-nowrap">
                                                商品序號
                                            </th>
                                            <th class="text-nowrap">
                                                商品名稱
                                            </th>
                                            <th class="text-nowrap">
                                                Item編號
                                            </th>
                                            <th class="text-nowrap">規格一</th>
                                            <th class="text-nowrap">規格二</th>
                                            <th class="text-nowrap">供應商</th>
                                            <th class="text-nowrap">可退量</th>
                                            <th class="text-nowrap">單價</th>
                                            <th class="text-nowrap">單位</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                productItem, index
                                            ) in productItems"
                                            :key="index"
                                        >
                                            <td>{{ index + 1 }}</td>
                                            <td class="text-center">
                                                <input
                                                    type="checkbox"
                                                    style="
                                                        width: 20px;
                                                        height: 20px;
                                                        cursor: pointer;
                                                    "
                                                    v-model="
                                                        productItem.checked
                                                    "
                                                />
                                            </td>
                                            <td>{{ productItem.productNo }}</td>
                                            <td>
                                                {{ productItem.productName }}
                                            </td>
                                            <td>{{ productItem.itemNo }}</td>
                                            <td>
                                                {{ productItem.spec1Value }}
                                            </td>
                                            <td>
                                                {{ productItem.spec2Value }}
                                            </td>
                                            <td>
                                                {{ productItem.supplierName }}
                                            </td>
                                            <td>{{ productItem.stockQty }}</td>
                                            <td>
                                                {{ productItem.unitPriceForDisplay }}
                                            </td>
                                            <td>{{ productItem.uom }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        modal: {
            type: Object,
            validator(obj) {
                if (obj.hasOwnProperty("id") && obj.hasOwnProperty("title")) {
                    return true;
                }
            },
        },
    },
    data() {
        return {
            form: {
                productNo: "",
                productName: "",
                supplierId: "",
                limit: "100",
            },
            suppliers: [],
            productItems: [],
            excludeProductItemIds: [],
            warehouseId: "",
        };
    },
    async created() {
        let payload = await this.getOptions();

        if (!_.isEmpty(payload.suppliers)) {
            payload.suppliers.forEach((supplier) => {
                this.suppliers.push({
                    text: `【${supplier.display_number}】 ${supplier.name}`,
                    id: supplier.id,
                });
            });
        }
    },
    watch: {
        "modal.excludeProductItemIds"(value) {
            this.excludeProductItemIds = value;
        },
        "modal.warehouseId"(value) {
            this.warehouseId = value;
        },
    },
    mounted() {
        let self = this;

        $(`#${this.modal.id}`).on("hidden.bs.modal", function (e) {
            self.productItems = [];
        });
    },
    methods: {
        getOptions() {
            return axios({
                method: "get",
                url: "/backend/misc-stock-requests/product-item-modal/options",
            })
                .then(function (response) {
                    return response.data.payload;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        async search() {
            let requestPayload = {
                product_no: this.form.productNo,
                product_name: this.form.productName,
                supplier_id: this.form.supplierId,
                limit: this.form.limit,
                exclude_product_item_ids: this.excludeProductItemIds,
                warehouse_id: this.warehouseId,
            };

            let responsePayload = await this.getList(requestPayload);

            this.productItems = [];
            responsePayload.list.forEach((item) => {
                this.productItems.push({
                    id: item.id,
                    checked: false,
                    productNo: item.product_no,
                    productName: item.product_name,
                    itemNo: item.item_no,
                    spec1Value: item.spec_1_value,
                    spec2Value: item.spec_2_value,
                    supplierId: item.supplier_id,
                    supplierName: item.supplier_name,
                    stockQty: item.stock_qty,
                    unitPrice: item.unit_price,
                    unitPriceForDisplay: item.unit_price != null ? item.unit_price.toLocaleString('en-US') : "",
                    uom: item.uom,
                });
            });
        },
        getList(payload) {
            return axios({
                method: "get",
                url: "/backend/misc-stock-requests/product-item-modal/list",
                params: payload,
            })
                .then(function (response) {
                    return response.data.payload;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        checkAll() {
            this.productItems.forEach((productItem) => {
                productItem.checked = true;
            });
        },
        cancelAll() {
            this.productItems.forEach((productItem) => {
                productItem.checked = false;
            });
        },
        save() {
            let checkedProductItems = [];

            for (let i = this.productItems.length - 1; i >= 0; i--) {
                if (this.productItems[i].checked) {
                    checkedProductItems.unshift(this.productItems[i]);
                    this.excludeProductItemIds.push(this.productItems[i].id);
                    this.productItems.splice(i, 1);
                }
            }

            this.$emit("save", checkedProductItems);
        },
        saveAndClose() {
            this.save();
            $(`#${this.modal.id}`).hide();
        },
    },
};
</script>
