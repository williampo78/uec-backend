@extends('backend.layouts.master')

@section('title', '編輯廣告上架')

@section('css')
    <link rel="stylesheet" href="{{ mix('css/advertisement.css') }}">
@endsection

@section('content')
    @if ($errors->any())
        <div id="error-message" style="display: none;">
            {{ $errors->first('message') }}
        </div>
    @endif

    <div id="page-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">請輸入下列欄位資料</div>
                    <div class="panel-body">
                        <form id="update-form" method="post"
                            action="{{ route('advertisemsement_launch.update', $ad_slot_content['content']['slot_content_id']) }}"
                            enctype="multipart/form-data">
                            @method('PUT')
                            @csrf

                            <div class="row">
                                <!-- 欄位 -->
                                <div class="col-sm-12">
                                    @include(
                                        'backend.advertisement.launch.slot_block'
                                    )
                                    @include(
                                        'backend.advertisement.launch.image_block'
                                    )
                                    @include(
                                        'backend.advertisement.launch.text_block'
                                    )
                                    @include(
                                        'backend.advertisement.launch.product_block'
                                    )

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                @if ($share_role_auth['auth_update'])
                                                    <button class="btn btn-success" type="button" id="btn-save">
                                                        <i class="fa-solid fa-floppy-disk"></i> 儲存
                                                    </button>
                                                @endif

                                                <button class="btn btn-danger" type="button" id="btn-cancel">
                                                    <i class="fa-solid fa-ban"></i> 取消
                                                </button>
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
        @include('backend.advertisement.promotion_campaign_model')
    </div>
@endsection

@section('js')
    <script src="{{ mix('js/advertisement.js') }}"></script>

    <script>
        $(function() {
            $("#see_more_cate_hierarchy_id").select2();
            // 商品分類下拉選項
            let product_category = @json($product_category);
            // 商品下拉選項
            let products = @json($products);
            let content = @json($ad_slot_content['content']);
            let details = @json($ad_slot_content['details']);

            if ($('#error-message').length) {
                alert($('#error-message').text().trim());
            }

            $('#btn-save').on('click', function() {
                $("#update-form").submit();
            });

            $('#btn-cancel').on('click', function() {
                location.href = "{{ route('advertisemsement_launch') }}";
            });

            let product_category_select_options = getProductCategorySelectOptions(product_category);
            let product_select_options = getProductSelectOptions(products);

            // 驗證表單
            $("#update-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#btn-save').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    see_more_url:{
                        required: {
                            depends: function (element) {
                                return $("input[name='see_more_action'][value='U']").is(":checked");
                            }
                        },
                        url:{
                            depends: function (element) {
                                return $("input[name='see_more_action'][value='U']").is(":checked");
                            }
                        },
                    },
                    see_more_cate_hierarchy_id:{
                        required: {
                            depends: function (element) {
                                return $("input[name='see_more_action'][value='C']").is(":checked");
                            }
                        },
                    },
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
                        dateGreaterThanNow: {
                            depends: function(element) {
                                return $('#active_enabled').is(':checked');
                            },
                        },
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
                                    active: function() {
                                        if ($('#active_enabled').is(':checked')) {
                                            return 1;
                                        }

                                        return 0;
                                    },
                                    slot_content_id: function() {
                                        return content.slot_content_id;
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
                                    .val() && $('#active_enabled, #active_disabled').is(':disabled');
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
                                    slot_content_id: function() {
                                        return content.slot_content_id;
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
                        remote: "同一個版位、同一個時間點只能有一組「啟用」的設定",
                    },
                    active: {
                        remote: "同一個版位、同一個時間點只能有一組「啟用」的設定",
                    },
                },
                errorClass: "help-block",
                errorElement: "span",
                errorPlacement: function(error, element) {
                    if (element.is(':file')) {
                        error.insertAfter(element);
                        return;
                    }

                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                        return;
                    }

                    if (element.closest(".form-group").length) {
                        element.closest(".form-group").append(error);
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
            let photo_width = content.photo_width;
            let photo_height = content.photo_height;
            if (photo_width > 0 || photo_height > 0) {
                $('.show_size').text('尺寸：' + photo_width + '*' + photo_height);
            } else {
                $('.show_size').text('');
            }
            $('#slot_id').empty().append(
                `<option data-photo-width="${content.photo_width}" data-photo-height="${content.photo_height}" value="${content.slot_id}">【${content.slot_code}】${content.slot_desc}</option>`
            ).prop(
                'disabled', true);
            $('#start_at').val(content.start_at);
            $('#end_at').val(content.end_at);

            // 唯有「當前時間」小於﹝上架開始時間﹞時，﹝上架開始時間﹞、﹝上架結束時間﹞、﹝狀態﹞才都開放修改；否則﹝上架開始時間﹞與﹝狀態﹞都不開放修改
            if (new Date() >= new Date($('#start_at').val())) {
                $('#start_at, #active_enabled, #active_disabled').prop('disabled', true);
            }
            switch (content.see_more_action) {
                case 'X':
                    $("input[name='see_more_action'][value='X']").prop('checked', true);
                    break;
                case 'U':
                    $("input[name='see_more_action'][value='U']").prop('checked', true);
                    $('#see_more_url').val(content.see_more_url);
                    break;
                case 'C':
                    $("input[name='see_more_action'][value='C']").prop('checked', true);
                    $('#see_more_cate_hierarchy_id').val(content.see_more_cate_hierarchy_id).trigger("change");
                    break;
                default:
                    $('#see_more_url').val(content.see_more_url);
                    break;
            }

            if (content.slot_content_active == 1) {
                $('#active_enabled').prop('checked', true);
            } else {
                $('#active_disabled').prop('checked', true);
            }

            $('#img_slot_icon_name, #btn-delete-slot-icon-name').hide();
            $('#remark').val(content.contents_remark);
            if(content.see_more_target_blank){
                $('#see_more_target_blank').prop('checked', true);
            }
            // 開放編輯 版位主色、版位icon、版位標題
            if (content.is_user_defined == 1) {
                enableSlotColorCode();
                enableSlotIconName();
                enableSlotTitle();
                enableTitleleColorCode();
                $('#slot_color_code').val(content.slot_color_code);
                $('#slot_title_color').val(content.slot_title_color);
                $('#img_slot_icon_name').attr('src', content.slot_icon_name_url);
                $("#slot_icon_name").hide();
                $('#img_slot_icon_name, #btn-delete-slot-icon-name').show();
                $('#slot_title').val(content.slot_title);
            }
            // 開放編輯 版位主色
            else if (content.is_user_defined == 2) {
                enableSlotColorCode();
                $('#slot_color_code').val(content.slot_color_code);
            }

            $.each(details, function(key, value) {
                switch (value.data_type) {
                    case 'IMG':
                        addImageBlock(product_category_select_options, value);

                        switch (value.image_action) {
                            case 'X':
                                $(`#image-block table > tbody [name="image_block_image_action[${value.id}]"][value="X"]`)
                                    .prop('checked', true);
                                break;
                            case 'U':
                                $(`#image-block table > tbody [name="image_block_image_action[${value.id}]"][value="U"]`)
                                    .prop('checked', true);
                                break;
                            case 'C':
                                $(`#image-block table > tbody [name="image_block_image_action[${value.id}]"][value="C"]`)
                                    .prop('checked', true);
                                break;
                            case 'M':
                                $(`#image-block table > tbody [name="image_block_image_action[${value.id}]"][value="M"]`)
                                    .prop('checked', true);
                                break;
                        }

                        if (value.target_cate_hierarchy_id) {
                            $(`#image-block table > tbody [name="image_block_target_cate_hierarchy_id[${value.id}]"]`)
                                .val(value.target_cate_hierarchy_id).trigger('change');
                        }

                        if (value.is_target_blank == 1) {
                            $(`#image-block table > tbody [name="image_block_is_target_blank[${value.id}]"]`)
                                .prop('checked', true);
                        }
                        break;
                    case 'TXT':
                        addTextBlock(product_category_select_options, value);

                        switch (value.image_action) {
                            case 'X':
                                $(`#text-block table > tbody [name="text_block_image_action[${value.id}]"][value="X"]`)
                                    .prop('checked', true);
                                break;

                            case 'U':
                                $(`#text-block table > tbody [name="text_block_image_action[${value.id}]"][value="U"]`)
                                    .prop('checked', true);
                                break;

                            case 'C':
                                $(`#text-block table > tbody [name="text_block_image_action[${value.id}]"][value="C"]`)
                                    .prop('checked', true);
                                break;
                            case 'M':
                                $(`#text-block table > tbody [name="text_block_image_action[${value.id}]"][value="M"]`)
                                    .prop('checked', true);
                                break;
                        }

                        if (value.target_cate_hierarchy_id) {
                            $(`#text-block table > tbody [name="text_block_target_cate_hierarchy_id[${value.id}]"]`)
                                .val(value.target_cate_hierarchy_id).trigger('change');
                        }

                        if (value.is_target_blank == 1) {
                            $(`#text-block table > tbody [name="text_block_is_target_blank[${value.id}]"]`)
                                .prop('checked', true);
                        }
                        break;
                    case 'PRD':
                        if (value.product_id) {
                            addProductBlockProduct(product_select_options, value);

                            $(`#tab-product table > tbody [name="product_block_product_product_id[${value.id}]"]`)
                                .val(value.product_id).trigger('change');
                        }

                        if (value.web_category_hierarchy_id) {
                            addProductBlockCategory(product_category_select_options, value);

                            $(`#tab-category table > tbody [name="product_block_product_web_category_hierarchy_id[${value.id}]"]`)
                                .val(value.web_category_hierarchy_id).trigger('change');
                        }
                        break;
                }
            });

            init({
                'product_category_select_options': product_category_select_options,
                'product_select_options': product_select_options,
            });

            switch (content.product_assigned_type) {
                // 指定商品
                case 'P':
                    $('#product_assigned_type_product').click();

                    if ($(`#tab-product table > tbody > tr`).length > 0) {
                        $('#product_assigned_type_category').parent('label').hide();
                        $('#product-block-tab a[href="#tab-category"]').parent('li').hide();
                    }
                    break;

                    // 指定分類
                case 'C':
                    $('#product_assigned_type_category').click();

                    if ($(`#tab-category table > tbody > tr`).length > 0) {
                        $('#product_assigned_type_product').parent('label').hide();
                        $('#product-block-tab a[href="#tab-product"]').parent('li').hide();
                    }
                    break;
            }

            switch (content.slot_type) {
                // 圖檔
                case 'I':
                    $('#image-block').show();
                    break;
                    // 文字
                case 'T':
                    $('#text-block').show();
                    break;
                    // 商品
                case 'S':
                    $('#product-block').show();
                    break;
                    // 圖檔+商品
                case 'IS':
                    $('#image-block').show();
                    $('#product-block').show();
                    break;
            }
        });
    </script>
@endsection
