@extends('backend.layouts.master')

@section('title', '廣告上架 新增資料')

@section('css')
    <link rel="stylesheet" href="{{ mix('css/advertisement.css') }}">
@endsection

@section('content')
    @if ($errors->any())
        <div id="error-message" style="display: none;">
            {{ $errors->first('message') }}
        </div>
    @endif

    <div id="app" v-cloak>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="page-header">
                        <i class="fa-solid fa-plus"></i> 廣告上架 新增資料
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">請輸入下列欄位資料</div>
                        <div class="panel-body">
                            <form id="create-form" method="post" action="{{ route('advertisemsement_launch.store') }}"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-sm-12">
                                        @include('backend.advertisement.launch.slot_block')
                                        @include('backend.advertisement.launch.image_block')
                                        @include('backend.advertisement.launch.text_block')
                                        @include('backend.advertisement.launch.product_block')
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    @if ($share_role_auth['auth_create'])
                                                        <button
                                                            type="button"
                                                            class="btn btn-success"
                                                            @click="submit"
                                                            :disabled="submitButtonDisabled"
                                                        >
                                                            <i class="fa-solid fa-floppy-disk"></i> 儲存
                                                        </button>
                                                    @endif

                                                    <a href="{{ route('advertisemsement_launch') }}"
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
        @include('backend.advertisement.promotion_campaign_model')
    </div>
@endsection

@section('js')
    <script src="{{ mix('js/advertisement.js') }}"></script>
    <script>
        let vm = new Vue({
            el: "#app",
            data: {
                seeMore: {
                    action: "X",
                    categoryId: null,
                    categoryValid: true,
                    categoryRequired: false,
                },
                categoryTree: [],
                maxLevel: 1,
                submitButtonDisabled: false,
            },
            created() {
                let payload = @json($payload);

                if (!_.isEmpty(payload.category_tree)) {
                    this.categoryTree = payload.category_tree;
                }

                if (payload.max_level) {
                    this.maxLevel = payload.max_level;
                }
            },
            watch: {
                "seeMore.action"(value) {
                    this.seeMore.categoryRequired = value == 'C' ? true : false;
                },
            },
            methods: {
                normalizer(node) {
                    // remove empty children
                    if (_.isEmpty(node.children)) {
                        delete node.children;
                    }

                    // only use first and last level
                    if (node.category_level != 1 && node.category_level != this.maxLevel) {
                        node.isDisabled = true;
                    }

                    return {
                        id: node.id,
                        label: node.category_name,
                        children: node.children,
                    };
                },
                submit() {
                    this.seeMore.categoryValid = this.seeMore.categoryRequired && !this.seeMore.categoryId ? false : true;
                    $("#create-form").submit();
                },
            },
        });

        $(function() {
            let ad_slots = @json($payload['ad_slots']);
            // 商品分類下拉選項
            let product_category = @json($payload['product_category']);
            // 商品下拉選項
            let products = @json($payload['products']);

            $("#promotional_campaigns_time_type").select2({allowClear: false,});

            if ($('#error-message').length) {
                alert($('#error-message').text().trim());
            }

            // 驗證表單
            $("#create-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    vm.submitButtonDisabled = true;
                    form.submit();
                },
                rules: {
                    slot_id: {
                        required: true,
                    },
                    start_at: {
                        required: true,
                        dateGreaterThanNow: true,
                    },
                    end_at: {
                        required: true,
                        greaterThan: function() {
                            return $('#start_at').val();
                        },
                    },
                    see_more_url: {
                        required: {
                            depends: function (element) {
                                return $("input[name='see_more_action'][value='U']").is(":checked");
                            }
                        },
                        url: {
                            depends: function (element) {
                                return $("input[name='see_more_action'][value='U']").is(":checked");
                            }
                        },
                    },
                    active: {
                        required: true,
                        remote: {
                            param: {
                                url: "/backend/advertisemsement_launch/ajax/can-pass-active-validation",
                                type: "post",
                                dataType: "json",
                                cache: false,
                                data: {
                                    slot_id: function() {
                                        return $("#slot_id").val();
                                    },
                                    start_at: function() {
                                        return $('#start_at').val();
                                    },
                                    end_at: function() {
                                        return $('#end_at').val();
                                    },
                                },
                                dataFilter: function(data, type) {
                                    if (data) {
                                        let json_data = $.parseJSON(data);

                                        if (json_data.status) {
                                            return true;
                                        }
                                    }

                                    return false;
                                },
                            },
                            depends: function(element) {
                                return $("#slot_id").val() && $('#start_at').val() && $('#end_at')
                                    .val();
                            }
                        },
                    },
                },
                messages: {
                    end_at: {
                        greaterThan: "結束時間必須大於開始時間",
                    },
                    active: {
                        remote: "同一個版位、同一個時間點只能有一組「啟用」的設定",
                    },
                },
                errorClass: "help-block",
                errorElement: "span",
                errorPlacement: function(error, element) {
                    if (element.hasClass('vue-treeselect__input')) {
                        element.closest(".form-group").append(error);
                        return;
                    }

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
            });


            let ad_slot_select_options = getAdSlotSelectOptions(ad_slots);
            let product_category_select_options = getProductCategorySelectOptions(product_category);
            let product_select_options = getProductSelectOptions(products);

            init({
                'ad_slot_select_options': ad_slot_select_options,
                'product_category_select_options': product_category_select_options,
                'product_select_options': product_select_options,
            });

            $('#img_slot_icon_name, #btn-delete-slot-icon-name').hide();

            // 選擇版位
            $('#slot_id').on('change', function() {
                let element = $(this).find('option:selected');
                let is_user_defined = element.attr('data-is-user-defined');
                let slot_type = element.attr('data-slot-type');
                let photo_width = element.attr('data-photo-width');
                let photo_height = element.attr('data-photo-height');

                $('.image_block_image_name').map(function(obj) {
                    let vm = $(this);
                    const file = this.files[0];
                    if (file) {
                        if (photo_width && photo_height) { //顯示選擇照片的尺寸提醒
                            var img;
                            img = new Image();
                            var objectUrl = URL.createObjectURL(file);
                            img.onload = function() {
                                if (this.width !== parseInt(photo_width) || this.height !==
                                    parseInt(photo_height)) {
                                    let show_text = '上傳尺寸 ' + this.width + '*' + this.height + ' 非預期，存檔後系統會自動壓縮成制式尺寸！';
                                    vm.siblings('.select-img-size-box').show();
                                    vm.siblings('.select-img-size-box').find('.select-img-size-text').text(show_text);
                                } else {
                                    vm.siblings('.select-img-size-box').hide();
                                    vm.siblings('.select-img-size-box').find('.select-img-size-text').text('');
                                }
                                URL.revokeObjectURL(objectUrl);
                            };
                            img.src = objectUrl;
                        }
                    }
                });

                if (photo_width > 0 || photo_height > 0) {
                    $('.show_size').text('尺寸：' + photo_width + '*' + photo_height);
                } else {
                    $('.show_size').text('');
                }
                // 開放編輯 版位主色、版位icon、版位標題
                if (is_user_defined == 1) {
                    enableSlotColorCode();
                    enableSlotIconName();
                    enableSlotTitle();
                    enableTitleleColorCode();
                }
                // 開放編輯 版位主色
                else if (is_user_defined == 2) {
                    enableSlotColorCode();
                    disableSlotIconName();
                    disableSlotTitle();
                    disableTitleColorCode();
                }
                // 關閉編輯 版位主色、版位icon、版位標題
                else {
                    disableSlotColorCode();
                    disableSlotIconName();
                    disableSlotTitle();
                    disableTitleColorCode();
                }

                switch (slot_type) {
                    // 圖檔
                    case 'I':
                        $('#image-block').show();
                        $('#text-block').hide();
                        $('#product-block').hide();
                        break;

                        // 文字
                    case 'T':
                        $('#image-block').hide();
                        $('#text-block').show();
                        $('#product-block').hide();
                        break;

                        // 商品
                    case 'S':
                        $('#image-block').hide();
                        $('#text-block').hide();
                        $('#product-block').show();
                        break;

                        // 圖檔+商品
                    case 'IS':
                        $('#image-block').show();
                        $('#text-block').hide();
                        $('#product-block').show();
                        break;

                    default:
                        $('#image-block').hide();
                        $('#text-block').hide();
                        $('#product-block').hide();
                        break;
                }
            });
        });
    </script>
@endsection
