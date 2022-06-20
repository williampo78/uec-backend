@extends('backend.master')

@section('title', '進貨退出單')

@section('content')
    <div id="app">
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
                                                <select2 class="form-control" :options="statusCodes"
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
                                                @if ($share_role_auth['auth_query'])
                                                    <button type="button" class="btn btn-warning" @click="search">
                                                        <i class="fa-solid fa-magnifying-glass"></i> 查詢
                                                    </button>

                                                    <button type="button" class="btn btn-danger" @click="resetForm">
                                                        <i class="fa-solid fa-eraser"></i> 清除
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                @if ($share_role_auth['auth_create'])
                                    <div class="col-sm-2">
                                        <a href="{{ route('misc_stock_requests.create') }}"
                                            class="btn btn-block btn-warning btn-sm">
                                            <i class="fa-solid fa-plus"></i> 新增
                                        </a>
                                    </div>
                                @endif
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
                                        <tr v-for="(stockRequest, index) in form.miscStockRequests" :key="index">
                                            {{-- <td>
                                                @if ($share_role_auth['auth_query'])
                                                    <button type="button" class="btn btn-info btn-sm" title="檢視"
                                                        @click="showRequest('{{ $buyoutStockInRequest['id'] }}')">
                                                        <i class="fa-solid fa-magnifying-glass"></i>
                                                    </button>
                                                @endif

                                                @if ($share_role_auth['auth_update'] && $buyoutStockInRequest['status_code'] == 'DRAFTED')
                                                    <a class="btn btn-warning btn-sm"
                                                        href="{{ route('misc_stock_requests.edit', $buyoutStockInRequest['id']) }}">
                                                        編輯
                                                    </a>
                                                @endif

                                                @if ($share_role_auth['auth_delete'] && $buyoutStockInRequest['status_code'] == 'DRAFTED')
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        @click="deleteRequest('{{ $buyoutStockInRequest['id'] }}', '{{ $buyoutStockInRequest['request_no'] }}', $event)">
                                                        刪除
                                                    </button>
                                                @endif
                                            </td>
                                            <td>{{ $buyoutStockInRequest['request_no'] }}</td>
                                            <td>{{ $buyoutStockInRequest['request_date'] }}</td>
                                            <td>{{ $buyoutStockInRequest['expected_date'] }}</td>
                                            <td>{{ $buyoutStockInRequest['expected_qty'] }}</td>
                                            <td>{{ $buyoutStockInRequest['display_status_code'] }}</td>
                                            <td>{{ $buyoutStockInRequest['approved_at'] }}</td>
                                            <td>{{ $buyoutStockInRequest['edi_exported_at'] }}</td>
                                            <td>{{ $buyoutStockInRequest['actual_date'] }}</td>
                                            <td>{{ $buyoutStockInRequest['supplier'] }}</td> --}}
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
    </div>
@endsection

@section('js')
    <script>
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
                    requestNo: "",
                    requestDate: "",
                    supplier: "",
                    expectedDate: "",
                    expectedQty: "",
                    remark: "",
                    statusCode: "",
                    actualDate: "",
                    items: [],
                    reviewLogs: [],
                },
                statusCodes: [],
                suppliers: [],
            },
            created() {
                let payload = @json($payload);
                console.log(payload);

                if (payload.statusCodes) {
                    Object.entries(payload.statusCodes).forEach(([key, statusCode]) => {
                        this.statusCodes.push({
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

                this.initFlatPickrConfigs();
                this.setQueryParameters();
            },
            mounted() {
                let self = this;

                // 驗證表單
                $("#search-form").validate({
                    debug: true,
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
                    },
                    messages: {

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
                search() {
                    $("#search-form").submit();
                },
                resetForm() {
                    let self = this;

                    Object.keys(self.form).forEach(function(value, index) {
                        self.form[value] = "";
                    });
                },
                setQueryParameters() {
                    let requestDateStart = "{{ request()->input('request_date_start') }}";
                    let requestDateEnd = "{{ request()->input('request_date_end') }}";
                    let statusCode = "{{ request()->input('status_code') }}";
                    let expectedDateStart = "{{ request()->input('expected_date_start') }}";
                    let expectedDateEnd = "{{ request()->input('expected_date_end') }}";
                    let supplierId = "{{ request()->input('supplier_id') }}";

                    if (requestDateStart) {
                        this.form.requestDateStart = requestDateStart;
                    }

                    if (requestDateEnd) {
                        this.form.requestDateEnd = requestDateEnd;
                    }

                    if (statusCode) {
                        this.form.statusCode = statusCode;
                    }

                    if (expectedDateStart) {
                        this.form.expectedDateStart = expectedDateStart;
                    }

                    if (expectedDateEnd) {
                        this.form.expectedDateEnd = expectedDateEnd;
                    }

                    if (supplierId) {
                        this.form.supplierId = supplierId;
                    }
                },
                deleteRequest(id, requestNo, event) {
                    if (confirm(`確定要刪除申請單《${requestNo}》？`)) {
                        axios({
                                method: "delete",
                                url: `/backend/buyout-stock-in-requests/${id}`,
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
                async showRequest(id) {
                    let buyoutStockInRequest = await this.getBuyoutStockInRequest(id);

                    this.modal.requestNo = buyoutStockInRequest.request_no;
                    this.modal.requestDate = buyoutStockInRequest.request_date;
                    this.modal.supplier = buyoutStockInRequest.supplier;
                    this.modal.expectedDate = buyoutStockInRequest.expected_date;
                    this.modal.expectedQty = buyoutStockInRequest.expected_qty;
                    this.modal.remark = buyoutStockInRequest.remark;
                    this.modal.statusCode = buyoutStockInRequest.status_code;
                    this.modal.actualDate = buyoutStockInRequest.actual_date;

                    this.modal.items = [];
                    if (Array.isArray(buyoutStockInRequest.items) && buyoutStockInRequest.items.length) {
                        buyoutStockInRequest.items.forEach(item => {
                            this.modal.items.push({
                                productItem: `${item.item_no}-${item.supplier_item_no}-${item.product_name}`,
                                spec1Value: item.spec_1_value,
                                spec2Value: item.spec_2_value,
                                minPurchaseQty: item.min_purchase_qty,
                                expectedQty: item.expected_qty,
                                actualQty: item.actual_qty,
                            });
                        });
                    }

                    this.modal.reviewLogs = [];
                    if (Array.isArray(buyoutStockInRequest.review_logs) && buyoutStockInRequest.review_logs.length) {
                        buyoutStockInRequest.review_logs.forEach(reviewLog => {
                            this.modal.reviewLogs.push({
                                reviewer: reviewLog.reviewer,
                                reviewAt: reviewLog.review_at,
                                reviewResult: reviewLog.review_result,
                                reviewRemark: reviewLog.review_remark,
                            });
                        });
                    }

                    $('#buyout-stock-in-request-modal').modal('show');
                },
                getBuyoutStockInRequest(id) {
                    return axios({
                            method: "get",
                            url: `/backend/buyout-stock-in-requests/${id}`,
                        })
                        .then(function(response) {
                            return response.data;
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
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
            },
        });
    </script>
@endsection
