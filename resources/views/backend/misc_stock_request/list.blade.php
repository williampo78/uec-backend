@extends('backend.layouts.master')

@section('title', '進貨退出單')

@section('content')
    <div id="app" v-cloak>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header"><i class="fa-solid fa-list"></i> 進貨退出單</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <form id="search-form" class="form-horizontal" method="get" action="">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">申請日期</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <div class="col-sm-5">
                                                        <vue-flat-pickr
                                                            name="request_date_start"
                                                            :value.sync="form.requestDateStart"
                                                            :config="flatPickrConfig.requestDateStart"
                                                            @on-change="onRequestDateStartChange">
                                                        </vue-flat-pickr>
                                                    </div>
                                                    <div class="col-sm-2 text-center">
                                                        <label class="control-label">～</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <vue-flat-pickr
                                                            name="request_date_end"
                                                            :value.sync="form.requestDateEnd"
                                                            :config="flatPickrConfig.requestDateEnd"
                                                            @on-change="onRequestDateEndChange">
                                                        </vue-flat-pickr>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">退出單號</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="request_no" v-model="form.requestNo">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">狀態</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <select2 class="form-control" :options="requestStatuses"
                                                    v-model="form.requestStatus" name="request_status">
                                                    <option disabled value=""></option>
                                                </select2>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">實際出庫日</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <div class="col-sm-5">
                                                        <vue-flat-pickr
                                                            name="actual_date_start"
                                                            :value.sync="form.actualDateStart"
                                                            :config="flatPickrConfig.actualDateStart"
                                                            @on-change="onActualDateStartChange">
                                                        </vue-flat-pickr>
                                                    </div>
                                                    <div class="col-sm-2 text-center">
                                                        <label class="control-label">～</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <vue-flat-pickr
                                                            name="actual_date_end"
                                                            :value.sync="form.actualDateEnd"
                                                            :config="flatPickrConfig.actualDateEnd"
                                                            @on-change="onActualDateEndChange">
                                                        </vue-flat-pickr>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">商品序號</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="product_no" v-model="form.productNo">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">供應商</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <select2 class="form-control" :options="suppliers"
                                                    v-model="form.supplierId" name="supplier_id">
                                                    <option disabled value=""></option>
                                                </select2>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">查詢筆數上限</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="limit" v-model="form.limit" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3"></div>
                                            <div class="col-sm-9 text-right">
                                                <span v-if="auth.auth_query">
                                                    <button type="button" class="btn btn-warning" @click="search">
                                                        <i class="fa-solid fa-magnifying-glass"></i> 查詢
                                                    </button>

                                                    <button type="button" class="btn btn-danger" @click="resetForm">
                                                        <i class="fa-solid fa-eraser"></i> 清除
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-2" v-if="auth.auth_create">
                                    <a href="{{ route('misc_stock_requests.create') }}"
                                        class="btn btn-block btn-warning btn-sm">
                                        <i class="fa-solid fa-plus"></i> 新增
                                    </a>
                                </div>
                            </div>
                            <hr />
                            <div class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive">
                                <table class="table table-striped table-bordered table-hover" style="width:100%"
                                    id="table_list">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap">功能</th>
                                            <th class="text-nowrap">項次</th>
                                            <th class="text-nowrap">退出單號</th>
                                            <th class="text-nowrap">申請日期</th>
                                            <th class="text-nowrap">預計出庫日</th>
                                            <th class="text-nowrap">送審時間</th>
                                            <th class="text-nowrap">供應商家數</th>
                                            <th class="text-nowrap">核准家數</th>
                                            <th class="text-nowrap">駁回家數</th>
                                            <th class="text-nowrap">審核完成時間</th>
                                            <th class="text-nowrap">EDI拋出時間</th>
                                            <th class="text-nowrap">實際出庫日</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(request, index) in miscStockRequests" :key="index">
                                            <td class="text-nowrap">
                                                <span v-if="auth.auth_query">
                                                    <button type="button" class="btn btn-info btn-sm" title="檢視"
                                                        @click="showRequest(request.id)">
                                                        <i class="fa-solid fa-magnifying-glass"></i>
                                                    </button>
                                                </span>

                                                <span v-if="auth.auth_update && request.requestStatus == 'DRAFTED'">
                                                    <a class="btn btn-warning btn-sm"
                                                        :href="`${BASE_URI}/${request.id}/edit`">
                                                        編輯
                                                    </a>
                                                </span>

                                                <span v-if="auth.auth_update && request.requestStatus == 'COMPLETED' && !request.ediExportedAt">
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        @click="editExpectedDate(request.id)">
                                                        預出日
                                                    </button>
                                                </span>

                                                <span v-if="auth.auth_delete && request.requestStatus == 'DRAFTED'">
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        @click="deleteRequest(request.id, request.requestNo, $event)">
                                                        刪除
                                                    </button>
                                                </span>
                                            </td>
                                            <td>@{{ index + 1 }}</td>
                                            <td>@{{ request.requestNo }}</td>
                                            <td>@{{ request.requestDate }}</td>
                                            <td>@{{ request.expectedDate }}</td>
                                            <td>@{{ request.submittedAt }}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" @click="viewSuppliers(request.id, request.requestNo)">
                                                    @{{ request.totalSupCount }}
                                                </button>
                                            </td>
                                            <td>@{{ request.approvedSupCount }}</td>
                                            <td>@{{ request.rejectedSupCount }}</td>
                                            <td>@{{ request.approvedAt }}</td>
                                            <td>@{{ request.ediExportedAt }}</td>
                                            <td>@{{ request.actualDate }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('backend.misc_stock_request.show')
        @include('backend.misc_stock_request.supplier')
        @include('backend.misc_stock_request.expected_date')
    </div>
@endsection

@section('js')
    <script>
        const BASE_URI = '/backend/misc-stock-requests';

        let vm = new Vue({
            el: "#app",
            data: {
                form: {
                    requestDateStart: moment().subtract(2, 'months').format("YYYY-MM-DD"),
                    requestDateEnd: moment().format("YYYY-MM-DD"),
                    requestNo: "",
                    requestStatus: "",
                    actualDateStart: "",
                    actualDateEnd: "",
                    productNo: "",
                    supplierId: "",
                    limit: 500,
                },
                modal: {
                    show: {
                        id: "show-modal",
                        title: "進貨退出單 檢視資料",
                        requestNo: "",
                        warehouseName: "",
                        expectedQty: "",
                        requestDate: "",
                        submittedAt: "",
                        expectedDate: "",
                        tax: "",
                        expectedTaxAmount: "",
                        expectedAmount: "",
                        remark: "",
                        shipToName: "",
                        shipToMobile: "",
                        shipToAddress: "",
                        actualDate: "",
                        actualTaxAmount: "",
                        actualAmount: "",
                        items: [],
                    },
                    supplier: {
                        id: "supplier-modal",
                        title: "",
                        list: [],
                        requestId: "",
                        detail: {
                            isShow: false,
                            supplierName: "",
                            items: [],
                            reviewAt: "",
                            reviewerName: "",
                            reviewResult: "",
                            reviewRemark: "",
                        },
                    },
                    expectedDate: {
                        id: "expected-date-modal",
                        title: "進貨退出單-倉儲資訊更新",
                        requestId: "",
                        requestNo: "",
                        expectedDate: "",
                        shipToName: "",
                        shipToMobile: "",
                        shipToAddress: "",
                    },
                },
                flatPickrConfig: {
                    requestDateStart: {},
                    requestDateEnd: {},
                    actualDateStart: {},
                    actualDateEnd: {},
                    expectedDate: {},
                },
                requestStatuses: [],
                suppliers: [],
                miscStockRequests: [],
                auth: {},
                expectedDateValidator: {},
            },
            created() {
                this.BASE_URI = BASE_URI;
                let payload = @json($payload);

                if (payload.request_statuses) {
                    Object.entries(payload.request_statuses).forEach(([key, requestStatus]) => {
                        this.requestStatuses.push({
                            text: requestStatus,
                            id: key,
                        });
                    });
                }

                if (!_.isEmpty(payload.suppliers)) {
                    payload.suppliers.forEach(supplier => {
                        this.suppliers.push({
                            text: `【${supplier.display_number}】 ${supplier.name}`,
                            id: supplier.id,
                        });
                    });
                }

                if (payload.auth) {
                    this.auth = Object.assign({}, this.auth, payload.auth);
                }

                if (!_.isEmpty(payload.misc_stock_requests)) {
                    payload.misc_stock_requests.forEach(request => {
                        this.miscStockRequests.push({
                            id: request.id,
                            requestNo: request.request_no,
                            requestDate: moment(request.request_date).format("YYYY-MM-DD HH:mm"),
                            expectedDate: request.expected_date ? moment(request.expected_date).format("YYYY-MM-DD") : "",
                            submittedAt: request.submitted_at ? moment(request.submitted_at).format("YYYY-MM-DD HH:mm") : "",
                            totalSupCount: request.total_sup_count,
                            approvedSupCount: request.approved_sup_count,
                            rejectedSupCount: request.rejected_sup_count,
                            approvedAt: request.approved_at ? moment(request.approved_at).format("YYYY-MM-DD HH:mm") : "",
                            ediExportedAt: request.edi_exported_at ? moment(request.edi_exported_at).format("YYYY-MM-DD HH:mm") : "",
                            actualDate: request.actual_date ? moment(request.actual_date).format("YYYY-MM-DD") : "",
                            requestStatus: request.request_status,
                        });
                    });
                }

                this.setQueryParameters();
                this.initFlatPickrConfigs();
            },
            mounted() {
                let self = this;

                // 驗證表單
                $("#search-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        form.submit();
                    },
                    rules: {
                        requestDateEnd: {
                            compareDates: {
                                param: function() {
                                    return {
                                        date2: moment(self.form.requestDateStart).add(3, 'months'),
                                        sign: "<=",
                                    };
                                },
                                depends: function(element) {
                                    return self.form.requestDateStart;
                                },
                            },
                        },
                        actualDateEnd: {
                            compareDates: {
                                param: function() {
                                    return {
                                        date2: moment(self.form.actualDateStart).add(3, 'months'),
                                        sign: "<=",
                                    };
                                },
                                depends: function(element) {
                                    return self.form.actualDateStart;
                                },
                            },
                        },
                    },
                    messages: {
                        requestDateEnd: {
                            compareDates: "「申請日期」起訖最多不可超過3個月",
                        },
                        actualDateEnd: {
                            compareDates: "「實際出庫日」起訖最多不可超過3個月",
                        },
                    },
                    errorClass: "help-block",
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        if (element.closest(".input-group").length) {
                            element.closest(".input-group").parent().append(error);
                            return;
                        }

                        if (element.closest(".radio-inline").length) {
                            element.closest(".radio-inline").parent().append(error);
                            return;
                        }

                        if (element.is('select')) {
                            element.parent().append(error);
                            return;
                        }

                        error.insertAfter(element);
                    },
                    highlight: function(element, errorClass, validClass) {
                        if ($(element).closest('.input-group').length) {
                            $(element).closest(".input-group").parent().addClass("has-error");
                            return;
                        }

                        $(element).closest(".form-group").addClass("has-error");
                    },
                    success: function(label, element) {
                        if ($(element).closest('.input-group').length) {
                            $(element).closest(".input-group").parent().removeClass("has-error");
                            return;
                        }

                        $(element).closest(".form-group").removeClass("has-error");
                    },
                });

                // 驗證表單
                this.expectedDateValidator = $("#expected-date-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        // form.submit();
                        self.updateExpectedDate();
                    },
                    rules: {
                        expectedDate: {
                            required: true,
                        },
                        shipToName: {
                            required: true,
                            maxlength: 30,
                        },
                        shipToMobile: {
                            required: true,
                            maxlength: 30,
                        },
                        shipToAddress: {
                            required: true,
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

                        if (element.closest(".radio-inline").length) {
                            element.closest(".radio-inline").parent().append(error);
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
                initFlatPickrConfigs() {
                    this.flatPickrConfig.requestDateStart = {
                        dateFormat: "Y-m-d",
                        maxDate: this.form.requestDateEnd,
                    };

                    this.flatPickrConfig.requestDateEnd = {
                        dateFormat: "Y-m-d",
                        minDate: this.form.requestDateStart,
                    };

                    this.flatPickrConfig.actualDateStart = {
                        dateFormat: "Y-m-d",
                        maxDate: this.form.actualDateEnd,
                    };

                    this.flatPickrConfig.actualDateEnd = {
                        dateFormat: "Y-m-d",
                        minDate: this.form.actualDateStart,
                    };

                    this.flatPickrConfig.expectedDate = {
                        dateFormat: "Y-m-d",
                        minDate: moment().format("YYYY-MM-DD"),
                    };
                },
                onRequestDateStartChange(selectedDates, dateStr, instance) {
                    this.$set(this.flatPickrConfig.requestDateEnd, 'minDate', dateStr);
                },
                onRequestDateEndChange(selectedDates, dateStr, instance) {
                    this.$set(this.flatPickrConfig.requestDateStart, 'maxDate', dateStr);
                },
                onActualDateStartChange(selectedDates, dateStr, instance) {
                    this.$set(this.flatPickrConfig.actualDateEnd, 'minDate', dateStr);
                },
                onActualDateEndChange(selectedDates, dateStr, instance) {
                    this.$set(this.flatPickrConfig.actualDateStart, 'maxDate', dateStr);
                },
                search() {
                    $("#search-form").submit();
                },
                resetForm() {
                    let self = this;

                    Object.keys(self.form).forEach(function(key) {
                        if (['limit'].includes(key)) {
                            return;
                        }

                        self.form[key] = "";
                    });
                },
                setQueryParameters() {
                    const urlSearchParams = new URLSearchParams(window.location.search);
                    const params = Object.fromEntries(urlSearchParams.entries());

                    urlSearchParams.forEach((value, key) => {
                        let formKey = _.camelCase(key);

                        if (!this.form.hasOwnProperty(formKey)) {
                            return;
                        }

                        this.form[formKey] = value;
                    });
                },
                async showRequest(id) {
                    let request = await this.getRequest(id);

                    this.modal.show.requestNo = request.request_no;
                    this.modal.show.warehouseName = request.warehouse_name;
                    this.modal.show.expectedQty = request.expected_qty;
                    this.modal.show.requestDate = moment(request.request_date).format("YYYY-MM-DD HH:mm");
                    this.modal.show.submittedAt = request.submitted_at ? moment(request.submitted_at).format("YYYY-MM-DD HH:mm") : "";
                    this.modal.show.expectedDate = request.expected_date ? moment(request.expected_date).format("YYYY-MM-DD") : "";
                    this.modal.show.tax = request.tax;
                    this.modal.show.expectedTaxAmount = request.expected_tax_amount.toLocaleString('en-US');
                    this.modal.show.expectedAmount = request.expected_amount.toLocaleString('en-US');
                    this.modal.show.remark = request.remark;
                    this.modal.show.shipToName = request.ship_to_name;
                    this.modal.show.shipToMobile = request.ship_to_mobile;
                    this.modal.show.shipToAddress = request.ship_to_address;
                    this.modal.show.actualDate = request.actual_date ? moment(request.actual_date).format("YYYY-MM-DD") : "";
                    this.modal.show.actualTaxAmount = request.actual_tax_amount.toLocaleString('en-US');
                    this.modal.show.actualAmount = request.actual_amount.toLocaleString('en-US');

                    this.modal.show.items = [];
                    if (!_.isEmpty(request.items)) {
                        request.items.forEach(item => {
                            this.modal.show.items.push({
                                productNo: item.product_no,
                                productName: item.product_name,
                                itemNo: item.item_no,
                                spec1Value: item.spec_1_value,
                                spec2Value: item.spec_2_value,
                                unitPrice: item.unit_price.toLocaleString('en-US'),
                                stockQty: item.stock_qty,
                                expectedQty: item.expected_qty,
                                expectedSubtotal: item.expected_subtotal.toLocaleString('en-US'),
                                supplierName: item.supplier_name,
                                actualQty: item.actual_qty,
                                actualSubtotal: item.actual_subtotal.toLocaleString('en-US'),
                            });
                        });
                    }

                    $(`#${this.modal.show.id}`).modal('show');
                },
                getRequest(id) {
                    return axios({
                            method: "get",
                            url: `${BASE_URI}/${id}`,
                        })
                        .then(function(response) {
                            return response.data.payload.misc_stock_request;
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
                deleteRequest(id, requestNo, event) {
                    if (confirm(`確定要刪除《${requestNo}》？`)) {
                        axios({
                            method: "delete",
                            url: `${BASE_URI}/${id}`,
                        })
                        .then(function(response) {
                            let dataTable = $('#table_list').DataTable();
                            dataTable.row(event.target.closest("tr")).remove().draw();
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                    }
                },
                async viewSuppliers(id, requestNo) {
                    this.modal.supplier.title = `退貨單號【${requestNo}】`;
                    this.modal.supplier.detail.isShow = false;
                    let suppliers = await this.getSuppliers(id);

                    this.modal.supplier.requestId = id;
                    this.modal.supplier.list = [];
                    if (!_.isEmpty(suppliers)) {
                        suppliers.forEach(supplier => {
                            this.modal.supplier.list.push({
                                id: supplier.id,
                                name: supplier.name,
                                statusCode: supplier.status_code,
                                expectedQty: supplier.expected_qty,
                                expectedAmount: supplier.expected_amount.toLocaleString('en-US'),
                            });
                        });
                    }

                    $(`#${this.modal.supplier.id}`).modal('show');
                },
                getSuppliers(id) {
                    return axios({
                            method: "get",
                            url: `${BASE_URI}/${id}/supplier-modal/suppliers`,
                        })
                        .then(function(response) {
                            return response.data.payload.list;
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
                async viewRequestSupplierDetail(supplierId, supplierName) {
                    let detail = await this.getRequestSupplierDetail(supplierId);

                    this.modal.supplier.detail.supplierName = supplierName;
                    this.modal.supplier.detail.reviewAt = detail.review_at ? moment(detail.review_at).format("YYYY-MM-DD HH:mm") : "";
                    this.modal.supplier.detail.reviewerName = detail.reviewer_name;
                    this.modal.supplier.detail.reviewResult = detail.review_result;
                    this.modal.supplier.detail.reviewRemark = detail.review_remark;

                    this.modal.supplier.detail.items = [];
                    if (!_.isEmpty(detail.items)) {
                        detail.items.forEach(item => {
                            this.modal.supplier.detail.items.push({
                                productNo: item.product_no,
                                productName: item.product_name,
                                itemNo: item.item_no,
                                spec1Value: item.spec_1_value,
                                spec2Value: item.spec_2_value,
                                unitPrice: item.unit_price.toLocaleString('en-US'),
                                stockQty: item.stock_qty,
                                expectedQty: item.expected_qty,
                                expectedSubtotal: item.expected_subtotal.toLocaleString('en-US'),
                            });
                        });
                    }

                    this.modal.supplier.detail.isShow = true;
                },
                getRequestSupplierDetail(supplierId) {
                    return axios({
                            method: "get",
                            url: `${BASE_URI}/${this.modal.supplier.requestId}/supplier-modal/suppliers/${supplierId}`,
                        })
                        .then(function(response) {
                            return response.data.payload.detail;
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
                async editExpectedDate(id) {
                    let request = await this.getRequest(id);

                    this.modal.expectedDate.requestId = id;
                    this.modal.expectedDate.requestNo = request.request_no;
                    this.modal.expectedDate.expectedDate = request.expected_date;
                    this.modal.expectedDate.shipToName = request.ship_to_name;
                    this.modal.expectedDate.shipToMobile = request.ship_to_mobile;
                    this.modal.expectedDate.shipToAddress = request.ship_to_address;

                    this.expectedDateValidator.resetForm();
                    $("#expected-date-form").find(".has-error").removeClass("has-error");
                    $(`#${this.modal.expectedDate.id}`).modal('show');
                },
                saveExpectedDate() {
                    $("#expected-date-form").submit();
                },
                updateExpectedDate() {
                    axios({
                        method: "patch",
                        url: `${BASE_URI}/${this.modal.expectedDate.requestId}/expected-date`,
                        data: {
                            expected_date: this.modal.expectedDate.expectedDate,
                            ship_to_name: this.modal.expectedDate.shipToName,
                            ship_to_mobile: this.modal.expectedDate.shipToMobile,
                            ship_to_address: this.modal.expectedDate.shipToAddress,
                        },
                    })
                    .then((response) => {
                        alert('儲存成功！');
                    })
                    .catch((error) => {
                        console.log(error);

                        if (error.response) {
                            let data = error.response.data;
                            alert(data.message);
                        }
                    })
                    .finally(() => {
                        $(`#${this.modal.expectedDate.id}`).modal('hide');
                    });
                },
            },
        });
    </script>
@endsection
