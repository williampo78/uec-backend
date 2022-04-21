@extends('backend.master')

@section('title', '供應商主檔管理')

@section('content')
    <div id="app">
        <!--新增-->
        <div id="page-wrapper">
            <!-- 表頭名稱 -->
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header"><i class="fa-solid fa-truck"></i> 供應商主檔管理</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <!-- 功能按鈕(新增) -->
                        <div class="panel-heading">
                            <form id="search-form" class="form-horizontal" method="get" action="">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">供應商類別</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <v-select v-model="searchForm.supplierTypeId" :reduce="option => option.code" :options="[
                                                    @isset($supplierTypes)
                                                        @foreach ($supplierTypes as $supplierType)
                                                            {label: '{{ $supplierType->name }}', code: '{{ $supplierType->id }}'},
                                                        @endforeach
                                                    @endisset
                                                ]">
                                                </v-select>
                                                <input type="hidden" v-model="searchForm.supplierTypeId" name="supplier_type_id" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">供應商</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="display_number_or_name"
                                                    v-model="searchForm.displayNumberOrName" placeholder="模糊查詢" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">統一編號</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="company_number"
                                                    v-model="searchForm.companyNumber" />
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
                                                <v-select v-model="searchForm.active" :reduce="option => option.code" :options="[
                                                    @isset($activeOptions)
                                                        @foreach ($activeOptions as $key => $activeOption)
                                                            {label: '{{ $activeOption }}', code: '{{ $key }}'},
                                                        @endforeach
                                                    @endisset
                                                ]">
                                                </v-select>
                                                <input type="hidden" v-model="searchForm.active" name="active" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <div class="col-sm-3"></div>
                                            <div class="col-sm-9 text-right">
                                                @if ($share_role_auth['auth_query'])
                                                    <button type="button" class="btn btn-danger" @click="resetForm">
                                                        <i class="fa-solid fa-eraser"></i> 清除
                                                    </button>

                                                    <button class="btn btn-warning">
                                                        <i class="fa-solid fa-magnifying-glass"></i> 查詢
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Table list -->
                        <div class="panel-body">
                            <div class="row">
                                @if ($share_role_auth['auth_create'])
                                    <div class="col-sm-2">
                                        <a href="{{ route('supplier.create') }}" class="btn btn-block btn-warning btn-sm"
                                            id="btn-create">
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
                                        @foreach ($suppliers as $supplier)
                                            <tr>
                                                <td>
                                                    @if ($share_role_auth['auth_query'])
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('supplier.show', $supplier->id) }}" title="檢視">
                                                            <i class="fa-solid fa-magnifying-glass"></i>
                                                        </a>
                                                    @endif

                                                    @if ($share_role_auth['auth_update'])
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('supplier.edit', $supplier->id) }}">
                                                            <i class="fa-solid fa-pencil"></i>
                                                            編輯
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>{{ $supplier->display_number }}</td>
                                                <td>{{ $supplier->company_number }}</td>
                                                <td>{{ $supplier->short_name }}</td>
                                                <td>{{ $supplier->name }}</td>
                                                <td>
                                                    @if ($supplier->active)
                                                        啟用
                                                    @else
                                                        關閉
                                                    @endif
                                                </td>
                                                <td>
                                                    @isset($supplier->paymentTerm)
                                                        {{ $supplier->paymentTerm->description }}
                                                    @endisset
                                                </td>
                                                <td>{{ $supplier->telephone }}</td>
                                                <td>{{ $supplier->address }}</td>
                                            </tr>
                                        @endforeach
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
        let vm = new Vue({
            el: "#app",
            data: {
                searchForm: {
                    supplierTypeId: "",
                    displayNumberOrName: "",
                    companyNumber: "",
                    active: "",
                },
            },
            created() {
                this.searchForm.supplierTypeId = "{{ request()->input('supplier_type_id') }}";
                this.searchForm.displayNumberOrName = "{{ request()->input('display_number_or_name') }}";
                this.searchForm.companyNumber = "{{ request()->input('company_number') }}";
                this.searchForm.active = "{{ request()->input('active') }}";
            },
            methods: {
                resetForm() {
                    let self = this;

                    Object.keys(self.searchForm).forEach(function(value, index) {
                        self.searchForm[value] = "";
                    });
                },
            },
        });
    </script>
@endsection
