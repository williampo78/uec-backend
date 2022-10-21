@extends('backend.layouts.master')

@section('title', '庫存異動歷程查詢')

@section('content')
    <style>
        .stock-outer{ grid-template-columns: 1fr 1fr 1fr; gap: 15px; padding: 0 5px; }
        .stock-title{ display: grid; grid-template-columns: 100px 1fr;  align-items: center; }
    </style>
    <div id="app" v-cloak>
        <div id="page-wrapper">
            <!-- 表頭名稱 -->
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header"><i class="fa-solid fa-list"></i> 庫存異動歷程查詢</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <!-- 功能按鈕 -->
                        <div class="panel-heading">
                            <form id="search-form" class="form-horizontal" method="GET" action="">
                                <div class="d-block d-md-grid stock-outer" >
                                    <div class="mb-4 stock-title" >
                                        <label>異動日期</label>
                                        <div>
                                            <div class="d-flex align-items-center">
                                                <vue-flat-pickr name="dateStart"
                                                                v-model="searchParameters.dateStart.date"
                                                                style="flex-grow:1">
                                                </vue-flat-pickr>
                                                <label>～</label>
                                                <vue-flat-pickr name="dateEnd"
                                                                v-model="searchParameters.dateEnd.date"
                                                                style="flex-grow:1">
                                                </vue-flat-pickr>
                                            </div>
                                        </div>
                                    </div>
                                    <div  class="mb-4 stock-title">
                                        <label>倉庫</label>
                                        <div>
                                            <select2 class="form-control" :options="options.warehouses"
                                                        v-model="searchParameters.warehouse" name="warehouse">
                                                <option disabled value=""></option>
                                            </select2>
                                        </div>
                                    </div>
                                    <div  class="mb-4 stock-title">
                                        <label>供應商</label>
                                        <div>
                                            <select2 class="form-control" :options="options.suppliers"
                                                        :disabled="data.disabledSupplierId"
                                                        v-model="searchParameters.supplierId" name="supplierId">
                                                <option disabled value=""></option>
                                            </select2>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-block d-md-grid stock-outer">
                                    <div  class="mb-4 stock-title">
                                        <label>Item編號</label>
                                        <div class="d-flex align-items-center">
                                            <input class="form-control" name="itemNoStart"
                                                v-model="searchParameters.itemNoStart" autocomplete="off">
                                            <label>～</label>
                                            <input class="form-control" name="itemNoEnd"
                                                v-model="searchParameters.itemNoEnd" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="mb-4 stock-title">
                                        <label>來源單據名稱</label>
                                        <select2 class="form-control"
                                                    :options="options.sourceTableNames"
                                                    v-model="searchParameters.sourceTableName"
                                                    name="sourceTableName">
                                            <option disabled value=""></option>
                                        </select2>
                                    </div>
                                    <div class="mb-4 stock-title">
                                        <label>來源單號</label>
                                        <input type="text" class="form-control" name="sourceDocNo"
                                                v-model="searchParameters.sourceDocNo" autocomplete="off"
                                                placeholder="模糊查詢">
                                    </div>
                                </div>
                                <div class="d-block d-md-grid stock-outer">
                                    <div class="mb-4 stock-title">
                                        <label>商品序號</label>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control" name="productNoStart"
                                                    v-model="searchParameters.productNoStart"
                                                    autocomplete="off">
                                            <label>～</label>
                                            <input type="text" class="form-control" name="productNoEnd"
                                            v-model="searchParameters.productNoEnd"
                                            autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="mb-4 stock-title">
                                        <label>庫存類型
                                        </label>
                                        <select2 class="form-control" :options="options.stockTypes"
                                                    v-model="searchParameters.stockType" name="stockType">
                                            <option disabled value=""></option>
                                        </select2>
                                    </div>
                                    <div  class="mb-4 stock-title">
                                        <label>筆數限制
                                        </label>
                                        <input class="form-control search-limit-group" name="limit"
                                                v-model="searchParameters.limit" value="500" readonly>
                                    </div>
                                </div>
                                <div class="text-right" style="padding-right: 5px; padding-bottom: 5px;">
                                    <div v-if="data.auth.auth_query">
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fa-solid fa-magnifying-glass"></i> 查詢
                                        </button>
                                        <button type="button" class="btn btn-danger" @click="resetForm">
                                            <i class="fa-solid fa-eraser"></i> 清除
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Table list -->
                        <div class="panel-body">
                            <div class="dataTables_wrapper form-inline dt-bootstrap no-footer table-responsive">
                                <table class="table table-striped table-bordered table-hover" style="width:100%"
                                       id="table_list">
                                    <thead>
                                    <tr>
                                        <th class="text-nowrap">項次</th>
                                        <th class="text-nowrap">異動時間</th>
                                        <th class="text-nowrap">商品序號</th>
                                        <th class="text-nowrap">庫存類型</th>
                                        <th class="text-nowrap">Item編號</th>
                                        <th class="text-nowrap">商品名稱</th>
                                        <th class="text-nowrap">規格一</th>
                                        <th class="text-nowrap">規格二</th>
                                        <th class="text-nowrap">異動數量</th>
                                        <th class="text-nowrap">倉庫</th>
                                        <th class="text-nowrap">異動類型</th>
                                        <th class="text-nowrap">來源單號</th>
                                        <th class="text-nowrap">來源單據名稱</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(stockTransactionLog, index) in data.stockTransactionLogs"
                                        :key="stockTransactionLog.id">
                                        @verbatim
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ stockTransactionLog.transaction_date }}</td>
                                            <td>{{ stockTransactionLog.product_no }}</td>
                                            <td>{{ stockTransactionLog.stock_type }}</td>
                                            <td>{{ stockTransactionLog.item_no }}</td>
                                            <td>{{ stockTransactionLog.product_name }}</td>
                                            <td>{{ stockTransactionLog.spec_1_value }}</td>
                                            <td>{{ stockTransactionLog.spec_2_value }}</td>
                                            <td>{{ stockTransactionLog.transaction_qty }}</td>
                                            <td>{{ stockTransactionLog.warehouse_name }}</td>
                                            <td>{{ stockTransactionLog.transaction_type }}</td>
                                            <td>{{ stockTransactionLog.source_doc_no }}</td>
                                            <td>{{ stockTransactionLog.source_table_name }}</td>
                                        @endverbatim
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
@endsection

@section('js')
    <script>
        let disabledSupplierId = {{ empty($supplierId) ? 0 : 1 }};

        new Vue({
            el: '#app',
            data: {
                options: {
                    warehouses: @json($warehouses),
                    suppliers: @json($suppliers),
                    sourceTableNames: @json($sourceTableNames),
                    stockTypes:@json($stockTypes),
                },
                searchParameters: {
                    dateStart: {
                        name: 'dateStart',
                        date: '{{ request('dateStart') }}',
                        config: {},
                    },
                    dateEnd: {
                        name: 'dateEnd',
                        date: '{{ request('dateEnd') }}',
                        config: {},
                    },
                    warehouse: '{{ request('warehouse') }}',
                    supplierId: '{{ empty($supplierId) ? request('supplierId') : $supplierId }}',
                    itemNoStart: '{{ request('itemNoStart') }}',
                    itemNoEnd: '{{ request('itemNoEnd') }}',
                    sourceTableName: '{{ request('sourceTableName') }}',
                    sourceDocNo: '{{ request('sourceDocNo') }}',
                    productNoStart: '{{ request('productNoStart') }}',
                    productNoEnd: '{{ request('productNoEnd') }}',
                    stockType: '{{ request('stockType') }}',
                    limit: 500,
                },
                data: {
                    stockTransactionLogs: @json($stockTransactionLogs),
                    auth: @json($share_role_auth),
                    disabledSupplierId: false,
                }
            },
            created() {
                let supplierId = {{ empty($supplierId) ? 0 : 1 }};
                this.data.disabledSupplierId = supplierId == 1;
            },
            methods: {
                resetForm() {
                    let self = this;

                    Object.keys(self.searchParameters).forEach(function (key) {

                        if (['limit'].includes(key)) {
                            return;
                        }

                        if (['dateStart', 'dateEnd'].includes(key)) {
                            self.searchParameters[key].date = null;
                            return;
                        }

                        self.searchParameters[key] = null;
                    });
                }
            }
        });
    </script>
@endsection
