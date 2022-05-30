@extends('backend.master')

@section('title', '單品活動 新增資料')

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
                            <form id="create-form" method="post" action="{{ route('promotional_campaign_prd.store') }}">
                                @csrf
                                <div class="row">
                                    <!-- 欄位 -->
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
                                                            <select2 class="form-control" :options="campaignTypes"
                                                                v-model="form.campaignType" name="campaign_type"
                                                                @select2-change="changeCampaignType">
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
                                                                        name="n_value" v-model="form.nValue" min="1">
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
                                                                        name="x_value" v-model="form.xValue">
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
                                                                    autocomplete="off" data-input v-model="form.startAt">
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
                                                                v-model="form.targetGroups">所有會員
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
                                                <div class="col-sm-2">
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
                                                            <th class="text-nowrap">功能</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
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
                                                <div class="col-sm-2">
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
                                                            <th class="text-nowrap">功能</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <hr style="border-top: 1px solid gray;" />
                                        </div>


                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    @if ($share_role_auth['auth_create'])
                                                        <button class="btn btn-success" type="button" @click="submitForm"
                                                            :disabled="saveButton.isDisabled">
                                                            <i class="fa-solid fa-floppy-disk"></i> 儲存
                                                        </button>
                                                    @endif
                                                    <a href="{{ route('promotional_campaign_prd') }}"
                                                        class="btn btn-danger" type="button">
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
        // $(function() {
        //     let suppliers = json($suppliers);
        //     let product_types = json(config('uec.product_type_options'));
        //     var prd_modal_product_list = {}; // 單品modal清單中的商品
        //     var prd_product_list = {}; // 單品清單中的商品
        //     var gift_modal_product_list = {}; // 贈品modal清單中的商品
        //     var gift_product_list = {}; // 贈品清單中的商品

        //     renderCampaignType(campaign_types);
        //     renderPrdModalSupplier(suppliers);
        //     renderGiftModalSupplier(suppliers);
        //     renderPrdModalProductType(product_types);
        //     renderGiftModalProductType(product_types);

        //     $('#prd-modal-product-type').find('option[value="G"], option[value="A"]').remove(); // 移除贈品、加購品
        //     $('#prd-modal-product-type option[value="N"]').prop("selected", true); // 預設為一般品

        //     $('#gift-modal-product-type option[value="A"]').remove(); // 移除加購品
        //     $('#gift-modal-product-type option[value="G"]').prop("selected", true); // 預設為贈品

        //     init();

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
        // });

        let vm = new Vue({
            el: "#app",
            data: {
                form: {
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
                },
                saveButton: {
                    isDisabled: false,
                },
                campaignTypes: [],
                productModal: {
                    id: "product-modal",
                    title: "新增單品",
                    productType: {
                        id: "N",
                        includeOptions: ["N"],
                    },
                    excludeProductIds: [],
                },
                giveawayModal: {
                    id: "giveaway-modal",
                    title: "新增贈品",
                    productType: {
                        id: "G",
                        includeOptions: ["N", "G"],
                    },
                    excludeProductIds: [],
                },
                isNowGreaterThanOrEqualToStartAt: false,
                showXValue: true,
                showXValueHint: true,
                showGiveaway: false,
            },
            created() {
                let campaignTypes = @json($campaignTypes);

                if (campaignTypes) {
                    campaignTypes.forEach(campaignType => {
                        this.campaignTypes.push({
                            text: campaignType.description,
                            id: campaignType.code,
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
                    minDate: this.form.endAt,
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

                // 驗證表單
                $("#create-form").validate({
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
                                        url: "/backend/promotional-campaign-prd/ajax/can-pass-active-validation",
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
                                            if (response) {
                                                let data = JSON.parse(response);

                                                if (data.result) {
                                                    return true;
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
                        },
                        target_groups: {
                            required: true,
                        },
                        campaign_brief: {
                            required: true,
                            maxlength: 20,
                        },
                    },
                    messages: {
                        end_at: {
                            greaterThan: "結束時間必須大於開始時間",
                        },
                        active: {
                            remote: function(element) {
                                if (['PRD01', 'PRD02', 'PRD03', 'PRD04'].includes(self.form.campaignType)) {
                                    return "同一時間點、同一單品不可存在其他生效的﹝第N件(含)以上打X折﹞、﹝第N件(含)以上折X元﹞、﹝滿N件，每件打X折﹞、﹝滿N件，每件折X元﹞的行銷活動";
                                } else if (['PRD05'].includes(self.form.campaignType)) {
                                    return '同一時間點、同一單品不可存在其他生效的﹝買N件，送贈品﹞的行銷活動';
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
            methods: {
                // 新增贈品
                addGiveaway() {
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

                    $("#create-form").submit();
                },
                // 選擇活動類型
                changeCampaignType(value) {
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
            },
        });
    </script>
@endsection
