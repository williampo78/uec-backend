@extends('backend.master')

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
                                                        <vue-flat-pickr :setting="form.requestDateStart" @on-change="onRequestDateStartChange"></vue-flat-pickr>
                                                    </div>
                                                    <div class="col-sm-2 text-center">
                                                        <label class="control-label">～</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <vue-flat-pickr :setting="form.requestDateEnd" @on-change="onRequestDateEndChange"></vue-flat-pickr>
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
                                                <input type="text" class="form-control" name="requestNo" v-model="form.requestNo">
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
                                                    v-model="form.statusCode" name="statusCode">
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
                                                        <vue-flat-pickr :setting="form.actualDateStart" @on-change="onActualDateStartChange"></vue-flat-pickr>
                                                    </div>
                                                    <div class="col-sm-2 text-center">
                                                        <label class="control-label">～</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <vue-flat-pickr :setting="form.actualDateEnd" @on-change="onActualDateEndChange"></vue-flat-pickr>
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
                                                <input type="text" class="form-control" name="productNo" v-model="form.productNo">
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
                                                    v-model="form.supplierId" name="supplierId">
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
                                                <button type="button" class="btn btn-primary btn-sm" @click="viewRequestSuppliers(request.id, request.requestNo)">
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
    </div>
@endsection

@section('js')
    <script>
        const BASE_URI = '/backend/misc-stock-requests';

        let vm = new Vue({
            el: "#app",
            data: {
                form: {
                    requestDateStart: {
                        name: "requestDateStart",
                        date: moment().subtract(2, 'months').format("YYYY-MM-DD"),
                        config: {},
                    },
                    requestDateEnd: {
                        name: "requestDateEnd",
                        date: moment().format("YYYY-MM-DD"),
                        config: {},
                    },
                    requestNo: "",
                    statusCode: "",
                    actualDateStart: {
                        name: "actualDateStart",
                        date: "",
                        config: {},
                    },
                    actualDateEnd: {
                        name: "actualDateEnd",
                        date: "",
                        config: {},
                    },
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
                },
                requestStatuses: [],
                suppliers: [],
                miscStockRequests: [],
                auth: {},
            },
            created() {
                this.BASE_URI = BASE_URI;
                let payload = @json($payload);

                if (payload.requestStatuses) {
                    Object.entries(payload.requestStatuses).forEach(([key, statusCode]) => {
                        this.requestStatuses.push({
                            text: statusCode,
                            id: key,
                        });
                    });
                }

                if (Array.isArray(payload.suppliers) && payload.suppliers.length) {
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

                if (Array.isArray(payload.miscStockRequests) && payload.miscStockRequests.length) {
                    payload.miscStockRequests.forEach(request => {
                        this.miscStockRequests.push({
                            id: request.id,
                            requestNo: request.requestNo,
                            requestDate: moment(request.requestDate).format("YYYY-MM-DD HH:mm"),
                            expectedDate: request.expectedDate ? moment(request.expectedDate).format("YYYY-MM-DD") : "",
                            submittedAt: request.submittedAt ? moment(request.submittedAt).format("YYYY-MM-DD HH:mm") : "",
                            totalSupCount: request.totalSupCount,
                            approvedSupCount: request.approvedSupCount,
                            rejectedSupCount: request.rejectedSupCount,
                            approvedAt: request.approvedAt ? moment(request.approvedAt).format("YYYY-MM-DD HH:mm") : "",
                            ediExportedAt: request.ediExportedAt ? moment(request.ediExportedAt).format("YYYY-MM-DD HH:mm") : "",
                            actualDate: request.actualDate ? moment(request.actualDate).format("YYYY-MM-DD") : "",
                            requestStatus: request.requestStatus,
                        });
                    });
                }

                this.initFlatPickrConfigs();
                this.setQueryParameters();
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
                                        date2: moment(self.form.requestDateStart.date).add(3, 'months'),
                                        sign: "<=",
                                    };
                                },
                                depends: function(element) {
                                    return self.form.requestDateStart.date;
                                },
                            },
                        },
                        actualDateEnd: {
                            compareDates: {
                                param: function() {
                                    return {
                                        date2: moment(self.form.actualDateStart.date).add(3, 'months'),
                                        sign: "<=",
                                    };
                                },
                                depends: function(element) {
                                    return self.form.actualDateStart.date;
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
            },
            methods: {
                initFlatPickrConfigs() {
                    this.form.requestDateStart.config = {
                        dateFormat: "Y-m-d",
                        maxDate: this.form.requestDateEnd.date,
                    };

                    this.form.requestDateEnd.config = {
                        dateFormat: "Y-m-d",
                        minDate: this.form.requestDateStart.date,
                    };

                    this.form.actualDateStart.config = {
                        dateFormat: "Y-m-d",
                        maxDate: this.form.actualDateEnd.date,
                    };

                    this.form.actualDateEnd.config = {
                        dateFormat: "Y-m-d",
                        minDate: this.form.actualDateStart.date,
                    };
                },
                onRequestDateStartChange(selectedDates, dateStr, instance) {
                    this.$set(this.form.requestDateEnd.config, 'minDate', dateStr);
                },
                onRequestDateEndChange(selectedDates, dateStr, instance) {
                    this.$set(this.form.requestDateStart.config, 'maxDate', dateStr);
                },
                onActualDateStartChange(selectedDates, dateStr, instance) {
                    this.$set(this.form.actualDateEnd.config, 'minDate', dateStr);
                },
                onActualDateEndChange(selectedDates, dateStr, instance) {
                    this.$set(this.form.actualDateStart.config, 'maxDate', dateStr);
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

                        if (['requestDateStart', 'requestDateEnd', 'actualDateStart', 'actualDateEnd'].includes(key)) {
                            self.form[key].date = "";
                        } else {
                            self.form[key] = "";
                        }
                    });
                },
                setQueryParameters() {
                    const urlSearchParams = new URLSearchParams(window.location.search);
                    const params = Object.fromEntries(urlSearchParams.entries());

                    urlSearchParams.forEach((value, key) => {
                        if (!this.form.hasOwnProperty(key)) {
                            return;
                        }

                        if (['requestDateStart', 'requestDateEnd', 'actualDateStart', 'actualDateEnd'].includes(key)) {
                            this.form[key].date = value;
                        } else {
                            this.form[key] = value;
                        }
                    });
                },
                async showRequest(id) {
                    let request = await this.getRequest(id);

                    this.modal.show.requestNo = request.requestNo;
                    this.modal.show.warehouseName = request.warehouseName;
                    this.modal.show.expectedQty = request.expectedQty;
                    this.modal.show.requestDate = moment(request.requestDate).format("YYYY-MM-DD HH:mm");
                    this.modal.show.submittedAt = request.submittedAt ? moment(request.submittedAt).format("YYYY-MM-DD HH:mm") : "";
                    this.modal.show.expectedDate = request.expectedDate ? moment(request.expectedDate).format("YYYY-MM-DD") : "";
                    this.modal.show.tax = request.tax;
                    this.modal.show.expectedTaxAmount = request.expectedTaxAmount.toLocaleString('en-US');
                    this.modal.show.expectedAmount = request.expectedAmount.toLocaleString('en-US');
                    this.modal.show.remark = request.remark;
                    this.modal.show.shipToName = request.shipToName;
                    this.modal.show.shipToMobile = request.shipToMobile;
                    this.modal.show.shipToAddress = request.shipToAddress;
                    this.modal.show.actualDate = request.actualDate ? moment(request.actualDate).format("YYYY-MM-DD") : "";
                    this.modal.show.actualTaxAmount = request.actualTaxAmount.toLocaleString('en-US');
                    this.modal.show.actualAmount = request.actualAmount.toLocaleString('en-US');

                    this.modal.show.items = [];
                    if (Array.isArray(request.items) && request.items.length) {
                        request.items.forEach(item => {
                            this.modal.show.items.push({
                                productNo: item.productNo,
                                productName: item.productName,
                                itemNo: item.itemNo,
                                spec1Value: item.spec1Value,
                                spec2Value: item.spec2Value,
                                unitPrice: item.unitPrice.toLocaleString('en-US'),
                                stockQty: item.stockQty,
                                expectedQty: item.expectedQty,
                                expectedSubtotal: item.expectedSubtotal.toLocaleString('en-US'),
                                supplierName: item.supplierName,
                                actualQty: item.actualQty,
                                actualSubtotal: item.actualSubtotal.toLocaleString('en-US'),
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
                            return response.data.payload.miscStockRequest;
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
                async viewRequestSuppliers(id, requestNo) {
                    this.modal.supplier.title = `退貨單號【${requestNo}】`;
                    this.modal.supplier.detail.isShow = false;
                    let requestSuppliers = await this.getRequestSuppliers(id);

                    this.modal.supplier.list = [];
                    if (Array.isArray(requestSuppliers) && requestSuppliers.length) {
                        requestSuppliers.forEach(requestSupplier => {
                            this.modal.supplier.list.push({
                                id: requestSupplier.id,
                                supplierName: requestSupplier.supplierName,
                                statusCode: requestSupplier.statusCode,
                                expectedQty: requestSupplier.expectedQty,
                                expectedAmount: requestSupplier.expectedAmount.toLocaleString('en-US'),
                            });
                        });
                    }

                    $(`#${this.modal.supplier.id}`).modal('show');
                },
                getRequestSuppliers(id) {
                    return axios({
                            method: "get",
                            url: `${BASE_URI}/${id}/supplier-modal/list`,
                        })
                        .then(function(response) {
                            return response.data.payload.list;
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
                async viewRequestSupplierDetail(requestSupplierId, supplierName) {
                    let detail = await this.getRequestSupplierDetail(requestSupplierId);

                    this.modal.supplier.detail.supplierName = supplierName;
                    this.modal.supplier.detail.reviewAt = detail.reviewAt ? moment(detail.reviewAt).format("YYYY-MM-DD HH:mm") : "";
                    this.modal.supplier.detail.reviewerName = detail.reviewerName;
                    this.modal.supplier.detail.reviewResult = detail.reviewResult;
                    this.modal.supplier.detail.reviewRemark = detail.reviewRemark;

                    this.modal.supplier.detail.items = [];
                    if (Array.isArray(detail.items) && detail.items.length) {
                        detail.items.forEach(item => {
                            this.modal.supplier.detail.items.push({
                                productNo: item.productNo,
                                productName: item.productName,
                                itemNo: item.itemNo,
                                spec1Value: item.spec1Value,
                                spec2Value: item.spec2Value,
                                unitPrice: item.unitPrice.toLocaleString('en-US'),
                                stockQty: item.stockQty,
                                expectedQty: item.expectedQty,
                                expectedSubtotal: item.expectedSubtotal.toLocaleString('en-US'),
                            });
                        });
                    }

                    this.modal.supplier.detail.isShow = true;
                },
                getRequestSupplierDetail(requestSupplierId) {
                    return axios({
                            method: "get",
                            url: `${BASE_URI}/supplier-modal/list/${requestSupplierId}`,
                        })
                        .then(function(response) {
                            return response.data.payload.detail;
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
            },
        });
    </script>
@endsection
