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
                                                >供應商</label
                                            >
                                        </div>
                                        <div class="col-sm-10">
                                            <div v-if="modal.action == 'edit'">
                                                <input type="hidden" v-model="form.supplierId">
                                                <input class="form-control"  v-model="form.supplierName" disabled/>
                                            </div>
                                            <div v-if="modal.action == 'create'">
                                                <select2
                                                class="form-control"
                                                :options="suppliers"
                                                v-model="form.supplierId"
                                                :allow-clear="false"
                                                :disabled="supplier.isDisabled">
                                                <option
                                                    disabled
                                                    value=""
                                                ></option>
                                            </select2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                            </div>

                            <div class="row">
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
                                                placeholder="模糊查詢，至少輸入4碼"
                                                v-model="form.productName"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label"
                                                >售價</label
                                            >
                                        </div>
                                        <div class="col-sm-4">
                                            <input
                                                type="number"
                                                class="form-control"
                                                v-model="form.sellingPriceMin"
                                            />
                                        </div>
                                        <div class="col-sm-2 text-center">
                                            <label class="control-label"
                                                >~</label
                                            >
                                        </div>
                                        <div class="col-sm-4">
                                            <input
                                                type="number"
                                                class="form-control"
                                                v-model="form.sellingPriceMax"
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
                                                >建檔日</label
                                            >
                                        </div>
                                        <div class="col-sm-4">
                                            <div
                                                class="input-group"
                                                :id="`${modal.id}-created-at-start-flatpickr`"
                                            >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    autocomplete="off"
                                                    data-input
                                                    v-model="
                                                        form.createdAtStart
                                                    "
                                                />
                                                <span
                                                    class="input-group-btn"
                                                    data-toggle
                                                >
                                                    <button
                                                        class="btn btn-default"
                                                        type="button"
                                                    >
                                                        <i
                                                            class="fa-solid fa-calendar-days"
                                                        ></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 text-center">
                                            <label class="control-label"
                                                >~</label
                                            >
                                        </div>
                                        <div class="col-sm-4">
                                            <div
                                                class="input-group"
                                                :id="`${modal.id}-created-at-end-flatpickr`"
                                            >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    autocomplete="off"
                                                    data-input
                                                    v-model="form.createdAtEnd"
                                                />
                                                <span
                                                    class="input-group-btn"
                                                    data-toggle
                                                >
                                                    <button
                                                        class="btn btn-default"
                                                        type="button"
                                                    >
                                                        <i
                                                            class="fa-solid fa-calendar-days"
                                                        ></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label"
                                                >上架日起</label
                                            >
                                        </div>
                                        <div class="col-sm-4">
                                            <div
                                                class="input-group"
                                                :id="`${modal.id}-start-launched-at-start-flatpickr`"
                                            >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    autocomplete="off"
                                                    data-input
                                                    v-model="
                                                        form.startLaunchedAtStart
                                                    "
                                                />
                                                <span
                                                    class="input-group-btn"
                                                    data-toggle
                                                >
                                                    <button
                                                        class="btn btn-default"
                                                        type="button"
                                                    >
                                                        <i
                                                            class="fa-solid fa-calendar-days"
                                                        ></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 text-center">
                                            <label class="control-label"
                                                >~</label
                                            >
                                        </div>
                                        <div class="col-sm-4">
                                            <div
                                                class="input-group"
                                                :id="`${modal.id}-start-launched-at-end-flatpickr`"
                                            >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    autocomplete="off"
                                                    data-input
                                                    v-model="
                                                        form.startLaunchedAtEnd
                                                    "
                                                />
                                                <span
                                                    class="input-group-btn"
                                                    data-toggle
                                                >
                                                    <button
                                                        class="btn btn-default"
                                                        type="button"
                                                    >
                                                        <i
                                                            class="fa-solid fa-calendar-days"
                                                        ></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label"
                                                >商品類型</label
                                            >
                                        </div>
                                        <div class="col-sm-10">
                                            <select2
                                                class="form-control"
                                                :options="productTypeOptions"
                                                v-model="form.productType"
                                                :allow-clear="false"
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
                                                >前台分類</label
                                            >
                                        </div>
                                        <div class="col-sm-10">
                                            <select2
                                                class="form-control"
                                                :options="
                                                    webCategoryHierarchies
                                                "
                                                v-model="
                                                    form.webCategoryHierarchyId
                                                "
                                            >
                                                <option
                                                    disabled
                                                    value=""
                                                ></option>
                                            </select2>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label class="control-label"
                                                >筆數限制</label
                                            >
                                        </div>
                                        <div class="col-sm-4">
                                            <select2
                                                class="form-control"
                                                :options="limitOptions"
                                                v-model="form.limit"
                                                :allow-clear="false"
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
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-10 text-right">
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

                        <!-- Table list -->
                        <div class="panel-body">
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
                                                售價(含稅)
                                            </th>
                                            <th class="text-nowrap">
                                                開賣日期
                                            </th>
                                            <th class="text-nowrap">
                                                上架狀態
                                            </th>
                                            <th class="text-nowrap">毛利(%)</th>
                                            <th class="text-nowrap">
                                                商品類型
                                            </th>
                                            <th class="text-nowrap">
                                                前台分類
                                            </th>
                                            <th class="text-nowrap">供應商</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(product, index) in products"
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
                                                    v-model="product.checked"
                                                />
                                            </td>
                                            <td>{{ product.productNo }}</td>
                                            <td>{{ product.productName }}</td>
                                            <td>{{ product.sellingPrice }}</td>
                                            <td>
                                                {{ product.startSellingAt }}
                                            </td>
                                            <td>
                                                {{ product.launchStatus }}
                                            </td>
                                            <td>{{ product.grossMargin }}</td>
                                            <td>{{ product.productType }}</td>
                                            <td>
                                                {{
                                                    product.webCategoryHierarchy
                                                }}
                                            </td>
                                            <td>{{ product.supplier }}</td>
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
                supplierId: "all",
                productNo: "",
                productName: "",
                sellingPriceMin: "",
                sellingPriceMax: "",
                createdAtStart: "",
                createdAtEnd: "",
                startLaunchedAtStart: "",
                startLaunchedAtEnd: "",
                productType: "",
                webCategoryHierarchyId: "",
                limit: 100,
                stockType: "",
                excludeProductIds: [],
            },
            supplier: {
                isDisabled: false,
            },
            suppliers: [
                {
                    text: "全部",
                    id: "all",
                },
            ],
            productTypeOptions: [],
            webCategoryHierarchies: [],
            products: [],
            limitOptions: [100, 300, 500],
        };
    },
    async created() {
        let self = this;

        if (this.modal.supplier) {
            if (typeof this.modal.supplier.isDisabled !== "undefined") {
                this.supplier.isDisabled = this.modal.supplier.isDisabled;
            }
        }

        if (this.modal.productType) {
            if (this.modal.productType.id) {
                this.form.productType = this.modal.productType.id;
            }
        }

        let data = await this.getOptions();

        if (data.suppliers) {
            data.suppliers.forEach((supplier) => {
                this.suppliers.push({
                    text: `【${supplier.display_number}】 ${supplier.name}`,
                    id: supplier.id,
                });
                if(this.form.supplierId == supplier.id){
                    this.form.supplierName = supplier.name ;
                }
            });
        }

        if (data.product_type_options) {
            Object.entries(data.product_type_options).forEach(
                ([key, productTypeOption]) => {
                    this.productTypeOptions.push({
                        text: productTypeOption,
                        id: key,
                    });
                }
            );
        }

        if (data.web_category_hierarchies) {
            data.web_category_hierarchies.forEach((webCategoryHierarchy) => {
                this.webCategoryHierarchies.push({
                    text: webCategoryHierarchy.category_name,
                    id: webCategoryHierarchy.id,
                });
            });
        }

        if (this.modal.productType) {
            if (
                this.modal.productType.includeOptions &&
                this.modal.productType.includeOptions.length
            ) {
                this.productTypeOptions = this.productTypeOptions.filter(
                    function (option) {
                        return self.modal.productType.includeOptions.includes(
                            option.id
                        );
                    }
                );
            }
        }
    },
    computed: {
        modalSupplierId() {
            return this.modal.supplier.id;
        },
        modalStockType() {
            return this.modal.stockType;
        },
        modalExcludeProductIds() {
            return this.modal.excludeProductIds;
        },
    },
    watch: {
        modalSupplierId(newValue, oldValue) {
            this.form.supplierId = newValue;
        },
        modalStockType(newValue, oldValue) {
            this.form.stockType = newValue;
        },
        modalExcludeProductIds(newValue, oldValue) {
            this.form.excludeProductIds = newValue;
        },
    },
    mounted() {
        let self = this;

        let createdAtStartFlatpickr = flatpickr(
            `#${this.modal.id}-created-at-start-flatpickr`,
            {
                dateFormat: "Y-m-d",
                maxDate: this.form.createdAtEnd,
                onChange: function (selectedDates, dateStr, instance) {
                    createdAtEndFlatpickr.set("minDate", dateStr);
                },
            }
        );

        let createdAtEndFlatpickr = flatpickr(
            `#${this.modal.id}-created-at-end-flatpickr`,
            {
                dateFormat: "Y-m-d",
                minDate: this.form.createdAtStart,
                onChange: function (selectedDates, dateStr, instance) {
                    createdAtStartFlatpickr.set("maxDate", dateStr);
                },
            }
        );

        let startLaunchedAtStartFlatpickr = flatpickr(
            `#${this.modal.id}-start-launched-at-start-flatpickr`,
            {
                dateFormat: "Y-m-d",
                maxDate: this.form.startLaunchedAtEnd,
                onChange: function (selectedDates, dateStr, instance) {
                    startLaunchedAtEndFlatpickr.set("minDate", dateStr);
                },
            }
        );

        let startLaunchedAtEndFlatpickr = flatpickr(
            `#${this.modal.id}-start-launched-at-end-flatpickr`,
            {
                dateFormat: "Y-m-d",
                minDate: this.form.startLaunchedAtStart,
                onChange: function (selectedDates, dateStr, instance) {
                    startLaunchedAtStartFlatpickr.set("maxDate", dateStr);
                },
            }
        );

        $(`#${this.modal.id}`).on('hidden.bs.modal', function (e) {
            self.products = [];
        });
    },
    methods: {
        getOptions() {
            return axios({
                method: "get",
                url: "/backend/promotional-campaign-cart-v2/product-modal/options",
            })
                .then(function (response) {
                    return response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        async search() {
            let stockTypes = this.form.stockType.split("_");
            let data = {
                supplier_id: this.form.supplierId,
                product_no: this.form.productNo,
                product_name: this.form.productName,
                selling_price_min: this.form.sellingPriceMin,
                selling_price_max: this.form.sellingPriceMax,
                created_at_start: this.form.createdAtStart,
                created_at_end: this.form.createdAtEnd,
                start_launched_at_start: this.form.startLaunchedAtStart,
                start_launched_at_end: this.form.startLaunchedAtEnd,
                product_type: this.form.productType,
                web_category_hierarchy_id: this.form.webCategoryHierarchyId,
                limit: this.form.limit,
                stock_types: stockTypes,
                exclude_product_ids: this.form.excludeProductIds,
            };

            let products = await this.getProducts(data);

            this.products = [];
            products.forEach((product) => {
                this.products.push({
                    id: product.id,
                    checked: false,
                    productNo: product.product_no,
                    productName: product.product_name,
                    sellingPrice: product.selling_price,
                    startLaunchedAt: product.start_launched_at,
                    startSellingAt: product.start_selling_at,
                    launchStatus: product.launch_status,
                    grossMargin: product.gross_margin,
                    productType: product.product_type,
                    webCategoryHierarchy: product.web_category_hierarchy,
                    supplier: product.supplier,
                    stockType: product.stock_type,
                    stockQty: product.stock_qty,
                });
            });
        },
        getProducts(data) {
            return axios({
                method: "POST",
                url: "/backend/promotional-campaign-cart-v2/product-modal/products",
                data: data,
            })
                .then(function (response) {
                    return response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        checkAll() {
            this.products.forEach((product) => {
                product.checked = true;
            });
        },
        cancelAll() {
            this.products.forEach((product) => {
                product.checked = false;
            });
        },
        save() {
            let checkedProducts = [];

            for (let i = this.products.length - 1; i >= 0; i--) {
                if (this.products[i].checked) {
                    checkedProducts.unshift(this.products[i]);
                    this.form.excludeProductIds.push(this.products[i].id);
                    this.products.splice(i, 1);
                }
            }

            this.$emit('save', checkedProducts);
        },
        saveAndClose() {
            this.save();
            $(`#${this.modal.id}`).hide();
        },
    },
};
</script>
