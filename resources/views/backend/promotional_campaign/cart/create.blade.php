@extends('backend.layouts.master')

@section('title', '滿額活動 新增資料')

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
                            <form id="create-form" method="post" action="{{ route('promotional_campaign_cart.store') }}">
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
                                                                v-model="form.campaignType" name="campaign_type">
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
                                            <br>

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

                                        <div v-if="showProduct">
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
                                                        <tr v-for="(product, index) in form.products"
                                                            :key="index">
                                                            <input type="hidden" :name="`products[${index}][product_id]`"
                                                                :value="product.productId">
                                                            <td>@{{ index + 1 }}</td>
                                                            <td>@{{ product.productNo }}</td>
                                                            <td>@{{ product.productName }}</td>
                                                            <td>@{{ product.sellingPrice }}</td>
                                                            <td>@{{ product.launchedAt }}</td>
                                                            <td>@{{ product.launchStatus }}</td>
                                                            <td>@{{ product.grossMargin }}</td>
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
                                                        <tr v-for="(giveaway, index) in form.giveaways"
                                                            :key="index">
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
                                                                        v-model="giveaway.assignedQty">
                                                                </div>
                                                            </td>
                                                            <td>
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
                                                    @if ($share_role_auth['auth_create'])
                                                        <button class="btn btn-success" type="button" @click="submitForm"
                                                            :disabled="saveButton.isDisabled">
                                                            <i class="fa-solid fa-floppy-disk"></i> 儲存
                                                        </button>
                                                    @endif
                                                    <a href="{{ route('promotional_campaign_cart') }}"
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

        <cart-campaign-product-modal :modal="productModal" @save="saveProductModalProducts"></cart-campaign-product-modal>
        <cart-campaign-product-modal :modal="giveawayModal" @save="saveGiveawayModalProducts"></cart-campaign-product-modal>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/promotional_campaign/cart/main.js') }}"></script>
    <script>
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
                showXValue: true,
                showXValueHint: true,
                showProduct: false,
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
                                    return {
                                        url: "/backend/promotional-campaign-cart/can-active",
                                        type: "post",
                                        dataType: "json",
                                        cache: false,
                                        data: {
                                            campaign_type: self.form.campaignType,
                                            start_at: self.form.startAt,
                                            end_at: self.form.endAt,
                                            n_value: self.form.nValue,
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
                                                            `${content.campaign_name}`;

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
                                        self.form.nValue &&
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
                            min: function() {
                                if (['CART02'].includes(self.form.campaignType)) {
                                    if (self.form.xValue) {
                                        return parseInt(self.form.xValue) + 1;
                                    }
                                }

                                return 1;
                            },
                        },
                        x_value: {
                            required: true,
                            min: function() {
                                if (['CART01'].includes(self.form.campaignType)) {
                                    return 0.01;
                                } else if (['CART02'].includes(self.form.campaignType)) {
                                    return 1;
                                }

                                return 0;
                            },
                            max: {
                                param: 0.99,
                                depends: function(element) {
                                    return ['CART01'].includes(self.form.campaignType);
                                },
                            },
                            maxlength: {
                                param: 4,
                                depends: function(element) {
                                    return ['CART01'].includes(self.form.campaignType);
                                },
                            },
                            digits: {
                                depends: function(element) {
                                    return ['CART02'].includes(self.form.campaignType);
                                },
                            },
                            number: {
                                depends: function(element) {
                                    return ['CART01'].includes(self.form.campaignType);
                                },
                            },
                        },
                        target_groups: {
                            required: true,
                        },
                    },
                    messages: {
                        end_at: {
                            greaterThan: "結束時間必須大於開始時間",
                        },
                        active: {
                            remote: function(element) {
                                if (['CART01', 'CART02'].includes(self.form.campaignType)) {
                                    return `同一時間點、同一N值不可存在其他生效的﹝購物車滿N元，打X折﹞、﹝購物車滿N元，折X元﹞的行銷活動<br/>
                                        ${conflictContents}`;
                                } else if (['CART03'].includes(self.form.campaignType)) {
                                    return `同一時間點、同一N值不可存在其他生效的﹝購物車滿N元，送贈品﹞的行銷活動<br/>
                                        ${conflictContents}`;
                                }
                            },
                        },
                        n_value: {
                            digits: "只可輸入正整數",
                            min: function(value) {
                                if (['CART02'].includes(self.form.campaignType)) {
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
                                if (['CART02'].includes(self.form.campaignType)) {
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
                "form.campaignType"(value) {
                    if (['CART01'].includes(value)) {
                        this.showXValueHint = true;
                    } else {
                        this.showXValueHint = false;
                    }

                    if (['CART04'].includes(value)) {
                        this.showProduct = true;
                    } else {
                        this.showProduct = false;
                    }

                    if (['CART01', 'CART02'].includes(value)) {
                        this.showXValue = true;
                        this.showGiveaway = false;
                    } else {
                        this.showXValue = false;
                        this.showGiveaway = true;
                    }
                },
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
                // 儲存商品modal的商品
                saveProductModalProducts(products) {
                    products.forEach(product => {
                        this.form.products.push({
                            productId: product.id,
                            productNo: product.productNo,
                            productName: product.productName,
                            sellingPrice: product.sellingPrice,
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
                        });
                    });
                },
            },
        });
    </script>
@endsection
