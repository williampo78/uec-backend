@extends('backend.master')

@section('title', '購物車滿額活動 新增資料')

@section('style')
    <style>
        .modal-dialog {
            max-width: 100%;
        }

        .tab-content {
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            padding: 30px;
        }

    </style>
@endsection

@section('content')
    <div id="app">
        @if ($errors->any())
            <div id="error-message" style="display: none;">
                {{ $errors->first('message') }}
            </div>
        @endif

        <div id="page-wrapper">
            <!-- 表頭名稱 -->
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header"><i class="fa-solid fa-plus"></i> 購物車滿額活動 新增資料</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">請輸入下列欄位資料</div>
                        <div class="panel-body">
                            <form id="create-form" method="post"
                                action="{{ route('promotional_campaign_cart_v2.store') }}">
                                @csrf
                                <div class="form-horizontal">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label class="control-label">活動名稱 <span
                                                            style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <input type="text" class="form-control" name="campaign_name"
                                                        v-model="form.campaignName">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label for="campaign_type" class="control-label">活動類型 <span
                                                            style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <select2 class="form-control" :options="campaignTypes"
                                                        v-model="form.campaignType" name="campaign_type">
                                                        <option disabled value=""></option>
                                                    </select2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label class="control-label">狀態 <span
                                                            style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <div class="row">
                                                        <div class="col-sm-1">
                                                            <label class="radio-inline">
                                                                <input type="radio" name="active" value="1"
                                                                    v-model="form.active">生效
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-1">
                                                            <label class="radio-inline">
                                                                <input type="radio" name="active" value="0"
                                                                    v-model="form.active">失效
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label class="control-label">上架時間 <span
                                                            style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <div class="row">
                                                        <div class="col-sm-5">
                                                            <div class="input-group" id="start_at_flatpickr">
                                                                <input type="text" class="form-control" name="start_at"
                                                                    id="start_at" autocomplete="off" data-input
                                                                    v-model="form.startAt">
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
                                                            <div class="input-group" id="end_at_flatpickr">
                                                                <input type="text" class="form-control" name="end_at"
                                                                    id="end_at" autocomplete="off" data-input
                                                                    v-model="form.endAt" />
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
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label class="control-label">前台文案 <span
                                                            style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <input type="text" class="form-control" name="campaign_brief"
                                                        v-model="form.campaignBrief">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label class="control-label">前台URL <span
                                                            style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <input type="text" class="form-control" name="url_code"
                                                        v-model="form.urlCode">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label class="control-label">庫存類型 <span
                                                            style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <div class="row">
                                                        <div class="col-sm-1">
                                                            <label class="radio-inline">
                                                                <input type="radio" name="stock_type" value="A_B"
                                                                    v-model="form.stockType" @click="clickStockType">買斷 / 寄售
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-1">
                                                            <label class="radio-inline">
                                                                <input type="radio" name="stock_type" value="T"
                                                                    v-model="form.stockType" @click="clickStockType">轉單
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label for="campaign_type" class="control-label">供應商 <span
                                                            style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <select2 class="form-control" :options="suppliers"
                                                        v-model="form.supplierId" name="supplier_id" :allow-clear="false">
                                                        <option disabled value=""></option>
                                                    </select2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr style="border-top: 1px solid gray;" />
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs">
                                            <li class="active">
                                                <a href="#tab-threshold" data-toggle="tab">門檻</a>
                                            </li>
                                            <li>
                                                <a href="#tab-product" data-toggle="tab">指定商品</a>
                                            </li>
                                            <li>
                                                <a href="#tab-banner" data-toggle="tab">Banner</a>
                                            </li>
                                        </ul>

                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <div class="tab-pane fade in active" id="tab-threshold">
                                                <div v-if="form.campaignType === 'CART01'">
                                                    <div class="row">
                                                        <div class="col-sm-1">
                                                            <button type="button" class="btn btn-warning"
                                                                @click="addThreshold">
                                                                <i class="fa-solid fa-plus"></i> 新增門檻
                                                            </button>
                                                        </div>
                                                        <div class="col-sm-11">
                                                            <p class="text-primary form-control-static">※ 打「85折」時，折數輸入「0.85」
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="table-responsive">
                                                        <table class='table table-striped table-bordered table-hover'
                                                            style='width:100%'>
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-nowrap">項次</th>
                                                                    <th class="text-nowrap">N (滿額) <span
                                                                            style="color:red;">*</span></th>
                                                                    <th class="text-nowrap">X (折數) <span
                                                                            style="color:red;">*</span></th>
                                                                    <th class="text-nowrap">功能</th>
                                                                    <th class="text-nowrap"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(threshold, index) in form.thresholds"
                                                                    :key="index">
                                                                    <td>@{{ index + 1 }}</td>
                                                                    <td>
                                                                        <input type="number" class="form-control"
                                                                            :name="`thresholds[${index}][n_value]`" min="0"
                                                                            v-model="threshold.nValue">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" class="form-control"
                                                                            :name="`thresholds[${index}][x_value]`" min="0"
                                                                            v-model="threshold.xValue">
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-danger"
                                                                            @click="deleteThreshold(index)">
                                                                            <i class="fa-solid fa-trash-can"></i> 刪除
                                                                        </button>
                                                                    </td>
                                                                    <td>
                                                                        @{{ thresholdBrief(threshold) }}
                                                                        <input type="hidden"
                                                                            :name="`thresholds[${index}][threshold_brief]`">
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div v-else-if="form.campaignType === 'CART02'">
                                                    <div class="row">
                                                        <div class="col-sm-1">
                                                            <button type="button" class="btn btn-warning"
                                                                @click="addThreshold">
                                                                <i class="fa-solid fa-plus"></i> 新增門檻
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
                                                                    <th class="text-nowrap">N (滿額) <span
                                                                            style="color:red;">*</span></th>
                                                                    <th class="text-nowrap">X (折扣金額) <span
                                                                            style="color:red;">*</span></th>
                                                                    <th class="text-nowrap">功能</th>
                                                                    <th class="text-nowrap"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(threshold, index) in form.thresholds"
                                                                    :key="index">
                                                                    <td>@{{ index + 1 }}</td>
                                                                    <td>
                                                                        <input type="number" class="form-control"
                                                                            :name="`thresholds[${index}][n_value]`" min="0"
                                                                            v-model="threshold.nValue">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" class="form-control"
                                                                            :name="`thresholds[${index}][x_value]`" min="0"
                                                                            v-model="threshold.xValue">
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-danger"
                                                                            @click="deleteThreshold(index)">
                                                                            <i class="fa-solid fa-trash-can"></i> 刪除
                                                                        </button>
                                                                    </td>
                                                                    <td>
                                                                        @{{ thresholdBrief(threshold) }}
                                                                        <input type="hidden"
                                                                            :name="`thresholds[${index}][threshold_brief]`">
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div v-else-if="form.campaignType === 'CART03'">
                                                    <div class="row">
                                                        <div class="col-sm-1">
                                                            <button type="button" class="btn btn-warning"
                                                                @click="addThreshold">
                                                                <i class="fa-solid fa-plus"></i> 新增門檻
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
                                                                    <th class="text-nowrap">N (滿額) <span
                                                                            style="color:red;">*</span></th>
                                                                    <th class="text-nowrap">贈品 <span
                                                                            style="color:red;">*</span></th>
                                                                    <th class="text-nowrap">功能</th>
                                                                    <th class="text-nowrap"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(threshold, index) in form.thresholds"
                                                                    :key="index">
                                                                    <td>@{{ index + 1 }}</td>
                                                                    <td>
                                                                        <input type="number" class="form-control"
                                                                            :name="`thresholds[${index}][n_value]`" min="0"
                                                                            v-model="threshold.nValue">
                                                                    </td>
                                                                    <td>
                                                                        @include('backend.promotional_campaign.cart_v2.giveaway_block')
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-danger"
                                                                            @click="deleteThreshold(index)">
                                                                            <i class="fa-solid fa-trash-can"></i> 刪除
                                                                        </button>
                                                                    </td>
                                                                    <td>
                                                                        @{{ thresholdBrief(threshold) }}
                                                                        <input type="hidden"
                                                                            :name="`thresholds[${index}][threshold_brief]`">
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div v-else-if="form.campaignType === 'CART04'">
                                                    <div class="row">
                                                        <div class="col-sm-1">
                                                            <button type="button" class="btn btn-warning"
                                                                @click="addThreshold">
                                                                <i class="fa-solid fa-plus"></i> 新增門檻
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
                                                                    <th class="text-nowrap">N (滿件) <span
                                                                            style="color:red;">*</span></th>
                                                                    <th class="text-nowrap">贈品 <span
                                                                            style="color:red;">*</span></th>
                                                                    <th class="text-nowrap">功能</th>
                                                                    <th class="text-nowrap"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(threshold, index) in form.thresholds"
                                                                    :key="index">
                                                                    <td>@{{ index + 1 }}</td>
                                                                    <td>
                                                                        <input type="number" class="form-control"
                                                                            :name="`thresholds[${index}][n_value]`" min="0"
                                                                            v-model="threshold.nValue">
                                                                    </td>
                                                                    <td>
                                                                        @include('backend.promotional_campaign.cart_v2.giveaway_block')
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-danger"
                                                                            @click="deleteThreshold(index)">
                                                                            <i class="fa-solid fa-trash-can"></i> 刪除
                                                                        </button>
                                                                    </td>
                                                                    <td>
                                                                        @{{ thresholdBrief(threshold) }}
                                                                        <input type="hidden"
                                                                            :name="`thresholds[${index}][threshold_brief]`">
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab-product">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <button type="button" class="btn btn-warning" @click="addProduct">
                                                            <i class="fa-solid fa-plus"></i> 新增商品
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
                                                                <th class="text-nowrap">開賣時間</th>
                                                                <th class="text-nowrap">上架狀態</th>
                                                                <th class="text-nowrap">毛利(%)</th>
                                                                <th class="text-nowrap">前台分類</th>
                                                                <th class="text-nowrap">功能</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(product, index) in form.products"
                                                                :key="index">
                                                                <input type="hidden"
                                                                    :name="`products[${index}][product_id]`">
                                                                <td>@{{ index + 1 }}</td>
                                                                <td>@{{ product.productNo }}</td>
                                                                <td>@{{ product.productName }}</td>
                                                                <td>@{{ product.sellingPrice }}</td>
                                                                <td>@{{ product.startAt }}</td>
                                                                <td>@{{ product.launchedStatus }}</td>
                                                                <td>@{{ product.grossMargin }}</td>
                                                                <td>@{{ product.category }}</td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger"
                                                                        @click="deleteProduct(index)">
                                                                        <i class="fa-solid fa-trash-can"></i> 刪除
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab-banner">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label>Desktop版</label>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <p>(1) size不可超過1MB、(2) 副檔名須為JPG、JPEG、PNG、(3) 寬*高至少須為1200*150</p>
                                                        {{-- <img src="" class="img-responsive" width="300" height="300"> --}}
                                                        <input type="file" name="banner_photo_desktop">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="button" class="btn btn-danger"
                                                            @click="deleteBannerPhotoDesktop">
                                                            <i class="fa-solid fa-trash-can"></i> 刪除
                                                        </button>
                                                    </div>
                                                </div>
                                                <hr style="border-top: 1px solid gray;">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label>Mobile版</label>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <p>(1) size不可超過1MB、(2) 副檔名須為JPG、JPEG、PNG、(3) 寬*高至少須為345*180</p>
                                                        {{-- <img src="" class="img-responsive" width="300" height="300"> --}}
                                                        <input type="file" name="banner_photo_mobile">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="button" class="btn btn-danger"
                                                            @click="deleteBannerPhotoMobile">
                                                            <i class="fa-solid fa-trash-can"></i> 刪除
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr style="border-top: 1px solid gray;">

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            @if ($share_role_auth['auth_create'])
                                                <button class="btn btn-success" type="button" @click="submitForm"
                                                    :disabled='form.save.isDisabled'>
                                                    <i class="fa-solid fa-floppy-disk"></i> 儲存
                                                </button>
                                            @endif
                                            <a href="{{ route('promotional_campaign_cart_v2') }}" class="btn btn-danger"
                                                id="btn-cancel"><i class="fa-solid fa-ban"></i> 取消
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <product-modal :id="`product-modal`" :modal-title="`新增指定商品`"></product-modal>
        <product-modal :id="`giveaway-modal`" :modal-title="`新增贈品`"></product-modal>
        {{-- @include('backend.promotional_campaign.cart_v2.prd_modal') --}}
        {{-- @include('backend.promotional_campaign.cart_v2.gift_modal') --}}
    </div>
@endsection

@section('js')
    <script src="{{ mix('js/promotional_campaign/cart/main.js') }}"></script>
    <script>
        let vm = new Vue({
            el: "#app",
            data: {
                form: {
                    campaignName: "",
                    campaignType: "",
                    active: "0",
                    startAt: "",
                    endAt: "",
                    campaignBrief: "",
                    urlCode: "",
                    stockType: "",
                    supplierId: "all",
                    thresholds: [],
                    products: [],
                    save: {
                        isDisabled: false,
                    },
                },
                campaignTypes: [],
                suppliers: [{
                    text: "全部",
                    id: "all",
                }],
            },
            created() {
                let campaignTypes = @json($campaignTypes);
                let suppliers = @json($suppliers);

                if (campaignTypes) {
                    campaignTypes.forEach(campaignType => {
                        this.campaignTypes.push({
                            text: campaignType.description,
                            id: campaignType.code,
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
            },
            mounted() {
                let self = this;

                if (this.$refs.errorMessage) {
                    alert(this.$refs.errorMessage.innerText.trim());
                }

                let startAtFlatpickr = flatpickr("#start_at_flatpickr", {
                    dateFormat: "Y-m-d H:i:S",
                    minDate: moment().format("YYYY-MM-DD"),
                    enableTime: true,
                    enableSeconds: true,
                    defaultHour: 0,
                    defaultMinute: 0,
                    defaultSeconds: 0,
                    onChange: function(selectedDates, dateStr, instance) {
                        endAtFlatpickr.set('minDate', dateStr);

                        if (!endAtFlatpickr.input.value) {
                            endAtFlatpickr.hourElement.value = 23;
                            endAtFlatpickr.minuteElement.value = 59;
                            endAtFlatpickr.secondElement.value = 59;
                        }
                    },
                });

                let endAtFlatpickr = flatpickr("#end_at_flatpickr", {
                    dateFormat: "Y-m-d H:i:S",
                    minDate: moment().format("YYYY-MM-DD"),
                    enableTime: true,
                    enableSeconds: true,
                    defaultHour: 23,
                    defaultMinute: 59,
                    defaultSeconds: 59,
                    onChange: function(selectedDates, dateStr, instance) {
                        startAtFlatpickr.set('maxDate', dateStr);
                    },
                });

                let conflict_campaign_names = '';
                // 驗證表單
                $("#create-form").validate({
                    debug: true,
                    submitHandler: function(form) {
                        self.form.save.isDisabled = true;
                        form.submit();
                    },
                    rules: {
                        campaign_name: {
                            required: true,
                            maxlength: 80,
                        },
                        campaign_type: {
                            required: true,
                        },
                        active: {
                            required: true,
                            remote: {
                                param: function() {
                                    return {
                                        url: "/backend/promotional_campaign_cart/ajax/can-pass-active-validation",
                                        type: "post",
                                        dataType: "json",
                                        cache: false,
                                        data: {
                                            campaign_type: $("#campaign_type").val(),
                                            start_at: $('#start_at').val(),
                                            end_at: $('#end_at').val(),
                                            n_value: $('#n_value').val(),
                                        },
                                        dataFilter: function(response, type) {
                                            conflict_campaign_names = '';
                                            let data = JSON.parse(response);

                                            if (data.status) {
                                                return true;
                                            }

                                            if (data.conflict_campaign_names) {
                                                conflict_campaign_names = data.conflict_campaign_names;
                                            }

                                            return false;
                                        },
                                    }
                                },
                                depends: function(element) {
                                    return $("#campaign_type").val() &&
                                        $('#start_at').val() &&
                                        $('#end_at').val() &&
                                        $('#n_value').val();
                                }
                            },
                        },
                        start_at: {
                            required: true,
                            dateGreaterThanNow: true,
                        },
                        end_at: {
                            required: true,
                            dateGreaterThanNow: true,
                            greaterThan: function() {
                                return self.form.startAt;
                            },
                        },
                        campaign_brief: {
                            required: true,
                            maxlength: 20,
                        },
                        url_code: {
                            required: true,
                            isAlphaNumericUnderscoreHyphen: true,
                        },
                        stock_type: {
                            required: true,
                        },
                        n_value: {
                            required: true,
                            digits: true,
                            min: function() {
                                if ($('#campaign_type').val() == 'CART02') {
                                    if ($("#x_value").val()) {
                                        return parseInt($("#x_value").val()) + 1;
                                    }
                                }

                                return 1;
                            },
                        },
                        x_value: {
                            required: true,
                            min: function() {
                                if ($('#campaign_type').val() == 'CART01') {
                                    return 0.01;
                                } else if ($('#campaign_type').val() == 'CART02') {
                                    return 1;
                                }

                                return 0;
                            },
                            max: {
                                param: 0.99,
                                depends: function(element) {
                                    return $('#campaign_type').val() == 'CART01';
                                },
                            },
                            maxlength: {
                                param: 4,
                                depends: function(element) {
                                    return $('#campaign_type').val() == 'CART01';
                                },
                            },
                            digits: {
                                depends: function(element) {
                                    return $('#campaign_type').val() == 'CART02';
                                },
                            },
                            number: {
                                depends: function(element) {
                                    return $('#campaign_type').val() == 'CART01';
                                },
                            },
                        },
                    },
                    messages: {
                        active: {
                            remote: function(element) {
                                if (['CART01', 'CART02'].includes($("#campaign_type").val())) {
                                    return `同一時間點、同一N值不可存在其他生效的﹝購物車滿N元，打X折﹞、﹝購物車滿N元，折X元﹞的行銷活動<br/>
                                    衝突的活動名稱: ${conflict_campaign_names}`;
                                } else if (['CART03'].includes($("#campaign_type").val())) {
                                    return `同一時間點、同一N值不可存在其他生效的﹝購物車滿N元，送贈品﹞的行銷活動<br/>
                                    衝突的活動名稱: ${conflict_campaign_names}`;
                                }
                            },
                        },
                        end_at: {
                            greaterThan: "結束時間必須大於開始時間",
                        },
                        n_value: {
                            digits: "只可輸入正整數",
                            min: function(value) {
                                if ($('#campaign_type').val() == 'CART02') {
                                    if (value > 1) {
                                        return "必須大於X值";
                                    }
                                }

                                return '只可輸入正整數';
                            },
                        },
                        x_value: {
                            digits: "只可輸入正整數",
                            min: function() {
                                if ($('#campaign_type').val() == 'CART02') {
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
                            error.insertAfter(element.closest(".row"));
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
                addThreshold() {
                    switch (this.form.campaignType) {
                        case "CART01":
                            this.form.thresholds.push({
                                nValue: "",
                                xValue: "",
                            });
                            break;

                        case "CART02":
                            this.form.thresholds.push({
                                nValue: "",
                                xValue: "",
                            });
                            break;

                        case "CART03":
                            this.form.thresholds.push({
                                nValue: "",
                                giveaways: [],
                            });
                            break;

                        case "CART04":
                            this.form.thresholds.push({
                                nValue: "",
                                giveaways: [],
                            });
                            break;
                    }
                },
                deleteThreshold(index) {
                    if (confirm('確定要刪除嗎？')) {
                        this.form.thresholds.splice(index, 1);
                    }
                },
                addGiveaway(threshold) {
                    // $('#giveaway-modal').modal('show');
                    threshold.giveaways.push({
                        productNo: "6666",
                    });
                },
                deleteGiveaway(threshold, index) {
                    if (confirm('確定要刪除嗎？')) {
                        threshold.giveaways.splice(index, 1);
                    }
                },
                addProduct() {
                    $('#product-modal').modal('show');
                },
                deleteProduct(index) {
                    if (confirm('確定要刪除嗎？')) {
                        this.form.products.splice(index, 1);
                    }
                },
                deleteBannerPhotoDesktop() {

                },
                deleteBannerPhotoMobile() {

                },
                submitForm() {
                    // $(`.contact-name`).each(function() {
                    //     $(this).rules("add", {
                    //         required: true,
                    //     });
                    // });

                    $("#create-form").submit();
                },
                thresholdBrief(threshold) {
                    let thresholdBrief = null;

                    switch (this.form.campaignType) {
                        case "CART01":
                            if (threshold.nValue && threshold.xValue) {
                                let discount = parseFloat(threshold.xValue) * 100;
                                thresholdBrief = `指定商品達$${threshold.nValue}，打${discount}折`;
                            }
                            break;

                        case "CART02":
                            if (threshold.nValue && threshold.xValue) {
                                thresholdBrief = `指定商品達$${threshold.nValue}，折$${threshold.xValue}`;
                            }
                            break;

                        case "CART03":
                            if (threshold.nValue && threshold.giveaways) {
                                console.log(threshold.giveaways);
                                // thresholdBrief = `指定商品達$${threshold.nValue}，折$${threshold.xValue}`;
                            }
                            break;

                        case "CART04":

                            break;
                    }

                    return thresholdBrief;
                },
                clickStockType(event) {
                    if (this.form.thresholds.giveaways) {
                        console.log(this.form.thresholds.giveaways)
                    }
                    console.log(event.target.value)
                    // console.log(this.form.stockType);
                    // if (this.form.stockType == "T") {
                    //     event.preventDefault();
                    // }
                    // this.form.stockType = "";
                    // event.preventDefault();
                },
            },
        });
        // $(function() {
        //     let campaign_types = json($campaign_types);
        //     renderCampaignType(campaign_types);

        //     let suppliers = json($suppliers);
        //     renderPrdModalSupplier(suppliers);
        //     renderGiftModalSupplier(suppliers);

        //     let product_types = json(config('uec.product_type_option'));
        //     renderPrdModalProductType(product_types);
        //     renderGiftModalProductType(product_types);

        //     $('#prd-modal-product-type').find('option[value="G"], option[value="A"]').remove(); // 移除贈品、加購品
        //     $('#prd-modal-product-type option[value="N"]').prop("selected", true); // 預設為一般品

        //     $('#gift-modal-product-type option[value="A"]').remove(); // 移除加購品
        //     $('#gift-modal-product-type option[value="G"]').prop("selected", true); // 預設為贈品

        //     init();

        //     var prd_modal_product_list = {}; // 單品modal清單中的商品
        //     var prd_product_list = {}; // 單品清單中的商品

        //     // 新增單品
        //     $('#btn-new-prd').on('click', function() {
        //         $('#prd-modal').modal('show');
        //     });

        //     // 單品modal商品全勾選
        //     $('#prd-modal-btn-check-all').on('click', function() {
        //         $('#prd-modal-product-table > tbody [name="choose_product"]').prop('checked', true);
        //     });

        //     // 單品modal商品全取消
        //     $('#prd-modal-btn-cancel-all').on('click', function() {
        //         $('#prd-modal-product-table > tbody [name="choose_product"]').prop('checked', false);
        //     });

        //     // 單品modal儲存、儲存並關閉
        //     $('#prd-modal-btn-save, #prd-modal-btn-save-and-close').on('click', function() {
        //         // 取得單品modal清單中有勾選的商品
        //         $('#prd-modal-product-table > tbody [name="choose_product"]:checked').closest('tr').each(
        //             function() {
        //                 let id = $(this).attr('data-id');

        //                 prd_product_list[id] = prd_modal_product_list[id]; // 增加單品清單中的商品
        //                 delete prd_modal_product_list[id]; // 移除單品modal清單中的商品
        //             });

        //         renderPrdProductList(prd_product_list);
        //         renderPrdModalProductList(prd_modal_product_list);
        //     });

        //     // 刪除單品清單中的商品
        //     $(document).on('click', '.btn-delete-prd', function() {
        //         if (confirm('確定要刪除嗎?')) {
        //             let id = $(this).closest('tr').attr('data-id');

        //             delete prd_product_list[id]; // 移除單品清單中的商品

        //             renderPrdProductList(prd_product_list);
        //         }
        //     });

        //     // 單品modal搜尋
        //     $('#prd-modal-btn-search').on('click', function() {
        //         let query_datas = {
        //             'supplier_id': $('#prd-modal-supplier-id').val(),
        //             'product_no': $('#prd-modal-product-no').val(),
        //             'product_name': $('#prd-modal-product-name').val(),
        //             'selling_price_min': $('#prd-modal-selling-price-min').val(),
        //             'selling_price_max': $('#prd-modal-selling-price-max').val(),
        //             'start_created_at': $('#prd-modal-start-created-at').val(),
        //             'end_created_at': $('#prd-modal-end-created-at').val(),
        //             'start_launched_at_start': $('#prd-modal-start-launched-at-start').val(),
        //             'start_launched_at_end': $('#prd-modal-start-launched-at-end').val(),
        //             'product_type': $('#prd-modal-product-type').val(),
        //             'limit': $('#prd-modal-limit').val(),
        //             'exist_products': Object.keys(prd_product_list),
        //         };

        //         getProducts(query_datas).then(products => {
        //             prd_modal_product_list = products;

        //             renderPrdModalProductList(prd_modal_product_list);
        //         });
        //     });

        //     var gift_modal_product_list = {}; // 贈品modal清單中的商品
        //     var gift_product_list = {}; // 贈品清單中的商品

        //     // 新增贈品
        //     $('#btn-new-gift').on('click', function() {
        //         $('#gift-modal').modal('show');
        //     });

        //     // 贈品modal商品全勾選
        //     $('#gift-modal-btn-check-all').on('click', function() {
        //         $('#gift-modal-product-table > tbody [name="choose_product"]').prop('checked', true);
        //     });

        //     // 贈品modal商品全取消
        //     $('#gift-modal-btn-cancel-all').on('click', function() {
        //         $('#gift-modal-product-table > tbody [name="choose_product"]').prop('checked', false);
        //     });

        //     // 贈品modal儲存、儲存並關閉
        //     $('#gift-modal-btn-save, #gift-modal-btn-save-and-close').on('click', function() {
        //         // 取得贈品modal清單中有勾選的商品
        //         $('#gift-modal-product-table > tbody [name="choose_product"]:checked').closest('tr').each(
        //             function() {
        //                 let id = $(this).attr('data-id');

        //                 gift_product_list[id] = gift_modal_product_list[id]; // 增加贈品清單中的商品
        //                 delete gift_modal_product_list[id]; // 移除贈品modal清單中的商品
        //             });

        //         renderGiftProductList(gift_product_list);
        //         renderGiftModalProductList(gift_modal_product_list);
        //     });

        //     // 刪除贈品清單中的商品
        //     $(document).on('click', '.btn-delete-gift', function() {
        //         if (confirm('確定要刪除嗎?')) {
        //             let id = $(this).closest('tr').attr('data-id');

        //             delete gift_product_list[id]; // 移除贈品清單中的商品

        //             renderGiftProductList(gift_product_list);
        //         }
        //     });

        //     // 贈品modal搜尋
        //     $('#gift-modal-btn-search').on('click', function() {
        //         let query_datas = {
        //             'supplier_id': $('#gift-modal-supplier-id').val(),
        //             'product_no': $('#gift-modal-product-no').val(),
        //             'product_name': $('#gift-modal-product-name').val(),
        //             'selling_price_min': $('#gift-modal-selling-price-min').val(),
        //             'selling_price_max': $('#gift-modal-selling-price-max').val(),
        //             'start_created_at': $('#gift-modal-start-created-at').val(),
        //             'end_created_at': $('#gift-modal-end-created-at').val(),
        //             'start_launched_at_start': $('#gift-modal-start-launched-at-start').val(),
        //             'start_launched_at_end': $('#gift-modal-start-launched-at-end').val(),
        //             'product_type': $('#gift-modal-product-type').val(),
        //             'limit': $('#gift-modal-limit').val(),
        //             'exist_products': Object.keys(gift_product_list),
        //         };

        //         getProducts(query_datas).then(products => {
        //             gift_modal_product_list = products;

        //             renderGiftModalProductList(gift_modal_product_list);
        //         });
        //     });
    </script>
@endsection
