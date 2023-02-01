@extends('backend.layouts.master')

@section('title', '購物車滿額活動 新增資料')

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
        @if ($errors->any())
            <div ref="errorMessage" style="display: none;">
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
                                action="{{ route('promotional_campaign_cart_v2.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-horizontal">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label class="control-label text-nowrap">活動名稱 <span
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
                                                    <label for="campaign_type" class="control-label text-nowrap">活動類型 <span
                                                            style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <select2
                                                        class="form-control"
                                                        :options="campaignTypes"
                                                        v-model="form.campaignType"
                                                        name="campaign_type"
                                                        @select2-change="onCampaignTypeChange"
                                                        @select2-selecting="onCampaignTypeSelecting"
                                                        :allow-clear="false"
                                                    >
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
                                                    <label class="control-label text-nowrap">狀態 <span
                                                            style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="active" value="1" v-model="form.active">生效
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="active" value="0" v-model="form.active">失效
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label class="control-label text-nowrap">上架時間 <span
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
                                                    <label class="control-label text-nowrap">前台文案 <span
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
                                                    <label class="control-label text-nowrap">前台URL <span
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
                                                    <label class="control-label text-nowrap">庫存類型 <span
                                                            style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="stock_type" value="A_B"
                                                            v-model="form.stockType" @click="clickStockType">買斷 / 寄售
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="stock_type" value="T"
                                                            v-model="form.stockType" @click="clickStockType">轉單
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-1">
                                                    <label for="campaign_type" class="control-label text-nowrap">供應商 <span
                                                            style="color: red;">*</span></label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <select2
                                                        class="form-control"
                                                        :options="suppliers"
                                                        v-model="form.supplierId"
                                                        name="supplier_id"
                                                        :allow-clear="false"
                                                        @select2-selecting="onSupplierSelecting"
                                                    >
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
                                                    <div class="row">
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
                                                                    <th class="text-nowrap">功能</th>
                                                                    <th class="text-nowrap"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(threshold, thresholdIndex) in form.thresholds"
                                                                    :key="thresholdIndex">
                                                                    <td>@{{ thresholdIndex + 1 }}</td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input type="number"
                                                                                class="form-control threshold-n-value"
                                                                                :name="`thresholds[${thresholdIndex}][n_value]`"
                                                                                min="0" v-model="threshold.nValue">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input type="number"
                                                                                class="form-control threshold-x-value"
                                                                                :name="`thresholds[${thresholdIndex}][x_value]`"
                                                                                min="0" v-model="threshold.xValue">
                                                                        </div>
                                                                    </td>
                                                                    <td>
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
                                                    <div class="row">
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
                                                                    <th class="text-nowrap">功能</th>
                                                                    <th class="text-nowrap"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(threshold, thresholdIndex) in form.thresholds"
                                                                    :key="thresholdIndex">
                                                                    <td>@{{ thresholdIndex + 1 }}</td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input type="number"
                                                                                class="form-control threshold-n-value"
                                                                                :name="`thresholds[${thresholdIndex}][n_value]`"
                                                                                min="0" v-model="threshold.nValue">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input type="number"
                                                                                class="form-control threshold-x-value"
                                                                                :name="`thresholds[${thresholdIndex}][x_value]`"
                                                                                min="0" v-model="threshold.xValue">
                                                                        </div>
                                                                    </td>
                                                                    <td>
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
                                                    <div class="row">
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
                                                                    <th class="text-nowrap">功能</th>
                                                                    <th class="text-nowrap"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(threshold, thresholdIndex) in form.thresholds"
                                                                    :key="thresholdIndex">
                                                                    <td>@{{ thresholdIndex + 1 }}</td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input type="number"
                                                                                class="form-control threshold-n-value"
                                                                                :name="`thresholds[${thresholdIndex}][n_value]`"
                                                                                min="0" v-model="threshold.nValue">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        @include('backend.promotional_campaign.cart_v2.giveaway_block')
                                                                    </td>
                                                                    <td>
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
                                                    <div class="row">
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
                                                                    <th class="text-nowrap">功能</th>
                                                                    <th class="text-nowrap"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(threshold, thresholdIndex) in form.thresholds"
                                                                    :key="thresholdIndex">
                                                                    <td>@{{ thresholdIndex + 1 }}</td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input type="number"
                                                                                class="form-control threshold-n-value"
                                                                                :name="`thresholds[${thresholdIndex}][n_value]`"
                                                                                min="0" v-model="threshold.nValue">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        @include('backend.promotional_campaign.cart_v2.giveaway_block')
                                                                    </td>
                                                                    <td>
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
                                                                    :name="`products[${index}][product_id]`"
                                                                    :value="product.productId">
                                                                <td>@{{ index + 1 }}</td>
                                                                <td>@{{ product.productNo }}</td>
                                                                <td>@{{ product.productName }}</td>
                                                                <td>@{{ product.sellingPrice }}</td>
                                                                <td>@{{ product.startLaunchedAt }}</td>
                                                                <td>@{{ product.launchStatus }}</td>
                                                                <td>@{{ product.grossMargin }}</td>
                                                                <td>@{{ product.webCategoryHierarchy }}</td>
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
                                                        <img v-show="bannerPhotoDesktop.url" :src="bannerPhotoDesktop.url"
                                                            width="100%" height="150">
                                                        <div v-show="bannerPhotoDesktop.showInputFile"
                                                            class="form-group">
                                                            <input type="file" name="banner_photo_desktop"
                                                                :data-image-width="bannerPhotoDesktop.width"
                                                                :data-image-height="bannerPhotoDesktop.height"
                                                                ref="bannerPhotoDesktop"
                                                                accept="image/png, image/jpeg"
                                                                @change="onDesktopFileChange">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button v-show="bannerPhotoDesktop.showDeleteButton" type="button"
                                                            class="btn btn-danger" @click="deleteDesktopFile">
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
                                                        <div v-show="bannerPhotoMobile.showInputFile"
                                                            class="form-group">
                                                            <input type="file" name="banner_photo_mobile"
                                                                :data-image-width="bannerPhotoMobile.width"
                                                                :data-image-height="bannerPhotoMobile.height"
                                                                ref="bannerPhotoMobile"
                                                                accept="image/png, image/jpeg"
                                                                @change="onMobileFileChange">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button v-show="bannerPhotoMobile.showDeleteButton" type="button"
                                                            class="btn btn-danger" @click="deleteMobileFile">
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
                                                    :disabled='saveButton.isDisabled'>
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

        <cart-v2-campaign-product-modal :modal="productModal" @save="saveProductModalProducts">
        </cart-v2-campaign-product-modal>
        <cart-v2-campaign-product-modal :modal="giveawayModal" @save="saveGiveawayModalProducts">
        </cart-v2-campaign-product-modal>
    </div>
@endsection

@section('js')
    <script src="{{ mix('js/promotional_campaign/cart_v2/main.js') }}"></script>
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
                },
                bannerPhotoMobile: {
                    url: "",
                    width: "",
                    height: "",
                    showInputFile: true,
                    showDeleteButton: false,
                },
                isNowGreaterThanOrEqualToStartAt: false,
            },
            created() {
                let campaignTypes = @json($campaignTypes);
                let suppliers = @json($suppliers);

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
            },
            mounted() {
                let self = this;

                if (this.$refs.errorMessage) {
                    alert(this.$refs.errorMessage.innerText.trim());
                }

                let startAtLastSelectedDate;
                let startAtFlatpickr = flatpickr("#start_at_flatpickr", {
                    dateFormat: "Y-m-d H:i:S",
                    minDate: moment().format("YYYY-MM-DD"),
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

                let conflictContents = '';
                // 驗證表單
                $("#create-form").validate({
                    // debug: true,
                    ignore: "",
                    submitHandler: function(form) {
                        self.saveButton.isDisabled = true;
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
                                    let productIds = self.form.products.map((product) => {
                                        return product.productId;
                                    });

                                    return {
                                        url: "/backend/promotional-campaign-cart-v2/can-active",
                                        type: "post",
                                        dataType: "json",
                                        cache: false,
                                        data: {
                                            campaign_type: self.form.campaignType,
                                            start_at: self.form.startAt,
                                            end_at: self.form.endAt,
                                            product_ids: productIds,
                                        },
                                        dataFilter: function(response) {
                                            conflictContents = "";
                                            if (response) {
                                                let data = JSON.parse(response);

                                                if (data.result) {
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
                                },
                            },
                            atLeastOneThreshold: {
                                param: function() {
                                    return self.form.thresholds;
                                },
                                depends: function(element) {
                                    return self.form.active == 1;
                                },
                            },
                            atLeastOneProduct: {
                                param: function() {
                                    return self.form.products;
                                },
                                depends: function(element) {
                                    return self.form.active == 1;
                                },
                            },
                            eachThresholdAtLeastOneGiveaway: {
                                param: function() {
                                    return self.form.thresholds;
                                },
                                depends: function(element) {
                                    return self.form.active == 1 && ['CART_P03', 'CART_P04'].includes(
                                        self.form.campaignType);
                                },
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
                        supplier_id: {
                            required: true,
                        },
                        banner_photo_desktop: {
                            accept: "image/*",
                            filesize: [1, 'MB'],
                            minImageWidth: 1200,
                            minImageHeight: 150,
                        },
                        banner_photo_mobile: {
                            accept: "image/*",
                            filesize: [1, 'MB'],
                            minImageWidth: 345,
                            minImageHeight: 180,
                        },
                    },
                    messages: {
                        active: {
                            remote: function(element) {
                                if (['CART_P01', 'CART_P02'].includes(self.form.campaignType)) {
                                    return `同一個商品，同一時間點不可同時出現於多個「指定商品滿N元，打X折」、「指定商品滿N元，折X元」類型的生效活動<br/>
                                    ${conflictContents}`;
                                } else if (['CART_P03', 'CART_P04'].includes(self.form.campaignType)) {
                                    return `同一個商品，同一時間點不可同時出現於多個「指定商品滿N件，送贈品」、「指定商品滿N元，送贈品」類型的生效活動<br/>
                                    ${conflictContents}`;
                                }
                            },
                        },
                        end_at: {
                            greaterThan: "結束時間必須大於開始時間",
                        },
                        banner_photo_desktop: {
                            accept: "檔案類型錯誤",
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
                    invalidHandler: function(event, validator) {
                        if (validator.errorList.length) {
                            let errorElement = $(validator.errorList[0].element);
                            let classNames = [];

                            if (errorElement.attr('class')) {
                                classNames = errorElement.attr('class').split(' ');
                            }

                            if (['threshold-n-value', 'threshold-x-value', 'giveaway-assigned-qty']
                                .some((item, index, array) => classNames.includes(item)) ||
                                ['banner_photo_desktop', 'banner_photo_mobile'].includes(errorElement
                                    .attr('name'))) {
                                let tabId = errorElement.closest(".tab-pane").attr('id');
                                $(`ul.nav-tabs a[href="#${tabId}"]`).tab('show');
                            }
                        }
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
                // 儲存
                submitForm() {
                    let self = this;

                    $(`.threshold-n-value`).each(function() {
                        let nValueElement = $(this);

                        $(this).rules("add", {
                            required: true,
                            digits: true,
                            min: function() {
                                let xValue = nValueElement.closest("tr").find(
                                    ".threshold-x-value").val();

                                if (self.form.campaignType == 'CART_P02') {
                                    if (xValue) {
                                        return parseInt(xValue) + 1;
                                    }
                                }

                                return 1;
                            },
                            unique: ".threshold-n-value",
                            messages: {
                                digits: "只可輸入正整數",
                                min: function(value) {
                                    if (self.form.campaignType == 'CART_P02') {
                                        if (value > 1) {
                                            return "必須大於X值";
                                        }
                                    }

                                    return '只可輸入正整數';
                                },
                            },
                        });
                    });

                    $(`.threshold-x-value`).each(function() {
                        $(this).rules("add", {
                            required: true,
                            min: function() {
                                if (self.form.campaignType == 'CART_P01') {
                                    return 0.01;
                                } else if (self.form.campaignType == 'CART_P02') {
                                    return 1;
                                }

                                return 0;
                            },
                            max: {
                                param: 0.99,
                                depends: function(element) {
                                    return self.form.campaignType == 'CART_P01';
                                },
                            },
                            maxlength: {
                                param: 4,
                                depends: function(element) {
                                    return self.form.campaignType == 'CART_P01';
                                },
                            },
                            digits: {
                                depends: function(element) {
                                    return self.form.campaignType == 'CART_P02';
                                },
                            },
                            number: {
                                depends: function(element) {
                                    return self.form.campaignType == 'CART_P01';
                                },
                            },
                            messages: {
                                digits: "只可輸入正整數",
                                min: function() {
                                    if (self.form.campaignType == 'CART_P02') {
                                        return '只可輸入正整數';
                                    }

                                    return '請輸入不小於 0 的數值';
                                },
                            },
                        });
                    });

                    $(`.giveaway-assigned-qty`).each(function() {
                        $(this).rules("add", {
                            required: true,
                            digits: true,
                            min: 1,
                        });
                    });

                    $("#create-form").submit();
                },
                // 設定門檻簡述
                thresholdBrief(threshold) {
                    let thresholdBrief = null;
                    let xValue = threshold.xValue;
                    let nValue = this.numberComma(threshold.nValue);
                    switch (this.form.campaignType) {
                        case "CART_P01":
                            if (threshold.nValue && threshold.xValue) {
                                let discount = this.roundDown(Number(threshold.xValue), 2);
                                discount = discount.toString().substring(2);
                                thresholdBrief = `滿${nValue}元打${discount}折`;
                            }
                            break;

                        case "CART_P02":
                            if (threshold.nValue && threshold.xValue) {
                                thresholdBrief = `滿${nValue}元折${threshold.xValue}元`;
                            }
                            break;

                        case "CART_P03":
                            if (threshold.nValue) {
                                thresholdBrief = `滿${nValue}元贈`;
                            }
                            break;

                        case "CART_P04":
                            if (threshold.nValue) {
                                thresholdBrief = `滿${threshold.nValue}件贈`;
                            }
                            break;
                    }

                    return thresholdBrief;
                },
                numberComma(num) {
                    let comma = /\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g
                    return num.toString().replace(comma, ',')
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
                onSupplierSelecting(event) {
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
                // 當選擇活動類型時
                onCampaignTypeSelecting(event) {
                    let errorMessage = "";
                    // 頁籤「門檻」已有指定贈品
                    if (this.form.thresholds.some(threshold => threshold.giveaways.length)) {
                        errorMessage += "請先刪除「門檻」頁籤的贈品設定，才能切換「活動類型」！\n"
                    }

                    // 頁籤「指定商品」已有指定商品
                    if (this.form.products.length) {
                        errorMessage += "請先刪除「指定商品」頁籤的商品設定，才能切換「活動類型」！\n"
                    }

                    if (errorMessage) {
                        event.preventDefault();
                        alert(errorMessage);
                    }
                },
                // 選擇活動類型
                onCampaignTypeChange() {
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
                            launchStatus: product.launchStatus,
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
