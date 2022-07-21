@extends('backend.layouts.master')

@section('title', '進貨退出單審核')

@section('content')
    <div id="app" v-cloak>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header"><i class="fa-solid fa-list"></i> 進貨退出單審核</h1>
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
                                                <label class="control-label">送審時間</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <div class="col-sm-5">
                                                        <vue-flat-pickr
                                                            name="submitted_at_start"
                                                            :value.sync="form.submittedAtStart"
                                                            :config="flatPickrConfig.submittedAtStart"
                                                            @on-change="onSubmittedAtStartChange">
                                                        </vue-flat-pickr>
                                                    </div>
                                                    <div class="col-sm-2 text-center">
                                                        <label class="control-label">～</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <vue-flat-pickr
                                                            name="submitted_at_end"
                                                            :value.sync="form.submittedAtEnd"
                                                            :config="flatPickrConfig.submittedAtEnd"
                                                            @on-change="onSubmittedAtEndChange">
                                                        </vue-flat-pickr>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3"></div>
                                            <div class="col-sm-9 text-right">
                                                <span v-if="auth.auth_query">
                                                    <button type="button" class="btn btn-warning" @click="search">
                                                        <i class="fa-solid fa-magnifying-glass"></i> 查詢
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="panel-body">
                            <div v-if="auth.auth_update" class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive">
                                <table class="table table-striped table-bordered table-hover" style="width:100%"
                                    id="table_list">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap">功能</th>
                                            <th class="text-nowrap">退出單號</th>
                                            <th class="text-nowrap">送審時間</th>
                                            <th class="text-nowrap">供應商家數</th>
                                            <th class="text-nowrap">申請總金額</th>
                                            <th class="text-nowrap">申請總量</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(request, index) in miscStockRequests" :key="index">
                                            <td class="text-nowrap">
                                                <button type="button" class="btn btn-info btn-sm" @click="reviewRequest(request.id, $event)">
                                                    簽核
                                                </button>
                                            </td>
                                            <td>@{{ request.requestNo }}</td>
                                            <td>@{{ request.submittedAt }}</td>
                                            <td>@{{ request.totalSupCount }}</td>
                                            <td>@{{ request.expectedAmount }}</td>
                                            <td>@{{ request.expectedQty }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="alert alert-danger">您無審核權限，請洽系統管理員！</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('backend.misc_stock_request_review.review')
        @include('backend.misc_stock_request_review.review_detail')
    </div>
@endsection

@section('js')
    <script>
        const BASE_URI = '/backend/misc-stock-request-reviews';

        let vm = new Vue({
            el: "#app",
            data: {
                form: {
                    requestNo: "",
                    submittedAtStart: "",
                    submittedAtEnd: "",
                },
                modal: {
                    review: {
                        id: "review-modal",
                        title: "進貨退出單審核",
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
                        suppliers: [],
                        form: {
                            requestId: "",
                            supplierIds: [],
                            reviewResult: "",
                            reviewRemark: "",
                        },
                    },
                    reviewDetail: {
                        id: "review-detail-modal",
                        title: "",
                        list: [],
                    },
                },
                flatPickrConfig: {
                    submittedAtStart: {},
                    submittedAtEnd: {},
                },
                miscStockRequests: [],
                auth: {},
                reviewValidator: {},
                reviewButtonEvent: {},
            },
            created() {
                let payload = @json($payload);

                if (payload.auth) {
                    this.auth = Object.assign({}, this.auth, payload.auth);
                }

                if (!_.isEmpty(payload.misc_stock_requests)) {
                    payload.misc_stock_requests.forEach(request => {
                        this.miscStockRequests.push({
                            id: request.id,
                            requestNo: request.request_no,
                            submittedAt: request.submitted_at ? moment(request.submitted_at).format("YYYY-MM-DD HH:mm") : "",
                            totalSupCount: request.total_sup_count,
                            expectedAmount: request.expected_amount.toLocaleString('en-US'),
                            expectedQty: request.expected_qty,
                        });
                    });
                }

                this.setQueryParameters();
                this.initFlatPickrConfigs();
            },
            mounted() {
                let self = this;

                // 驗證表單
                this.reviewValidator = $("#review-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        self.updateReviewResult();
                    },
                    rules: {
                        reviewResult: {
                            required: true,
                        },
                        reviewRemark: {
                            required: {
                                depends: function (element) {
                                    return self.modal.review.form.reviewResult == "REJECT";
                                }
                            },
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
                    this.flatPickrConfig.submittedAtStart = {
                        dateFormat: "Y-m-d",
                        maxDate: this.form.submittedAtEnd,
                    };

                    this.flatPickrConfig.submittedAtEnd = {
                        dateFormat: "Y-m-d",
                        minDate: this.form.submittedAtStart,
                    };
                },
                onSubmittedAtStartChange(selectedDates, dateStr, instance) {
                    this.$set(this.flatPickrConfig.submittedAtEnd, 'minDate', dateStr);
                },
                onSubmittedAtEndChange(selectedDates, dateStr, instance) {
                    this.$set(this.flatPickrConfig.submittedAtStart, 'maxDate', dateStr);
                },
                search() {
                    $("#search-form").submit();
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
                async reviewRequest(id, event) {
                    let request = await this.getRequest(id);

                    this.modal.review.form.requestId = id;
                    this.modal.review.requestNo = request.request_no;
                    this.modal.review.warehouseName = request.warehouse_name;
                    this.modal.review.expectedQty = request.expected_qty;
                    this.modal.review.requestDate = moment(request.request_date).format("YYYY-MM-DD HH:mm");
                    this.modal.review.submittedAt = request.submitted_at ? moment(request.submitted_at).format("YYYY-MM-DD HH:mm") : "";
                    this.modal.review.expectedDate = request.expected_date ? moment(request.expected_date).format("YYYY-MM-DD") : "";
                    this.modal.review.tax = request.tax;
                    this.modal.review.expectedTaxAmount = request.expected_tax_amount.toLocaleString('en-US');
                    this.modal.review.expectedAmount = request.expected_amount.toLocaleString('en-US');
                    this.modal.review.remark = request.remark;

                    this.modal.review.suppliers = [];
                    if (!_.isEmpty(request.suppliers)) {
                        request.suppliers.forEach(supplier => {
                            this.modal.review.suppliers.push({
                                checked: false,
                                id: supplier.id,
                                name: supplier.name,
                                expectedQty: supplier.expected_qty,
                                expectedAmount: supplier.expected_amount.toLocaleString('en-US'),
                            });
                        });
                    }

                    this.reviewValidator.resetForm();
                    $("#review-form").find(".has-error").removeClass("has-error");
                    this.modal.review.form.reviewResult = "";
                    this.modal.review.form.reviewRemark = "";
                    this.reviewButtonEvent = event;
                    $(`#${this.modal.review.id}`).modal('show');
                },
                getRequest(id) {
                    return axios({
                            method: "get",
                            url: `${BASE_URI}/${id}/edit`,
                        })
                        .then(function(response) {
                            return response.data.payload.misc_stock_request;
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
                saveReviewResult() {
                    this.modal.review.form.supplierIds = [];
                    this.modal.review.suppliers.forEach(supplier => {
                        if (supplier.checked) {
                            this.modal.review.form.supplierIds.push(supplier.id);
                        }
                    });

                    if (!this.modal.review.form.supplierIds.length) {
                        alert("至少需勾選一個供應商");
                        return;
                    }

                    $('#review-form').submit();
                },
                updateReviewResult() {
                    axios({
                        method: "patch",
                        url: `${BASE_URI}/${this.modal.review.form.requestId}`,
                        data: {
                            supplier_ids: this.modal.review.form.supplierIds,
                            review_result: this.modal.review.form.reviewResult,
                            review_remark: this.modal.review.form.reviewRemark,
                        },
                    })
                    .then((response) => {
                        let payload = response.data.payload;

                        alert('儲存成功！');
                        if (payload.remaining_supplier_count <= 0) {
                            let dataTable = $('#table_list').DataTable();
                            dataTable.row(this.reviewButtonEvent.target.closest("tr")).remove().draw();
                        }
                    })
                    .catch((error) => {
                        console.log(error);

                        if (error.response) {
                            let data = error.response.data;
                            alert(data.message);
                        }
                    })
                    .finally(() => {
                        $(`#${this.modal.review.id}`).modal('hide');
                    });
                },
                async viewSupplierDetail(supplierId, supplierName) {
                    let detail = await this.getSupplierDetail(supplierId);

                    this.modal.reviewDetail.title = `【${supplierName}】商品明細`;
                    this.modal.reviewDetail.list = [];
                    if (!_.isEmpty(detail.list)) {
                        detail.list.forEach(item => {
                            this.modal.reviewDetail.list.push({
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

                    $(`#${this.modal.reviewDetail.id}`).modal('show');
                },
                getSupplierDetail(supplierId) {
                    return axios({
                            method: "get",
                            url: `${BASE_URI}/${this.modal.review.form.requestId}/review-modal/suppliers/${supplierId}`,
                        })
                        .then(function(response) {
                            return response.data.payload.detail;
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
                checkAll() {
                    this.modal.review.suppliers.forEach((supplier) => {
                        supplier.checked = true;
                    });
                },
                cancelAll() {
                    this.modal.review.suppliers.forEach((supplier) => {
                        supplier.checked = false;
                    });
                },
            },
        });
    </script>
@endsection
