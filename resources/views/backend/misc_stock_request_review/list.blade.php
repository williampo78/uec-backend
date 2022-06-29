@extends('backend.master')

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
                                                <input type="text" class="form-control" name="requestNo" v-model="form.requestNo">
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
                                                        <vue-flat-pickr :setting="form.submittedAtStart" @on-change="onSubmittedAtStartChange"></vue-flat-pickr>
                                                    </div>
                                                    <div class="col-sm-2 text-center">
                                                        <label class="control-label">～</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <vue-flat-pickr :setting="form.submittedAtEnd" @on-change="onSubmittedAtEndChange"></vue-flat-pickr>
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
        {{-- @include('backend.misc_stock_request_review.review') --}}
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
                    submittedAtStart: {
                        name: "submittedAtStart",
                        date: "",
                        config: {},
                    },
                    submittedAtEnd: {
                        name: "submittedAtEnd",
                        date: "",
                        config: {},
                    },
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
                        form: {
                            requestSuppliers: [],
                            reviewResult: "",
                            reviewRemark: "",
                        },
                    },
                },
                miscStockRequests: [],
                auth: {},
            },
            created() {
                let payload = @json($payload);

                if (payload.auth) {
                    this.auth = Object.assign({}, this.auth, payload.auth);
                }

                if (Array.isArray(payload.miscStockRequests) && payload.miscStockRequests.length) {
                    payload.miscStockRequests.forEach(request => {
                        this.miscStockRequests.push({
                            id: request.id,
                            requestNo: request.requestNo,
                            submittedAt: request.submittedAt ? moment(request.submittedAt).format("YYYY-MM-DD HH:mm") : "",
                            totalSupCount: request.totalSupCount,
                            expectedAmount: request.expectedAmount.toLocaleString('en-US'),
                            expectedQty: request.expectedQty,
                        });
                    });
                }

                this.setQueryParameters();
                this.initFlatPickrConfigs();
            },
            mounted() {

            },
            methods: {
                initFlatPickrConfigs() {
                    this.form.submittedAtStart.config = {
                        dateFormat: "Y-m-d",
                        maxDate: this.form.submittedAtEnd.date,
                    };

                    this.form.submittedAtEnd.config = {
                        dateFormat: "Y-m-d",
                        minDate: this.form.submittedAtStart.date,
                    };
                },
                onSubmittedAtStartChange(selectedDates, dateStr, instance) {
                    this.$set(this.form.submittedAtEnd.config, 'minDate', dateStr);
                },
                onSubmittedAtEndChange(selectedDates, dateStr, instance) {
                    this.$set(this.form.submittedAtStart.config, 'maxDate', dateStr);
                },
                search() {
                    $("#search-form").submit();
                },
                setQueryParameters() {
                    const urlSearchParams = new URLSearchParams(window.location.search);
                    const params = Object.fromEntries(urlSearchParams.entries());

                    urlSearchParams.forEach((value, key) => {
                        if (!this.form.hasOwnProperty(key)) {
                            return;
                        }

                        if (['submittedAtStart', 'submittedAtEnd'].includes(key)) {
                            this.form[key].date = value;
                        } else {
                            this.form[key] = value;
                        }
                    });
                },
                async reviewRequest(id) {
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
                            url: `${BASE_URI}/${id}/edit`,
                        })
                        .then(function(response) {
                            return response.data.payload.miscStockRequest;
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
                saveReviewResult() {
                    $('#review-form').submit();
                },
                updateReviewResult() {
                    // axios({
                    //     method: "patch",
                    //     url: `${BASE_URI}/${this.modal.expectedDate.requestId}/expected-date`,
                    //     data: {
                    //         expectedDate: this.modal.expectedDate.expectedDate.date,
                    //         shipToName: this.modal.expectedDate.shipToName,
                    //         shipToMobile: this.modal.expectedDate.shipToMobile,
                    //         shipToAddress: this.modal.expectedDate.shipToAddress,
                    //     },
                    // })
                    // .then((response) => {
                    //     alert('儲存成功！');
                    // })
                    // .catch((error) => {
                    //     console.log(error);

                    //     if (error.response) {
                    //         let data = error.response.data;
                    //         alert(data.message);
                    //     }
                    // })
                    // .finally(() => {
                    //     $(`#${this.modal.expectedDate.id}`).modal('hide');
                    // });
                },
            },
        });
    </script>
@endsection
