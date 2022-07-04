@extends('backend.layouts.master')

@section('title', '滿額活動管理')

@section('content')
    <div id="app">
        <div id="page-wrapper">
            <!-- 表頭名稱 -->
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header"><i class="fa-solid fa-table"></i> 滿額活動管理</h1>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <!-- 功能按鈕 -->
                        <div class="panel-heading">
                            <form id="search-form" class="form-horizontal" method="get" action="">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">活動名稱</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control"
                                                    name="campaign_name"
                                                    v-model="form.campaignName" placeholder="模糊查詢" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">狀態</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <select2 class="form-control" :options="activeOptions"
                                                    v-model="form.active" name="active">
                                                    <option disabled value=""></option>
                                                </select2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">活動類型</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <select2 class="form-control" :options="campaignTypes"
                                                    v-model="form.campaignType" name="campaign_type">
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
                                                <label class="control-label">上架時間起</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <div class="col-sm-5">
                                                        <div class="input-group" id="start_at_start_flatpickr">
                                                            <input type="text" class="form-control" name="start_at_start"
                                                                autocomplete="off" data-input v-model="form.startAtStart">
                                                            <span class="input-group-btn" data-toggle>
                                                                <button class="btn btn-default" type="button">
                                                                    <i class="fa-solid fa-calendar-days"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2 text-center">
                                                        <label class="control-label">～</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="input-group" id="start_at_end_flatpickr">
                                                            <input type="text" class="form-control" name="start_at_end"
                                                                autocomplete="off" data-input v-model="form.startAtEnd">
                                                            <span class="input-group-btn" data-toggle>
                                                                <button class="btn btn-default" type="button">
                                                                    <i class="fa-solid fa-calendar-days"></i>
                                                                </button>
                                                            </span>
                                                        </div>
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
                                                <input type="text" class="form-control" name="product_no"
                                                    v-model="form.productNo">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-3"></div>
                                            <div class="col-sm-9 text-right">
                                                @if ($share_role_auth['auth_query'])
                                                    <button class="btn btn-warning">
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

                        <!-- Table list -->
                        <div class="panel-body">
                            <div class="row">
                                @if ($share_role_auth['auth_create'])
                                    <div class="col-sm-2">
                                        <a href="{{ route('promotional_campaign_cart.create') }}"
                                            class="btn btn-block btn-warning btn-sm" id="btn-create">
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
                                        <tr role="row">
                                            <th class="text-nowrap">功能</th>
                                            <th class="text-nowrap">項次</th>
                                            <th class="text-nowrap">活動名稱</th>
                                            <th class="text-nowrap">活動類型</th>
                                            <th class="text-nowrap">狀態</th>
                                            <th class="text-nowrap">上架時間起</th>
                                            <th class="text-nowrap">上架時間訖</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($campaigns)
                                            @foreach ($campaigns as $campaign)
                                                <tr>
                                                    <td>
                                                        @if ($share_role_auth['auth_query'])
                                                            <button type="button"
                                                                class="btn btn-info btn-sm" title="檢視"
                                                                @click="showCampaign('{{ $campaign['id'] }}')">
                                                                <i class="fa-solid fa-magnifying-glass"></i>
                                                            </button>
                                                        @endif

                                                        @if ($share_role_auth['auth_update'])
                                                            <a class="btn btn-info btn-sm"
                                                                href="{{ route('promotional_campaign_cart.edit', $campaign['id']) }}">
                                                                編輯
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $campaign['campaign_name'] }}</td>
                                                    <td>{{ $campaign['campaign_type'] ?? '' }}</td>
                                                    <td>{{ $campaign['active'] ?? '' }}</td>
                                                    <td>{{ $campaign['start_at'] }}</td>
                                                    <td>{{ $campaign['end_at'] }}</td>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('backend.promotional_campaign.cart.show')
        </div>
    </div>
@endsection

@section('js')
    <script>
        let vm = new Vue({
            el: "#app",
            data: {
                form: {
                    campaignName: "",
                    active: "",
                    campaignType: "",
                    startAtStart: "",
                    startAtEnd: "",
                    productNo: "",
                },
                modal: {
                    campaignName: "",
                    active: "",
                    campaignType: "",
                    nValue: "",
                    xValue: "",
                    startAt: "",
                    endAt: "",
                    targetGroups: "所有會員",
                    products: [],
                    giveaways: [],
                    showXValue: true,
                    showProduct: true,
                    showGiveaway: true,
                },
                activeOptions: [],
                campaignTypes: [],
            },
            created() {
                let activeOptions = @json($activeOptions);
                let campaignTypes = @json($campaignTypes);

                if (activeOptions) {
                    Object.entries(activeOptions).forEach(([key, activeOption]) => {
                        this.activeOptions.push({
                            text: activeOption,
                            id: key,
                        });
                    });
                }

                if (campaignTypes) {
                    campaignTypes.forEach(campaignType => {
                        this.campaignTypes.push({
                            text: campaignType.description,
                            id: campaignType.code,
                        });
                    });
                }

                this.setQueryParameters();
            },
            mounted() {
                let startAtStartFlatpickr = flatpickr("#start_at_start_flatpickr", {
                    dateFormat: "Y-m-d",
                    maxDate: this.form.startAtEnd,
                    onChange: function(selectedDates, dateStr, instance) {
                        startAtEndFlatpickr.set('minDate', dateStr);
                    },
                });

                let startAtEndFlatpickr = flatpickr("#start_at_end_flatpickr", {
                    dateFormat: "Y-m-d",
                    minDate: this.form.startAtStart,
                    onChange: function(selectedDates, dateStr, instance) {
                        startAtStartFlatpickr.set('maxDate', dateStr);
                    },
                });
            },
            methods: {
                resetForm() {
                    let self = this;

                    Object.keys(self.form).forEach(function(value, index) {
                        self.form[value] = "";
                    });
                },
                setQueryParameters() {
                    let campaignName =
                        "{{ request()->input('campaign_name') }}";
                    let active = "{{ request()->input('active') }}";
                    let campaignType = "{{ request()->input('campaign_type') }}";
                    let startAtStart = "{{ request()->input('start_at_start') }}";
                    let startAtEnd = "{{ request()->input('start_at_end') }}";
                    let productNo = "{{ request()->input('product_no') }}";

                    if (campaignName) {
                        this.form.campaignName = campaignName;
                    }

                    if (active) {
                        this.form.active = active;
                    }

                    if (campaignType) {
                        this.form.campaignType = campaignType;
                    }

                    if (startAtStart) {
                        this.form.startAtStart = startAtStart;
                    }

                    if (startAtEnd) {
                        this.form.startAtEnd = startAtEnd;
                    }

                    if (productNo) {
                        this.form.productNo = productNo;
                    }
                },
                async showCampaign(id) {
                    let campaign = await this.getCampaign(id);

                    if (['CART04'].includes(campaign.campaign_type)) {
                        this.modal.showProduct = true;
                    } else {
                        this.modal.showProduct = false;
                    }

                    if (['CART01', 'CART02'].includes(campaign.campaign_type)) {
                        this.modal.showXValue = true;
                        this.modal.showGiveaway = false;
                    } else {
                        this.modal.showXValue = false;
                        this.modal.showGiveaway = true;
                    }

                    this.modal.campaignName = campaign.campaign_name;
                    this.modal.active = campaign.active;
                    this.modal.campaignType = campaign.display_campaign_type;
                    this.modal.nValue = campaign.n_value;
                    this.modal.xValue = campaign.x_value;
                    this.modal.startAt = campaign.start_at;
                    this.modal.endAt = campaign.end_at;

                    this.modal.products = [];
                    if (Array.isArray(campaign.products) && campaign.products.length) {
                        campaign.products.forEach(product => {
                            this.modal.products.push({
                                productNo: product.product_no,
                                productName: product.product_name,
                                sellingPrice: product.selling_price,
                                launchedAt: product.start_launched_at || product.end_launched_at ? `${product.start_launched_at} ~ ${product.end_launched_at}` : '',
                                launchedStatus: product.launch_status,
                                grossMargin: product.gross_margin,
                            });
                        });
                    }

                    this.modal.giveaways = [];
                    if (Array.isArray(campaign.giveaways) && campaign.giveaways.length) {
                        campaign.giveaways.forEach(giveaway => {
                            this.modal.giveaways.push({
                                productNo: giveaway.product_no,
                                productName: giveaway.product_name,
                                assignedQty: giveaway.assigned_qty,
                            });
                        });
                    }

                    $('#cart-campaign-modal').modal('show');
                },
                getCampaign(id) {
                    return axios({
                            method: "get",
                            url: `/backend/promotional-campaign-cart/${id}`,
                        })
                        .then(function(response) {
                            return response.data;
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                },
            },
        });
    </script>
@endsection
