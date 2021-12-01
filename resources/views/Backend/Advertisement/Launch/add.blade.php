@extends('Backend.master')

@section('title', '新增廣告上架')

@section('style')
    <style>
        .colorpicker-2x .colorpicker-saturation {
            width: 200px;
            height: 200px;
        }

        .colorpicker-2x .colorpicker-hue,
        .colorpicker-2x .colorpicker-alpha {
            width: 30px;
            height: 200px;
        }

        .colorpicker-2x .colorpicker-color,
        .colorpicker-2x .colorpicker-color div {
            height: 30px;
        }

        .tab-content {
            border: 1px solid #ddd;
            padding: 30px;
        }
    </style>
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
                        <form role="form" id="create-form" method="post"
                            action="{{ route('advertisemsement_launch.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- 欄位 -->
                                <div class="col-sm-12">
                                    @include('Backend.Advertisement.Launch.slot_block')
                                    @include('Backend.Advertisement.Launch.image_block')
                                    @include('Backend.Advertisement.Launch.text_block')
                                    @include('Backend.Advertisement.Launch.product_block')

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                @if ($share_role_auth['auth_create'])
                                                    <button class="btn btn-success" type="button" id="btn-save"><i
                                                            class="fa fa-save"></i> 儲存</button>
                                                @endif
                                                <button class="btn btn-danger" type="button" id="btn-cancel"><i
                                                        class="fa fa-ban"></i> 取消</button>
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
@endsection

@section('js')
    <script src="{{ asset('asset/js/advertisement/main.js') }}"></script>
    <script src="{{ asset('asset/js/advertisement/validate.js') }}"></script>

    <script>
        $(function() {
            if ($('#error-message').length) {
                alert($('#error-message').text().trim());
            }

            $('#btn-save').on('click', function() {
                $("#create-form").submit();
            });

            $('#btn-cancel').on('click', function() {
                location.href = "{{ route('advertisemsement_launch') }}";
            });

            // 驗證表單
            $("#create-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#btn-save').prop('disabled', true);
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
                    active: {
                        required: true,
                        remote: {
                            param: {
                                url: "/backend/advertisemsement_launch/ajax/canPassActiveValidation",
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

            let ad_slots = @json($ad_slots);
            let ad_slot_select_options = getAdSlotSelectOptions(ad_slots);

            // 商品分類下拉選項
            let product_category = @json($product_category);
            let product_category_select_options = getProductCategorySelectOptions(product_category);

            // 商品下拉選項
            let products = @json($products);
            let product_select_options = getProductSelectOptions(products);

            init({
                'ad_slot_select_options': ad_slot_select_options,
                'product_category_select_options': product_category_select_options,
                'product_select_options': product_select_options,
            });

            // 選擇版位
            $('#slot_id').on('change', function() {
                let element = $(this).find('option:selected');
                let is_user_defined = element.attr('data-is-user-defined');
                let slot_type = element.attr('data-slot-type');

                // 開放編輯 版位主色、版位icon、版位標題
                if (is_user_defined == 1) {
                    $('#slot_color_code').prop('disabled', false);
                    $('#slot_icon_name').prop('disabled', false);
                    $('#slot_title').prop('disabled', false);

                    if ($('#slot_color_code').prev('label').find('span').length < 1) {
                        $('#slot_color_code').prev('label').append(' <span style="color:red;">*</span>');
                    }

                    if ($('#slot_icon_name').closest('.form-group').find('label > span').length < 1) {
                        $('#slot_icon_name').closest('.form-group').find('label').append(' <span style="color:red;">*</span>');
                    }

                    if ($('#slot_title').prev('label').find('span').length < 1) {
                        $('#slot_title').prev('label').append(' <span style="color:red;">*</span>');
                    }

                    validateUserDefinedBlock();
                }
                // 關閉編輯 版位主色、版位icon、版位標題，皆為必填
                else {
                    $('#slot_color_code').prop('disabled', true);
                    $('#slot_icon_name').prop('disabled', true);
                    $('#slot_title').prop('disabled', true);

                    $('#slot_color_code').prev('label').find('span').remove();
                    $('#slot_icon_name').prev('label').find('span').remove();
                    $('#slot_title').prev('label').find('span').remove();

                    removeUserDefinedBlockValidation();
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
