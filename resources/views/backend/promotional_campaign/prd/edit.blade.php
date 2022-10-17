@extends('backend.layouts.master')

@section('title', '單品活動 編輯資料')

@section('css')
    <style>
        .modal-dialog {
            max-width: 100%;
        }
    </style>
@endsection

@section('content')
    <div id="app" v-cloak>
        @if ($errors->any())
            <div ref="errorMessage" style="display: none;">
                {{ $errors->first('message') }}
            </div>
        @endif

        <div id="page-wrapper">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">請輸入下列欄位資料</div>
                        <div class="panel-body">
                            <form id="edit-form" method="post"
                                action="{{ route('promotional_campaign_prd.update', $campaign['id']) }}">
                                @method('PUT')
                                @csrf
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-horizontal">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="col-sm-2">
                                                            <label class="control-label">活動名稱 <span
                                                                    style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <input type="text" class="form-control" name="campaign_name"
                                                                v-model="form.campaignName">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="col-sm-2">
                                                            <label class="control-label">狀態 <span
                                                                    style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <label class="radio-inline">
                                                                <input type="radio" name="active" value="1"
                                                                    v-model="form.active">生效
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" name="active" value="0"
                                                                    v-model="form.active">失效
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br />

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="col-sm-2">
                                                            <label class="control-label">活動類型 <span
                                                                    style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <select2
                                                                class="form-control"
                                                                :options="campaignTypes"
                                                                v-model="form.campaignType"
                                                                name="campaign_type"
                                                                @select2-selecting="onCampaignTypeSelecting"
                                                                :allow-clear="false"
                                                            >
                                                                <option disabled value=""></option>
                                                            </select2>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <div class="col-sm-4">
                                                                    <label class="control-label">N (滿額) = <span
                                                                            style="color: red;">*</span></label>
                                                                </div>
                                                                <div class="col-sm-8">
                                                                    <input type="number" class="form-control"
                                                                        name="n_value" v-model="form.nValue" min="1"
                                                                        :disabled="isNowGreaterThanOrEqualToStartAt">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6" v-if="showXValue">
                                                            <div class="form-group">
                                                                <div class="col-sm-3">
                                                                    <label class="control-label">X (折扣) = <span
                                                                            style="color: red;">*</span></label>
                                                                </div>
                                                                <div class="col-sm-5">
                                                                    <input type="number" class="form-control"
                                                                        name="x_value" v-model="form.xValue"
                                                                        :disabled="isNowGreaterThanOrEqualToStartAt">
                                                                </div>
                                                                <div class="col-sm-4" v-if="showXValueHint">
                                                                    <p class="form-control-static">打85折，輸入0.85</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br />

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="col-sm-2">
                                                            <label class="control-label">上架時間起 <span
                                                                    style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <div class="input-group" id="start_at_flatpickr">
                                                                <input type="text" class="form-control" name="start_at"
                                                                    autocomplete="off" data-input v-model="form.startAt"
                                                                    :disabled="isNowGreaterThanOrEqualToStartAt">
                                                                <span class="input-group-btn" data-toggle>
                                                                    <button class="btn btn-default" type="button">
                                                                        <i class="fa-solid fa-calendar-days"></i>
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="col-sm-2">
                                                            <label class="control-label">上架時間訖 <span
                                                                    style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <div class="input-group" id="end_at_flatpickr">
                                                                <input type="text" class="form-control" name="end_at"
                                                                    autocomplete="off" data-input v-model="form.endAt">
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
                                            <br />

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="col-sm-2">
                                                            <label class="control-label">前台文案 <span
                                                                    style="color: red;">*</span></label>
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <input type="text" class="form-control" name="campaign_brief"
                                                                v-model="form.campaignBrief">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="col-sm-2">
                                                            <label class="control-label">
                                                                庫存類型 <span style="color: red;">*</span>
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <label class="radio-inline">
                                                                <input
                                                                    type="radio"
                                                                    name="stock_type"
                                                                    value="A_B"
                                                                    v-model="form.stockType"
                                                                    @click="clickStockType"
                                                                    :disabled="isNowGreaterThanOrEqualToStartAt"
                                                                >買斷 / 寄售
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input
                                                                    type="radio"
                                                                    name="stock_type"
                                                                    value="T"
                                                                    v-model="form.stockType"
                                                                    @click="clickStockType"
                                                                    :disabled="isNowGreaterThanOrEqualToStartAt"
                                                                >轉單
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <div class="col-sm-2">
                                                            <label class="control-label">
                                                                供應商 <span style="color: red;">*</span>
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-10">
                                                            <select2
                                                                class="form-control"
                                                                :options="suppliers"
                                                                v-model="form.supplierId"
                                                                name="supplier_id"
                                                                :allow-clear="false"
                                                                @select2-selecting="onSupplierSelecting"
                                                                :disabled="isNowGreaterThanOrEqualToStartAt"
                                                            >
                                                                <option disabled value=""></option>
                                                            </select2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr style="border-top: 1px solid gray;" />
                                        </div>

                                        <div>
                                            <div class="form-group">
                                                <label class="control-label">適用對象 <span
                                                        style="color: red;">*</span></label>

                                                <div class="row" style="margin-left: 1rem;">
                                                    <div class="col-sm-2">
                                                        <label class="radio-inline">
                                                            <input type="radio" name="target_groups" value="all"
                                                                v-model="form.targetGroups"
                                                                :disabled="isNowGreaterThanOrEqualToStartAt">所有會員
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr style="border-top: 1px solid gray;" />
                                        </div>

                                        <div>
                                            <div class="row">
                                                <div class="col-sm-1">
                                                    <p>單品清單</p>
                                                </div>
                                                <div class="col-sm-2" v-if="!isNowGreaterThanOrEqualToStartAt">
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        @click="addProduct">
                                                        <i class="fa-solid fa-plus"></i> 新增單品
                                                    </button>
                                                </div>
                                            </div>
                                            <br>

                                            <div class="table-responsive">
                                                <table class='table table-striped table-bordered table-hover'
                                                    style='width:100%'>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-nowrap">項次</th>
                                                            <th class="text-nowrap">商品序號</th>
                                                            <th class="text-nowrap">商品名稱</th>
                                                            <th class="text-nowrap">售價(含稅)</th>
                                                            <th class="text-nowrap">上架日期</th>
                                                            <th class="text-nowrap">上架狀態</th>
                                                            <th class="text-nowrap">毛利(%)</th>
                                                            <th class="text-nowrap"
                                                                v-if="!isNowGreaterThanOrEqualToStartAt">功能</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(product, index) in form.products"
                                                            :key="index">
                                                            <input type="hidden" :name="`products[${index}][id]`"
                                                                :value="product.id">
                                                            <input type="hidden" :name="`products[${index}][product_id]`"
                                                                :value="product.productId">
                                                            <td>@{{ index + 1 }}</td>
                                                            <td>@{{ product.productNo }}</td>
                                                            <td>@{{ product.productName }}</td>
                                                            <td>@{{ product.sellingPriceForDisplay }}</td>
                                                            <td>@{{ product.launchedAt }}</td>
                                                            <td>@{{ product.launchStatus }}</td>
                                                            <td>@{{ product.grossMargin }}</td>
                                                            <td v-if="!isNowGreaterThanOrEqualToStartAt">
                                                                <button type="button" class="btn btn-danger"
                                                                    @click="deleteProduct(index)">
                                                                    <i class="fa-solid fa-trash-can"></i> 刪除
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <hr style="border-top: 1px solid gray;" />
                                        </div>

                                        <div v-if="showGiveaway">
                                            <div class="row">
                                                <div class="col-sm-1">
                                                    <p>贈品清單</p>
                                                </div>
                                                <div class="col-sm-2" v-if="!isNowGreaterThanOrEqualToStartAt">
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        @click="addGiveaway">
                                                        <i class="fa-solid fa-plus"></i> 新增贈品
                                                    </button>
                                                </div>
                                            </div>
                                            <br>

                                            <div class="table-responsive">
                                                <table class='table table-striped table-bordered table-hover'
                                                    style='width:100%'>
                                                    <thead>
                                                        <tr>
                                                            <th class="text-nowrap">項次</th>
                                                            <th class="text-nowrap">商品序號</th>
                                                            <th class="text-nowrap">商品名稱</th>
                                                            <th class="text-nowrap">贈品數量</th>
                                                            <th class="text-nowrap">庫存數</th>
                                                            <th class="text-nowrap">上架狀態</th>
                                                            <th class="text-nowrap"
                                                                v-if="!isNowGreaterThanOrEqualToStartAt">功能</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(giveaway, index) in form.giveaways"
                                                            :key="index">
                                                            <input type="hidden" :name="`giveaways[${index}][id]`"
                                                                :value="giveaway.id">
                                                            <input type="hidden" :name="`giveaways[${index}][product_id]`"
                                                                :value="giveaway.productId">
                                                            <td>@{{ index + 1 }}</td>
                                                            <td>@{{ giveaway.productNo }}</td>
                                                            <td>@{{ giveaway.productName }}</td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="number"
                                                                        class="form-control giveaway-assigned-qty"
                                                                        :name="`giveaways[${index}][assigned_qty]`" min="1"
                                                                        v-model="giveaway.assignedQty"
                                                                        :disabled="isNowGreaterThanOrEqualToStartAt">
                                                                </div>
                                                            </td>
                                                            <td>@{{ giveaway.stockQty }}</td>
                                                            <td>@{{ giveaway.launchStatus }}</td>
                                                            <td v-if="!isNowGreaterThanOrEqualToStartAt">
                                                                <button type="button" class="btn btn-danger"
                                                                    @click="deleteGiveaway(index)">
                                                                    <i class="fa-solid fa-trash-can"></i> 刪除
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <hr style="border-top: 1px solid gray;" />
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    @if ($share_role_auth['auth_update'])
                                                        <button class="btn btn-success" type="button" @click="submitForm"
                                                            :disabled="saveButton.isDisabled">
                                                            <i class="fa-solid fa-floppy-disk"></i> 儲存
                                                        </button>
                                                    @endif
                                                    <a href="{{ route('promotional_campaign_prd') }}"
                                                        class="btn btn-danger">
                                                        <i class="fa-solid fa-ban"></i> 取消
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <prd-campaign-product-modal :modal="productModal" @save="saveProductModalProducts"></prd-campaign-product-modal>
        <prd-campaign-product-modal :modal="giveawayModal" @save="saveGiveawayModalProducts"></prd-campaign-product-modal>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/promotional_campaign/prd/main.js') }}"></script>
    <script>
        let vm = new Vue({
            el: "#app",
            data: {
                form: {
                    campaignId: "",
                    campaignName: "",
                    active: "0",
                    campaignType: "",
                    nValue: "",
                    xValue: "",
                    startAt: "",
                    endAt: "",
                    campaignBrief: "",
                    targetGroups: "all",
                    giveaways: [],
                    products: [],
                    stockType: "",
                    supplierId: "all",
                },
                saveButton: {
                    isDisabled: false,
                },
                campaignTypes: [],
                suppliers: [{
                    text: "全部",
                    id: "all",
                }],
                productModal: {
                    id: "product-modal",
                    title: "新增單品",
                    supplier: {
                        id: "",
                        isDisabled: true,
                    },
                    productType: {
                        id: "N",
                        includeOptions: ["N"],
                    },
                    stockType: "",
                    excludeProductIds: [],
                },
                giveawayModal: {
                    id: "giveaway-modal",
                    title: "新增贈品",
                    supplier: {
                        id: "",
                        isDisabled: true,
                    },
                    productType: {
                        id: "G",
                        includeOptions: ["N", "G"],
                    },
                    stockType: "",
                    excludeProductIds: [],
                },
                showXValue: true,
                showXValueHint: true,
                showGiveaway: false,
                isNowGreaterThanOrEqualToStartAt: false,
            },
            created() {
                let campaignTypes = @json($campaignTypes);
                let suppliers = @json($suppliers);
                let campaign = @json($campaign);

                if (campaignTypes) {
                    campaignTypes.forEach(campaignType => {
                        this.campaignTypes.push({
                            text: campaignType.description,
                            id: campaignType.code,
                            udf03: campaignType.udf_03,
                        });
                    });
                }

                if (suppliers) {
                    suppliers.forEach(supplier => {
                        this.suppliers.push({
                            text: supplier.name,
                            id: supplier.id,
                        });
                    });
                }

                if (campaign) {
                    this.form.campaignId = campaign.id;
                    this.form.campaignName = campaign.campaign_name;
                    this.form.active = campaign.active;
                    this.form.campaignType = campaign.campaign_type;
                    this.form.nValue = campaign.n_value;
                    this.form.xValue = campaign.x_value;
                    this.form.startAt = campaign.start_at;
                    this.form.endAt = campaign.end_at;
                    this.form.campaignBrief = campaign.campaign_brief;
                    this.form.stockType = campaign.stock_type;
                    this.form.supplierId = campaign.supplier_id;

                    if (Array.isArray(campaign.products) && campaign.products.length) {
                        campaign.products.forEach(product => {
                            this.form.products.push({
                                id: product.id,
                                productId: product.product_id,
                                productNo: product.product_no,
                                productName: product.product_name,
                                sellingPrice: product.selling_price,
                                sellingPriceForDisplay: product.selling_price.toLocaleString('en-US'),
                                launchedAt: product.start_launched_at || product.end_launched_at ?
                                    `${product.start_launched_at} ~ ${product.end_launched_at}` :
                                    '',
                                launchStatus: product.launch_status,
                                grossMargin: product.gross_margin,
                            });
                        });
                    }

                    if (Array.isArray(campaign.giveaways) && campaign.giveaways.length) {
                        campaign.giveaways.forEach(giveaway => {
                            this.form.giveaways.push({
                                id: giveaway.id,
                                productId: giveaway.product_id,
                                productNo: giveaway.product_no,
                                productName: giveaway.product_name,
                                assignedQty: giveaway.assigned_qty,
                                stockQty: giveaway.stock_qty,
                                launchStatus: giveaway.launch_status,
                            });
                        });
                    }
                }

                // 若當下時間 ≧ ﹝上架時間起﹞，僅開放﹝活動名稱﹞、﹝狀態﹞、﹝上架時間訖﹞供修改
                if (moment().isSameOrAfter(this.form.startAt)) {
                    this.isNowGreaterThanOrEqualToStartAt = true;
                }
            },
            mounted() {
                let self = this;

                if (this.$refs.errorMessage) {
                    alert(this.$refs.errorMessage.innerText.trim());
                }

                let startAtLastSelectedDate;
                let startAtFlatpickr = flatpickr("#start_at_flatpickr", {
                    dateFormat: "Y-m-d H:i:S",
                    maxDate: this.form.endAt,
                    enableTime: true,
                    enableSeconds: true,
                    defaultHour: 0,
                    defaultMinute: 0,
                    defaultSeconds: 0,
                    onChange: function(selectedDates, dateStr, instance) {
                        let selectedDate = selectedDates[0];

                        // 沒有選過日期 或 選到的日期和上一次選到的日期不同天
                        if (!startAtLastSelectedDate || !moment(selectedDate).isSame(
                                startAtLastSelectedDate, 'day')) {
                            // 判斷選到的日期是否為當天日期
                            if (moment(selectedDate).isSame(moment(), 'day')) {
                                selectedDate = moment().add(5, 'minutes').seconds(0).toDate();
                                this.setDate(selectedDate);
                            }
                        }

                        startAtLastSelectedDate = selectedDate;

                        endAtFlatpickr.set('minDate', selectedDate);

                        if (!endAtFlatpickr.input.value) {
                            endAtFlatpickr.hourElement.value = 23;
                            endAtFlatpickr.minuteElement.value = 59;
                            endAtFlatpickr.secondElement.value = 59;
                        }
                    },
                });

                let endAtFlatpickr = flatpickr("#end_at_flatpickr", {
                    dateFormat: "Y-m-d H:i:S",
                    minDate: this.form.startAt,
                    enableTime: true,
                    enableSeconds: true,
                    defaultHour: 23,
                    defaultMinute: 59,
                    defaultSeconds: 59,
                    onChange: function(selectedDates, dateStr, instance) {
                        startAtFlatpickr.set('maxDate', dateStr);
                    },
                });

                let conflictContents = '';
                // 驗證表單
                $("#edit-form").validate({
                    // debug: true,
                    submitHandler: function(form) {
                        self.saveButton.isDisabled = true;
                        form.submit();
                    },
                    rules: {
                        campaign_name: {
                            required: true,
                            maxlength: 80,
                        },
                        active: {
                            required: true,
                            remote: {
                                param: function() {
                                    let productIds = self.form.products.map((product) => {
                                        return product.productId;
                                    });

                                    return {
                                        url: "/backend/promotional-campaign-prd/can-active",
                                        type: "post",
                                        dataType: "json",
                                        cache: false,
                                        data: {
                                            campaign_type: self.form.campaignType,
                                            start_at: self.form.startAt,
                                            end_at: self.form.endAt,
                                            product_ids: productIds,
                                            exclude_promotional_campaign_id: self.form.campaignId,
                                        },
                                        dataFilter: function(response) {
                                            conflictContents = "";
                                            if (response) {
                                                let data = JSON.parse(response);

                                                if (data.status) {
                                                    return true;
                                                }

                                                if (data.conflict_contents) {
                                                    conflictContents += "衝突的活動名稱: ";
                                                    data.conflict_contents.forEach((content, index,
                                                        array) => {
                                                        conflictContents +=
                                                            `${content.campaign_name} (商品${content.product_no})`;

                                                        if (index != array.length - 1) {
                                                            conflictContents += "、";
                                                        }
                                                    });
                                                }
                                            }

                                            return false;
                                        },
                                    }
                                },
                                depends: function(element) {
                                    return self.form.campaignType &&
                                        self.form.startAt &&
                                        self.form.endAt &&
                                        self.form.products &&
                                        self.form.products.length &&
                                        self.form.active == 1;
                                }
                            },
                        },
                        campaign_type: {
                            required: true,
                        },
                        start_at: {
                            required: true,
                        },
                        end_at: {
                            required: true,
                            greaterThan: function() {
                                return self.form.startAt;
                            },
                        },
                        n_value: {
                            required: true,
                            digits: true,
                            min: 1,
                        },
                        x_value: {
                            required: true,
                            min: function() {
                                if (['PRD01', 'PRD03'].includes(self.form.campaignType)) {
                                    return 0.01;
                                } else if (['PRD02', 'PRD04'].includes(self.form.campaignType)) {
                                    return 1;
                                }

                                return 0;
                            },
                            max: {
                                param: 0.99,
                                depends: function(element) {
                                    return ['PRD01', 'PRD03'].includes(self.form.campaignType);
                                },
                            },
                            maxlength: {
                                param: 4,
                                depends: function(element) {
                                    return ['PRD01', 'PRD03'].includes(self.form.campaignType);
                                },
                            },
                            digits: {
                                depends: function(element) {
                                    return ['PRD02', 'PRD04'].includes(self.form.campaignType);
                                },
                            },
                            number: {
                                depends: function(element) {
                                    return ['PRD01', 'PRD03'].includes(self.form.campaignType);
                                },
                            },
                            compareDiscountAndSellingPrice: {
                                param: function() {
                                    return self.form.products;
                                },
                                depends: function(element) {
                                    return ['PRD02', 'PRD04'].includes(self.form.campaignType);
                                },
                            },
                        },
                        target_groups: {
                            required: true,
                        },
                        campaign_brief: {
                            required: true,
                            maxlength: 20,
                        },
                        stock_type: {
                            required: true,
                        },
                        supplier_id: {
                            required: true,
                        },
                    },
                    messages: {
                        end_at: {
                            greaterThan: "結束時間必須大於開始時間",
                        },
                        active: {
                            remote: function(element) {
                                if (['PRD01', 'PRD02', 'PRD03', 'PRD04'].includes(self.form
                                        .campaignType)) {
                                    return `同一時間點、同一單品不可存在其他生效的﹝第N件(含)以上打X折﹞、﹝第N件(含)以上折X元﹞、﹝滿N件，每件打X折﹞、﹝滿N件，每件折X元﹞的行銷活動<br/>
                                        ${conflictContents}`;
                                } else if (['PRD05'].includes(self.form.campaignType)) {
                                    return `同一時間點、同一單品不可存在其他生效的﹝買N件，送贈品﹞的行銷活動<br/>
                                        ${conflictContents}`;
                                }
                            },
                        },
                        n_value: {
                            digits: "只可輸入正整數",
                            min: "只可輸入正整數",
                        },
                        x_value: {
                            digits: "只可輸入正整數",
                            min: function() {
                                if (['PRD02', 'PRD04'].includes(self.form.campaignType)) {
                                    return '只可輸入正整數';
                                }

                                return '請輸入不小於 0 的數值';
                            },
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
            watch: {
                "form.stockType"(value) {
                    // 若「庫存類型」為「轉單」且「活動類型」為「GIFT」：「供應商」欄位不允許選「全部」，一定要指定到單一供應商。
                    if (value == "T") {
                        let campaignType = this.campaignTypes.find(campaignType => campaignType.id == this.form.campaignType);
                        if (campaignType && campaignType.udf03 == "GIFT") {
                            this.$set(this.suppliers[0], "disabled", true);
                            if (this.form.supplierId == "all") {
                                this.form.supplierId = "";
                            }
                        }
                    }
                    // 若「庫存類型」為「買斷/寄售」：可選擇「全部」、亦可指定到單一供應商。
                    else {
                        if (this.suppliers[0].hasOwnProperty('disabled')) {
                            this.suppliers[0].disabled = false;
                        }
                    }

                    this.productModal.stockType = value;
                    this.giveawayModal.stockType = value;
                },
                "form.supplierId"(value) {
                    this.productModal.supplier.id = value;
                    this.giveawayModal.supplier.id = value;
                },
                "form.campaignType"(value) {
                    if (['PRD01', 'PRD02', 'PRD03', 'PRD04'].includes(value)) {
                        this.showXValue = true;
                        this.showGiveaway = false;
                    } else {
                        this.showXValue = false;
                        this.showGiveaway = true;
                    }

                    if (['PRD01', 'PRD03'].includes(value)) {
                        this.showXValueHint = true;
                    } else {
                        this.showXValueHint = false;
                    }

                    let campaignType = this.campaignTypes.find(campaignType => campaignType.id == value);
                    // 若「活動類型」為「GIFT」且「庫存類型」為「轉單」：「供應商」欄位不允許選「全部」，一定要指定到單一供應商。
                    if (campaignType && campaignType.udf03 == "GIFT") {
                        if (this.form.stockType == "T") {
                            this.$set(this.suppliers[0], "disabled", true);
                            if (this.form.supplierId == "all") {
                                this.form.supplierId = "";
                            }
                        }
                    }
                    // 若「活動類型」非「GIFT」：可選擇「全部」、亦可指定到單一供應商。
                    else {
                        if (this.suppliers[0].hasOwnProperty('disabled')) {
                            this.suppliers[0].disabled = false;
                        }
                    }
                },
            },
            methods: {
                // 新增贈品
                addGiveaway() {
                    if (!this.form.stockType || !this.form.supplierId) {
                        alert("尚未指定「庫存類型」、「供應商」，不允許新增贈品！");
                        return;
                    }

                    this.giveawayModal.excludeProductIds = [];
                    this.form.giveaways.forEach(giveaway => {
                        this.giveawayModal.excludeProductIds.push(giveaway.productId);
                    });

                    $('#giveaway-modal').modal('show');
                },
                // 刪除贈品
                deleteGiveaway(index) {
                    if (confirm('確定要刪除嗎？')) {
                        this.form.giveaways.splice(index, 1);
                    }
                },
                // 新增商品
                addProduct() {
                    if (!this.form.stockType || !this.form.supplierId) {
                        alert("尚未指定「庫存類型」、「供應商」，不允許新增商品！");
                        return;
                    }

                    this.productModal.excludeProductIds = [];
                    this.form.products.forEach(product => {
                        this.productModal.excludeProductIds.push(product.productId);
                    });

                    $('#product-modal').modal('show');
                },
                // 刪除商品
                deleteProduct(index) {
                    if (confirm('確定要刪除嗎？')) {
                        this.form.products.splice(index, 1);
                    }
                },
                // 儲存
                submitForm() {
                    $(`.giveaway-assigned-qty`).each(function() {
                        $(this).rules("add", {
                            required: true,
                            digits: true,
                            min: 1,
                        });
                    });

                    $("#edit-form").submit();
                },
                // 儲存商品modal的商品
                saveProductModalProducts(products) {
                    products.forEach(product => {
                        this.form.products.push({
                            productId: product.id,
                            productNo: product.productNo,
                            productName: product.productName,
                            sellingPrice: product.sellingPrice,
                            sellingPriceForDisplay: product.sellingPriceForDisplay,
                            launchedAt: product.launchedAt,
                            launchStatus: product.launchStatus,
                            grossMargin: product.grossMargin,
                        });
                    });
                },
                // 儲存贈品modal的商品
                saveGiveawayModalProducts(products) {
                    products.forEach(product => {
                        this.form.giveaways.push({
                            productId: product.id,
                            productNo: product.productNo,
                            productName: product.productName,
                            assignedQty: 1,
                            stockQty: product.stockQty,
                            launchStatus: product.launchStatus,
                        });
                    });
                },
                // 點擊庫存類型
                clickStockType(event) {
                    if (this.form.stockType == event.target.value) {
                        return;
                    }

                    let errorMessage = "";
                    // 未選擇活動類型
                    if (!this.form.campaignType) {
                        errorMessage += "請先選擇「活動類型」，才能切換「庫存類型」！\n"
                    }

                    // 已有指定贈品
                    if (this.form.giveaways.length) {
                        errorMessage += "請先刪除贈品設定，才能切換「庫存類型」！\n"
                    }

                    // 已有指定商品
                    if (this.form.products.length) {
                        errorMessage += "請先刪除商品設定，才能切換「庫存類型」！\n"
                    }

                    if (errorMessage) {
                        event.preventDefault();
                        alert(errorMessage);
                    }
                },
                // 當選擇供應商時
                onSupplierSelecting(event) {
                    if (event.params.args.data.id == "all") {
                        return;
                    }

                    let errorMessage = "";
                    // 已有指定贈品
                    if (this.form.giveaways.length) {
                        errorMessage += "請先刪除贈品設定，才能切換「供應商」！\n"
                    }

                    // 已有指定商品
                    if (this.form.products.length) {
                        errorMessage += "請先刪除商品設定，才能切換「供應商」！\n"
                    }

                    if (errorMessage) {
                        event.preventDefault();
                        alert(errorMessage);
                    }
                },
                // 當選擇活動類型時
                onCampaignTypeSelecting(event) {
                    let errorMessage = "";
                    // 已有指定贈品
                    if (this.form.giveaways.length) {
                        errorMessage += "請先刪除贈品設定，才能切換「活動類型」！\n"
                    }

                    // 已有指定商品
                    if (this.form.products.length) {
                        errorMessage += "請先刪除商品設定，才能切換「活動類型」！\n"
                    }

                    if (errorMessage) {
                        event.preventDefault();
                        alert(errorMessage);
                    }
                },
            },
        });
    </script>
@endsection
