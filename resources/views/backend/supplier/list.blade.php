@extends('backend.layouts.master')

@section('title', '供應商主檔管理')

@section('content')
    <div id="app" v-cloak>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header"><i class="fa-solid fa-truck"></i> 供應商主檔管理</h1>
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
                                                <label class="control-label">供應商類別</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <select2
                                                    class="form-control"
                                                    :options="supplierTypes"
                                                    v-model="form.supplierTypeId"
                                                    name="supplier_type_id"
                                                >
                                                    <option disabled value=""></option>
                                                </select2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">供應商</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    name="display_number_or_name"
                                                    v-model="form.displayNumberOrName"
                                                    placeholder="模糊查詢"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">統一編號</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    name="company_number"
                                                    v-model="form.companyNumber"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">狀態</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <select2
                                                    class="form-control"
                                                    :options="activeOptions"
                                                    v-model="form.active"
                                                    name="active"
                                                >
                                                    <option disabled value=""></option>
                                                </select2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <div class="col-sm-3"></div>
                                            <div class="col-sm-9 text-right">
                                                <span v-if="auth.authQuery">
                                                    <button class="btn btn-warning">
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
                                <div class="col-sm-2" v-if="auth.authCreate">
                                    <a :href="`${BASE_URI}/create`"
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
                                            <th class="text-nowrap">供應商編號</th>
                                            <th class="text-nowrap">統一統編</th>
                                            <th class="text-nowrap">簡稱</th>
                                            <th class="text-nowrap">完整名稱</th>
                                            <th class="text-nowrap">狀態</th>
                                            <th class="text-nowrap">付款條件</th>
                                            <th class="text-nowrap">聯絡電話</th>
                                            <th class="text-nowrap">地址</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(supplier, index) in suppliers" :key="index">
                                            <td>
                                                <span v-if="auth.authQuery">
                                                    <a class="btn btn-info btn-sm"
                                                        :href="`${BASE_URI}/${supplier.id}`" title="檢視">
                                                        <i class="fa-solid fa-magnifying-glass"></i>
                                                    </a>
                                                </span>

                                                <span v-if="auth.authUpdate">
                                                    <a class="btn btn-info btn-sm"
                                                        :href="`${BASE_URI}/${supplier.id}/edit`">
                                                        <i class="fa-solid fa-pencil"></i>
                                                        編輯
                                                    </a>
                                                </span>
                                            </td>
                                            <td>@{{ supplier.displayNumber }}</td>
                                            <td>@{{ supplier.companyNumber }}</td>
                                            <td>@{{ supplier.shortName }}</td>
                                            <td>@{{ supplier.name }}</td>
                                            <td>@{{ supplier.activeName }}</td>
                                            <td>@{{ supplier.paymentTermName }}</td>
                                            <td>@{{ supplier.telephone }}</td>
                                            <td>@{{ supplier.address }}</td>
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
        const BASE_URI = '/backend/supplier';

        let vm = new Vue({
            el: "#app",
            data() {
                return {
                    form: {
                        supplierTypeId: null,
                        displayNumberOrName: "",
                        companyNumber: "",
                        active: null,
                    },
                    supplierTypes: [],
                    activeOptions: [
                        {
                            id: 1,
                            text: "啟用",
                        },
                        {
                            id: 0,
                            text: "關閉",
                        },
                    ],
                    suppliers: [],
                    auth: {},
                };
            },
            created() {
                this.BASE_URI = BASE_URI;
                let payload = @json($payload);
                payload = camelcaseKeys(payload, { deep: true });

                // 供應商類別
                if (!_.isEmpty(payload.supplierTypes)) {
                    payload.supplierTypes.forEach(supplierType => {
                        this.supplierTypes.push({
                            text: supplierType.name,
                            id: supplierType.id,
                        });
                    });
                }

                // 權限
                if (payload.auth) {
                    this.auth = payload.auth;
                }

                // 供應商
                if (!_.isEmpty(payload.suppliers)) {
                    this.suppliers = payload.suppliers;
                }

                this.setQueryParameters();
            },
            methods: {
                resetForm() {
                    this.form = this.$options.data().form;
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
            },
        });
    </script>
@endsection
