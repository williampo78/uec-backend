@extends('backend.master')

@section('title', '購物車滿額活動 檢視資料')

@section('css')
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
    <div id="app" v-cloak>
        <div id="page-wrapper">
            <!-- 表頭名稱 -->
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header"><i class="fa-solid fa-magnifying-glass"></i> 購物車滿額活動 檢視資料</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">請輸入下列欄位資料</div>
                        <div class="panel-body">
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
                                                    v-model="form.campaignType" name="campaign_type"
                                                    @select2-change="changeCampaignType" disabled>
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
                                                <label class="control-label">狀態 <span style="color: red;">*</span></label>
                                            </div>
                                            <div class="col-sm-11">
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
                                                                v-model="form.startAt"
                                                                :disabled="isNowGreaterThanOrEqualToStartAt">
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
                                                    v-model="form.campaignBrief"
                                                    :disabled="isNowGreaterThanOrEqualToStartAt">
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
                                                    v-model="form.urlCode" :disabled="isNowGreaterThanOrEqualToStartAt">
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
                                                <label class="radio-inline">
                                                    <input type="radio" name="stock_type" value="A_B"
                                                        v-model="form.stockType" @click="clickStockType"
                                                        :disabled="isNowGreaterThanOrEqualToStartAt">買斷 / 寄售
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="stock_type" value="T"
                                                        v-model="form.stockType" @click="clickStockType"
                                                        :disabled="isNowGreaterThanOrEqualToStartAt">轉單
                                                </label>
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
                                                    v-model="form.supplierId" name="supplier_id" :allow-clear="false"
                                                    @select2-selecting="selectingSupplier"
                                                    :disabled="isNowGreaterThanOrEqualToStartAt">
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
                                            <div v-if="form.campaignType === 'CART_P01'">
                                                <div class="row" v-if="!isNowGreaterThanOrEqualToStartAt">
                                                    <div class="col-sm-12">
                                                        <button type="button" class="btn btn-warning"
                                                            @click="addThreshold">
                                                            <i class="fa-solid fa-plus"></i> 新增門檻
                                                        </button>
                                                        <span class="text-primary" style="margin-left: 2rem;">※ 打「85折」時，折數輸入「0.85」</span>
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
                                                                <th class="text-nowrap"
                                                                    v-if="!isNowGreaterThanOrEqualToStartAt">功能</th>
                                                                <th class="text-nowrap"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(threshold, thresholdIndex) in form.thresholds"
                                                                :key="thresholdIndex">
                                                                <input type="hidden"
                                                                    :name="`thresholds[${thresholdIndex}][id]`"
                                                                    :value="threshold.id">
                                                                <td>@{{ thresholdIndex + 1 }}</td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="number"
                                                                            class="form-control threshold-n-value"
                                                                            :name="`thresholds[${thresholdIndex}][n_value]`"
                                                                            min="0" v-model="threshold.nValue"
                                                                            :disabled="isNowGreaterThanOrEqualToStartAt">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="number"
                                                                            class="form-control threshold-x-value"
                                                                            :name="`thresholds[${thresholdIndex}][x_value]`"
                                                                            min="0" v-model="threshold.xValue"
                                                                            :disabled="isNowGreaterThanOrEqualToStartAt">
                                                                    </div>
                                                                </td>
                                                                <td v-if="!isNowGreaterThanOrEqualToStartAt">
                                                                    <button type="button" class="btn btn-danger"
                                                                        @click="deleteThreshold(thresholdIndex)">
                                                                        <i class="fa-solid fa-trash-can"></i> 刪除
                                                                    </button>
                                                                </td>
                                                                <td>
                                                                    @{{ thresholdBrief(threshold) }}
                                                                    <input type="hidden"
                                                                        :name="`thresholds[${thresholdIndex}][threshold_brief]`"
                                                                        :value="thresholdBrief(threshold)">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div v-else-if="form.campaignType === 'CART_P02'">
                                                <div class="row" v-if="!isNowGreaterThanOrEqualToStartAt">
                                                    <div class="col-sm-12">
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
                                                                <th class="text-nowrap"
                                                                    v-if="!isNowGreaterThanOrEqualToStartAt">功能</th>
                                                                <th class="text-nowrap"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(threshold, thresholdIndex) in form.thresholds"
                                                                :key="thresholdIndex">
                                                                <input type="hidden"
                                                                    :name="`thresholds[${thresholdIndex}][id]`"
                                                                    :value="threshold.id">
                                                                <td>@{{ thresholdIndex + 1 }}</td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="number"
                                                                            class="form-control threshold-n-value"
                                                                            :name="`thresholds[${thresholdIndex}][n_value]`"
                                                                            min="0" v-model="threshold.nValue"
                                                                            :disabled="isNowGreaterThanOrEqualToStartAt">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="number"
                                                                            class="form-control threshold-x-value"
                                                                            :name="`thresholds[${thresholdIndex}][x_value]`"
                                                                            min="0" v-model="threshold.xValue"
                                                                            :disabled="isNowGreaterThanOrEqualToStartAt">
                                                                    </div>
                                                                </td>
                                                                <td v-if="!isNowGreaterThanOrEqualToStartAt">
                                                                    <button type="button" class="btn btn-danger"
                                                                        @click="deleteThreshold(thresholdIndex)">
                                                                        <i class="fa-solid fa-trash-can"></i> 刪除
                                                                    </button>
                                                                </td>
                                                                <td>
                                                                    @{{ thresholdBrief(threshold) }}
                                                                    <input type="hidden"
                                                                        :name="`thresholds[${thresholdIndex}][threshold_brief]`"
                                                                        :value="thresholdBrief(threshold)">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div v-else-if="form.campaignType === 'CART_P03'">
                                                <div class="row" v-if="!isNowGreaterThanOrEqualToStartAt">
                                                    <div class="col-sm-12">
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
                                                                <th class="text-nowrap"
                                                                    v-if="!isNowGreaterThanOrEqualToStartAt">功能</th>
                                                                <th class="text-nowrap"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(threshold, thresholdIndex) in form.thresholds"
                                                                :key="thresholdIndex">
                                                                <input type="hidden"
                                                                    :name="`thresholds[${thresholdIndex}][id]`"
                                                                    :value="threshold.id">
                                                                <td>@{{ thresholdIndex + 1 }}</td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="number"
                                                                            class="form-control threshold-n-value"
                                                                            :name="`thresholds[${thresholdIndex}][n_value]`"
                                                                            min="0" v-model="threshold.nValue"
                                                                            :disabled="isNowGreaterThanOrEqualToStartAt">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @include(
                                                                        'backend.promotional_campaign.cart_v2.giveaway_block'
                                                                    )
                                                                </td>
                                                                <td v-if="!isNowGreaterThanOrEqualToStartAt">
                                                                    <button type="button" class="btn btn-danger"
                                                                        @click="deleteThreshold(thresholdIndex)">
                                                                        <i class="fa-solid fa-trash-can"></i> 刪除
                                                                    </button>
                                                                </td>
                                                                <td>
                                                                    @{{ thresholdBrief(threshold) }}
                                                                    <input type="hidden"
                                                                        :name="`thresholds[${thresholdIndex}][threshold_brief]`"
                                                                        :value="thresholdBrief(threshold)">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div v-else-if="form.campaignType === 'CART_P04'">
                                                <div class="row" v-if="!isNowGreaterThanOrEqualToStartAt">
                                                    <div class="col-sm-12">
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
                                                                <th class="text-nowrap"
                                                                    v-if="!isNowGreaterThanOrEqualToStartAt">功能</th>
                                                                <th class="text-nowrap"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(threshold, thresholdIndex) in form.thresholds"
                                                                :key="thresholdIndex">
                                                                <input type="hidden"
                                                                    :name="`thresholds[${thresholdIndex}][id]`"
                                                                    :value="threshold.id">
                                                                <td>@{{ thresholdIndex + 1 }}</td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="number"
                                                                            class="form-control threshold-n-value"
                                                                            :name="`thresholds[${thresholdIndex}][n_value]`"
                                                                            min="0" v-model="threshold.nValue"
                                                                            :disabled="isNowGreaterThanOrEqualToStartAt">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @include(
                                                                        'backend.promotional_campaign.cart_v2.giveaway_block'
                                                                    )
                                                                </td>
                                                                <td v-if="!isNowGreaterThanOrEqualToStartAt">
                                                                    <button type="button" class="btn btn-danger"
                                                                        @click="deleteThreshold(thresholdIndex)">
                                                                        <i class="fa-solid fa-trash-can"></i> 刪除
                                                                    </button>
                                                                </td>
                                                                <td>
                                                                    @{{ thresholdBrief(threshold) }}
                                                                    <input type="hidden"
                                                                        :name="`thresholds[${thresholdIndex}][threshold_brief]`"
                                                                        :value="thresholdBrief(threshold)">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab-product">
                                            <div class="row" v-if="!isNowGreaterThanOrEqualToStartAt">
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
                                                            <td>@{{ product.sellingPrice }}</td>
                                                            <td>@{{ product.startLaunchedAt }}</td>
                                                            <td>@{{ product.launchStatus }}</td>
                                                            <td>@{{ product.grossMargin }}</td>
                                                            <td>@{{ product.webCategoryHierarchy }}</td>
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
                                        </div>
                                        <div class="tab-pane fade" id="tab-banner">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <label>Desktop版</label>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p>(1) size不可超過1MB、(2) 副檔名須為JPG、JPEG、PNG、(3) 寬*高至少須為1200*150</p>
                                                    <img v-show="bannerPhotoDesktop.url" :src="bannerPhotoDesktop.url"
                                                        width="100%" height="150">
                                                    <div v-show="bannerPhotoDesktop.showInputFile && !isNowGreaterThanOrEqualToStartAt"
                                                        class="form-group">
                                                        <input type="file" name="banner_photo_desktop"
                                                            :data-image-width="bannerPhotoDesktop.width"
                                                            :data-image-height="bannerPhotoDesktop.height"
                                                            ref="bannerPhotoDesktop" @change="onDesktopFileChange">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input type="hidden" name="is_delete_banner_photo_desktop"
                                                        :value="bannerPhotoDesktop.isDeleteFile">
                                                    <button
                                                        v-show="bannerPhotoDesktop.showDeleteButton && !isNowGreaterThanOrEqualToStartAt"
                                                        type="button" class="btn btn-danger" @click="deleteDesktopFile">
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
                                                    <img v-show="bannerPhotoMobile.url" :src="bannerPhotoMobile.url"
                                                        width="100%" height="180">
                                                    <div v-show="bannerPhotoMobile.showInputFile && !isNowGreaterThanOrEqualToStartAt"
                                                        class="form-group">
                                                        <input type="file" name="banner_photo_mobile"
                                                            :data-image-width="bannerPhotoMobile.width"
                                                            :data-image-height="bannerPhotoMobile.height"
                                                            ref="bannerPhotoMobile" @change="onMobileFileChange">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input type="hidden" name="is_delete_banner_photo_mobile"
                                                        :value="bannerPhotoMobile.isDeleteFile">
                                                    <button
                                                        v-show="bannerPhotoMobile.showDeleteButton && !isNowGreaterThanOrEqualToStartAt"
                                                        type="button" class="btn btn-danger" @click="deleteMobileFile">
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
                                        <a href="{{ route('promotional_campaign_cart_v2') }}" class="btn btn-success">
                                            <i class="fa-solid fa-reply"></i> 返回
                                        </a>
                                    </div>
                                </div>
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
                form: {
                    campaignId: "",
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
                    title: "新增指定商品",
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
                currentThreshold: {},
                bannerPhotoDesktop: {
                    url: "",
                    width: "",
                    height: "",
                    showInputFile: true,
                    showDeleteButton: false,
                    isDeleteFile: false,
                },
                bannerPhotoMobile: {
                    url: "",
                    width: "",
                    height: "",
                    showInputFile: true,
                    showDeleteButton: false,
                    isDeleteFile: false,
                },
                isNowGreaterThanOrEqualToStartAt: true,
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
                    this.form.campaignType = campaign.campaign_type;
                    this.form.active = campaign.active;
                    this.form.startAt = campaign.start_at;
                    this.form.endAt = campaign.end_at;
                    this.form.campaignBrief = campaign.campaign_brief;
                    this.form.urlCode = campaign.url_code;
                    this.form.stockType = campaign.stock_type;
                    this.form.supplierId = campaign.supplier_id;

                    if (campaign.banner_photo_desktop_url) {
                        this.bannerPhotoDesktop.url = campaign.banner_photo_desktop_url;
                        this.bannerPhotoDesktop.showInputFile = false;
                        this.bannerPhotoDesktop.showDeleteButton = true;
                    }

                    if (campaign.banner_photo_mobile_url) {
                        this.bannerPhotoMobile.url = campaign.banner_photo_mobile_url;
                        this.bannerPhotoMobile.showInputFile = false;
                        this.bannerPhotoMobile.showDeleteButton = true;
                    }

                    if (campaign.thresholds) {
                        campaign.thresholds.forEach(threshold => {
                            let giveaways = [];

                            if (threshold.giveaways) {
                                threshold.giveaways.forEach(giveaway => {
                                    giveaways.push({
                                        id: giveaway.id,
                                        productId: giveaway.product_id,
                                        productNo: giveaway.product_no,
                                        productName: giveaway.product_name,
                                        assignedQty: giveaway.assigned_qty,
                                        stockType: giveaway.stock_type,
                                        productType: giveaway.product_type,
                                        supplier: giveaway.supplier,
                                        stockQty: giveaway.stock_qty,
                                    });
                                });
                            }

                            this.form.thresholds.push({
                                id: threshold.id,
                                nValue: threshold.n_value,
                                xValue: threshold.x_value,
                                giveaways: giveaways,
                            });
                        });
                    }

                    if (campaign.products) {
                        campaign.products.forEach(product => {
                            this.form.products.push({
                                id: product.id,
                                productId: product.product_id,
                                productNo: product.product_no,
                                productName: product.product_name,
                                sellingPrice: product.selling_price,
                                startLaunchedAt: product.start_launched_at,
                                launchStatus: product.launch_status,
                                grossMargin: product.gross_margin,
                                webCategoryHierarchy: product.web_category_hierarchy,
                            });
                        });
                    }
                }
            },
            mounted() {
                const tags = ["input", "textarea", "select"];
                tags.forEach(tagName => {
                    const nodes = document.getElementsByTagName(tagName);
                    for (let i = 0; i < nodes.length; i++) {
                        nodes[i].disabled = true;
                    }
                });
            },
            methods: {
                // 新增門檻
                addThreshold() {
                    this.form.thresholds.push({
                        nValue: "",
                        xValue: "",
                        giveaways: [],
                    });
                },
                // 刪除門檻
                deleteThreshold(index) {
                    if (confirm('確定要刪除嗎？')) {
                        this.form.thresholds.splice(index, 1);
                    }
                },
                // 新增贈品
                addGiveaway(threshold) {
                    if (!this.form.stockType || !this.form.supplierId) {
                        alert("尚未指定「庫存類型」、「供應商」，不允許新增贈品！");
                        return;
                    }

                    this.currentThreshold = threshold;
                    this.giveawayModal.excludeProductIds = [];
                    threshold.giveaways.forEach(giveaway => {
                        this.giveawayModal.excludeProductIds.push(giveaway.productId);
                    });

                    $('#giveaway-modal').modal('show');
                },
                // 刪除贈品
                deleteGiveaway(threshold, index) {
                    if (confirm('確定要刪除嗎？')) {
                        threshold.giveaways.splice(index, 1);
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
                // 設定門檻簡述
                thresholdBrief(threshold) {
                    let thresholdBrief = null;

                    switch (this.form.campaignType) {
                        case "CART_P01":
                            if (threshold.nValue && threshold.xValue) {
                                let discount = this.roundDown(Number(threshold.xValue), 2);
                                discount = discount.toString().substring(2);
                                thresholdBrief = `指定商品達$${threshold.nValue}，打${discount}折`;
                            }
                            break;

                        case "CART_P02":
                            if (threshold.nValue && threshold.xValue) {
                                thresholdBrief = `指定商品達$${threshold.nValue}，折$${threshold.xValue}`;
                            }
                            break;

                        case "CART_P03":
                            if (threshold.nValue) {
                                thresholdBrief = `指定商品達$${threshold.nValue}送贈`;
                            }
                            break;

                        case "CART_P04":
                            if (threshold.nValue) {
                                thresholdBrief = `指定商品達${threshold.nValue}件送贈`;
                            }
                            break;
                    }

                    return thresholdBrief;
                },
                // 點擊庫存類型
                clickStockType(event) {
                    if (this.form.stockType == event.target.value) {
                        return;
                    }

                    let errorMessage = "";
                    // 頁籤「門檻」已有指定贈品
                    if (this.form.thresholds.some(threshold => threshold.giveaways.length)) {
                        errorMessage += "請先刪除「門檻」頁籤的贈品設定，才能切換「庫存類型」！\n"
                    }

                    // 頁籤「指定商品」已有指定商品
                    if (this.form.products.length) {
                        errorMessage += "請先刪除「指定商品」頁籤的商品設定，才能切換「庫存類型」！\n"
                    }

                    if (errorMessage) {
                        event.preventDefault();
                        alert(errorMessage);
                    }
                },
                // 當選擇供應商時
                selectingSupplier(event) {
                    if (event.params.args.data.id == "all") {
                        return;
                    }

                    let errorMessage = "";
                    // 頁籤「門檻」已有指定贈品
                    if (this.form.thresholds.some(threshold => threshold.giveaways.length)) {
                        errorMessage += "請先刪除「門檻」頁籤的贈品設定，才能切換「供應商」！\n"
                    }

                    // 頁籤「指定商品」已有指定商品
                    if (this.form.products.length) {
                        errorMessage += "請先刪除「指定商品」頁籤的商品設定，才能切換「供應商」！\n"
                    }

                    if (errorMessage) {
                        event.preventDefault();
                        alert(errorMessage);
                    }
                },
                // 選擇活動類型
                changeCampaignType() {
                    this.form.thresholds = [];
                },
                // 儲存商品modal的商品
                saveProductModalProducts(products) {
                    products.forEach(product => {
                        this.form.products.push({
                            productId: product.id,
                            productNo: product.productNo,
                            productName: product.productName,
                            sellingPrice: product.sellingPrice,
                            startLaunchedAt: product.startLaunchedAt,
                            launchStatus: product.launchStatus,
                            grossMargin: product.grossMargin,
                            webCategoryHierarchy: product.webCategoryHierarchy,
                        });
                    });
                },
                // 儲存贈品modal的商品
                saveGiveawayModalProducts(products) {
                    products.forEach(product => {
                        this.currentThreshold.giveaways.push({
                            productId: product.id,
                            productNo: product.productNo,
                            productName: product.productName,
                            assignedQty: 1,
                            stockType: product.stockType,
                            productType: product.productType,
                            supplier: product.supplier,
                            stockQty: product.stockQty,
                        });
                    });
                },
                // 上傳Desktop圖片
                onDesktopFileChange(event) {
                    this.bannerPhotoDesktop.url = "";
                    this.bannerPhotoDesktop.width = "";
                    this.bannerPhotoDesktop.height = "";

                    const file = event.target.files[0];

                    if (!file || file.type.indexOf('image/') !== 0) {
                        this.bannerPhotoDesktop.showDeleteButton = false;
                        return;
                    }

                    let reader = new FileReader();

                    reader.readAsDataURL(file);
                    reader.onload = (event) => {
                        let img = new Image();
                        img.onload = () => {
                            this.bannerPhotoDesktop.width = img.width;
                            this.bannerPhotoDesktop.height = img.height;
                        }
                        img.src = event.target.result;
                        this.bannerPhotoDesktop.url = event.target.result;
                        this.bannerPhotoDesktop.showDeleteButton = true;
                    }

                    reader.onerror = (event) => {
                        console.error(event);
                    }
                },
                // 刪除Desktop圖片
                deleteDesktopFile() {
                    this.bannerPhotoDesktop.showDeleteButton = false;
                    this.bannerPhotoDesktop.url = "";
                    this.bannerPhotoDesktop.showInputFile = true;
                    this.bannerPhotoDesktop.isDeleteFile = true;
                    this.$refs.bannerPhotoDesktop.value = "";
                },
                // 上傳Mobile圖片
                onMobileFileChange(event) {
                    this.bannerPhotoMobile.url = "";
                    this.bannerPhotoMobile.width = "";
                    this.bannerPhotoMobile.height = "";

                    const file = event.target.files[0];

                    if (!file || file.type.indexOf('image/') !== 0) {
                        this.bannerPhotoMobile.showDeleteButton = false;
                        return;
                    }

                    let reader = new FileReader();

                    reader.readAsDataURL(file);
                    reader.onload = (event) => {
                        let img = new Image();
                        img.onload = () => {
                            this.bannerPhotoMobile.width = img.width;
                            this.bannerPhotoMobile.height = img.height;
                        }
                        img.src = event.target.result;
                        this.bannerPhotoMobile.url = event.target.result;
                        this.bannerPhotoMobile.showDeleteButton = true;
                    }

                    reader.onerror = (event) => {
                        console.error(event);
                    }
                },
                // 刪除Mobile圖片
                deleteMobileFile() {
                    this.bannerPhotoMobile.showDeleteButton = false;
                    this.bannerPhotoMobile.url = "";
                    this.bannerPhotoMobile.showInputFile = true;
                    this.bannerPhotoMobile.isDeleteFile = true;
                    this.$refs.bannerPhotoMobile.value = "";
                },
                // 無條件捨去
                roundDown(num, decimal) {
                    return Math.floor((num + Number.EPSILON) * Math.pow(10, decimal)) / Math.pow(10, decimal);
                },
            },
        });
    </script>
@endsection
