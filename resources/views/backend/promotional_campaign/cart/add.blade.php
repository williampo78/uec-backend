@extends('backend.master')

@section('title', '滿額活動 新增資料')

@section('style')
    <style>
        .modal-dialog {
            max-width: 100%;
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
                            action="{{ route('promotional_campaign_cart.store') }}">
                            @csrf

                            <div class="row">
                                <!-- 欄位 -->
                                <div class="col-sm-12">
                                    @include('backend.promotional_campaign.campaign_block')
                                    @include('backend.promotional_campaign.applicable_target_block')
                                    @include('backend.promotional_campaign.prd_block')
                                    @include('backend.promotional_campaign.gift_block')

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

    @include('backend.promotional_campaign.prd_modal')
    @include('backend.promotional_campaign.gift_modal')
@endsection

@section('js')
    <script src="{{ asset('asset/js/promotional-campaign/main.js') }}"></script>
    <script src="{{ asset('asset/js/promotional-campaign/validate.js') }}"></script>

    <script>
        $(function() {
            if ($('#error-message').length) {
                alert($('#error-message').text().trim());
            }

            $('#btn-save').on('click', function() {
                $("#create-form").submit();
            });

            $('#btn-cancel').on('click', function() {
                location.href = "{{ route('promotional_campaign_cart') }}";
            });

            let conflict_campaign_names = '';
            // 驗證表單
            $("#create-form").validate({
                // debug: true,
                submitHandler: function(form) {
                    $('#btn-save').prop('disabled', true);
                    form.submit();
                },
                rules: {
                    campaign_name: {
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
                    campaign_type: {
                        required: true,
                    },
                    start_at: {
                        required: true,
                        // dateGreaterThanNow: true,
                    },
                    end_at: {
                        required: true,
                        greaterThan: function() {
                            return $('#start_at').val();
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
                    target_groups: {
                        required: true,
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
                        min: "只可輸入正整數",
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
                    if (element.is(':radio[name="active"]')) {
                        element.parent().parent().parent().append(error);
                        return;
                    }

                    if (element.parent('.input-group').length || element.is(':radio')) {
                        error.insertAfter(element.parent());
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

            let campaign_types = @json($campaign_types);
            renderCampaignType(campaign_types);

            let suppliers = @json($suppliers);
            renderPrdModalSupplier(suppliers);
            renderGiftModalSupplier(suppliers);

            let product_types = @json(config('uec.product_type_option'));
            renderPrdModalProductType(product_types);
            renderGiftModalProductType(product_types);

            $('#prd-modal-product-type').find('option[value="G"], option[value="A"]').remove(); // 移除贈品、加購品
            $('#prd-modal-product-type option[value="N"]').prop("selected", true); // 預設為一般品

            $('#gift-modal-product-type option[value="A"]').remove(); // 移除加購品
            $('#gift-modal-product-type option[value="G"]').prop("selected", true); // 預設為贈品

            init();

            var prd_modal_product_list = {}; // 單品modal清單中的商品
            var prd_product_list = {}; // 單品清單中的商品

            // 新增單品
            $('#btn-new-prd').on('click', function() {
                $('#prd-modal').modal('show');
            });

            // 單品modal商品全勾選
            $('#prd-modal-btn-check-all').on('click', function() {
                $('#prd-modal-product-table > tbody [name="choose_product"]').prop('checked', true);
            });

            // 單品modal商品全取消
            $('#prd-modal-btn-cancel-all').on('click', function() {
                $('#prd-modal-product-table > tbody [name="choose_product"]').prop('checked', false);
            });

            // 單品modal儲存、儲存並關閉
            $('#prd-modal-btn-save, #prd-modal-btn-save-and-close').on('click', function() {
                // 取得單品modal清單中有勾選的商品
                $('#prd-modal-product-table > tbody [name="choose_product"]:checked').closest('tr').each(
                    function() {
                        let id = $(this).attr('data-id');

                        prd_product_list[id] = prd_modal_product_list[id]; // 增加單品清單中的商品
                        delete prd_modal_product_list[id]; // 移除單品modal清單中的商品
                    });

                renderPrdProductList(prd_product_list);
                renderPrdModalProductList(prd_modal_product_list);
            });

            // 刪除單品清單中的商品
            $(document).on('click', '.btn-delete-prd', function() {
                if (confirm('確定要刪除嗎?')) {
                    let id = $(this).closest('tr').attr('data-id');

                    delete prd_product_list[id]; // 移除單品清單中的商品

                    renderPrdProductList(prd_product_list);
                }
            });

            // 單品modal搜尋
            $('#prd-modal-btn-search').on('click', function() {
                let query_datas = {
                    'supplier_id': $('#prd-modal-supplier-id').val(),
                    'product_no': $('#prd-modal-product-no').val(),
                    'product_name': $('#prd-modal-product-name').val(),
                    'selling_price_min': $('#prd-modal-selling-price-min').val(),
                    'selling_price_max': $('#prd-modal-selling-price-max').val(),
                    'start_created_at': $('#prd-modal-start-created-at').val(),
                    'end_created_at': $('#prd-modal-end-created-at').val(),
                    'start_launched_at_start': $('#prd-modal-start-launched-at-start').val(),
                    'start_launched_at_end': $('#prd-modal-start-launched-at-end').val(),
                    'product_type': $('#prd-modal-product-type').val(),
                    'limit': $('#prd-modal-limit').val(),
                    'exist_products': Object.keys(prd_product_list),
                };

                getProducts(query_datas).then(products => {
                    prd_modal_product_list = products;

                    renderPrdModalProductList(prd_modal_product_list);
                });
            });

            var gift_modal_product_list = {}; // 贈品modal清單中的商品
            var gift_product_list = {}; // 贈品清單中的商品

            // 新增贈品
            $('#btn-new-gift').on('click', function() {
                $('#gift-modal').modal('show');
            });

            // 贈品modal商品全勾選
            $('#gift-modal-btn-check-all').on('click', function() {
                $('#gift-modal-product-table > tbody [name="choose_product"]').prop('checked', true);
            });

            // 贈品modal商品全取消
            $('#gift-modal-btn-cancel-all').on('click', function() {
                $('#gift-modal-product-table > tbody [name="choose_product"]').prop('checked', false);
            });

            // 贈品modal儲存、儲存並關閉
            $('#gift-modal-btn-save, #gift-modal-btn-save-and-close').on('click', function() {
                // 取得贈品modal清單中有勾選的商品
                $('#gift-modal-product-table > tbody [name="choose_product"]:checked').closest('tr').each(
                    function() {
                        let id = $(this).attr('data-id');

                        gift_product_list[id] = gift_modal_product_list[id]; // 增加贈品清單中的商品
                        delete gift_modal_product_list[id]; // 移除贈品modal清單中的商品
                    });

                renderGiftProductList(gift_product_list);
                renderGiftModalProductList(gift_modal_product_list);
            });

            // 刪除贈品清單中的商品
            $(document).on('click', '.btn-delete-gift', function() {
                if (confirm('確定要刪除嗎?')) {
                    let id = $(this).closest('tr').attr('data-id');

                    delete gift_product_list[id]; // 移除贈品清單中的商品

                    renderGiftProductList(gift_product_list);
                }
            });

            // 贈品modal搜尋
            $('#gift-modal-btn-search').on('click', function() {
                let query_datas = {
                    'supplier_id': $('#gift-modal-supplier-id').val(),
                    'product_no': $('#gift-modal-product-no').val(),
                    'product_name': $('#gift-modal-product-name').val(),
                    'selling_price_min': $('#gift-modal-selling-price-min').val(),
                    'selling_price_max': $('#gift-modal-selling-price-max').val(),
                    'start_created_at': $('#gift-modal-start-created-at').val(),
                    'end_created_at': $('#gift-modal-end-created-at').val(),
                    'start_launched_at_start': $('#gift-modal-start-launched-at-start').val(),
                    'start_launched_at_end': $('#gift-modal-start-launched-at-end').val(),
                    'product_type': $('#gift-modal-product-type').val(),
                    'limit': $('#gift-modal-limit').val(),
                    'exist_products': Object.keys(gift_product_list),
                };

                getProducts(query_datas).then(products => {
                    gift_modal_product_list = products;

                    renderGiftModalProductList(gift_modal_product_list);
                });
            });

            // 選擇活動類型
            $('#campaign_type').on('change', function() {
                switch ($(this).val()) {
                    // ﹝滿額﹞購物車滿N元，打X折
                    case 'CART01':
                        $('#prd-block').hide();
                        $('#gift-block').hide();
                        $('#x_value').closest('.form-group').show().find('div:last').show();
                        break;
                        // ﹝滿額﹞購物車滿N元，折X元
                    case 'CART02':
                        $('#prd-block').hide();
                        $('#gift-block').hide();
                        $('#x_value').closest('.form-group').show().find('div:last').hide();
                        break;
                        // ﹝滿額﹞購物車滿N元，送贈品
                    case 'CART03':
                        $('#prd-block').hide();
                        $('#gift-block').show();
                        $('#x_value').closest('.form-group').hide();
                        break;
                        // ﹝滿額﹞指定商品滿N件，送贈品
                    case 'CART04':
                        $('#prd-block').show();
                        $('#gift-block').show();
                        $('#x_value').closest('.form-group').hide();
                        break;
                    default:
                        $('#prd-block').hide();
                        $('#gift-block').hide();
                        $('#x_value').closest('.form-group').show().find('div:last').show();
                        break;
                }
            });
        });
    </script>
@endsection
