@extends('Backend.master')

@section('title', '滿額活動 新增資料')

@section('style')
    <style>
        .modal-dialog {
            max-width: 100%;
        }
        .display-flex-center {
            display:flex;
            align-items:center;
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
                                    @include('Backend.PromotionalCampaign.CART.campaign_block')
                                    @include('Backend.PromotionalCampaign.CART.applicable_target_block')
                                    @include('Backend.PromotionalCampaign.CART.prd_block')
                                    @include('Backend.PromotionalCampaign.CART.gift_block')

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                {{-- @if ($share_role_auth['auth_create']) --}}
                                                <button class="btn btn-success" type="button" id="btn-save"><i
                                                        class="fa fa-save"></i> 儲存</button>
                                                {{-- @endif --}}
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

    @include('Backend.PromotionalCampaign.CART.prd_modal')
@endsection

@section('js')
    <script src="{{ asset('asset/js/promotional-campaign/main.js') }}"></script>
    {{-- <script src="{{ asset('asset/js/advertisement/validate.js') }}"></script> --}}

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
            /*
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
                            unhighlight: function(element, errorClass, validClass) {
                                $(element).closest(".form-group").removeClass("has-error");
                            },
                            success: function(label, element) {
                                $(element).closest(".form-group").removeClass("has-error");
                            },
                        });
            */

            let campaign_types = @json($campaign_types);
            let suppliers = @json($suppliers);
            let product_type_option = @json(config('uec.product_type_option'));

            init({
                'campaign_types': campaign_types,
                'suppliers': suppliers,
                'product_type_option': product_type_option,
            });

            $('#btn-new-prd').on('click', function() {
                $('#prd_modal').modal('show');
            });

            var exist_product = [];

            $('#btn-search-product').on('click', function() {
                let supplier_id = $('#supplier_id').val();
                let product_no = $('#product_no').val();
                let product_name = $('#product_name').val();
                let selling_price_min = $('#selling_price_min').val();
                let selling_price_max = $('#selling_price_max').val();
                let start_created_at = $('#start_created_at').val();
                let end_created_at = $('#end_created_at').val();
                let start_launched_at = $('#start_launched_at').val();
                let end_launched_at = $('#end_launched_at').val();
                let product_type = $('#product_type').val();
                let limit = $('#limit').val();

                console.log(supplier_id + ' ' + product_no + ' ' + product_name + ' ' + selling_price_min + ' ' + selling_price_max + ' ' + start_created_at + ' ' + end_created_at + ' ' + start_launched_at + ' ' + end_launched_at + ' ' + product_type + ' ' + limit + ' ' + exist_product);

                axios.post('/backend/promotional_campaign/ajax/products', {
                    supplier_id: supplier_id,
                    product_no: product_no,
                    product_name: product_name,
                    selling_price_min: selling_price_min,
                    selling_price_max: selling_price_max,
                    start_created_at: start_created_at,
                    end_created_at: end_created_at,
                    start_launched_at: start_launched_at,
                    end_launched_at: end_launched_at,
                    product_type: product_type,
                    limit: limit,
                    exist_product: exist_product,
                })
                .then(function(response) {
                    console.log(response.data);
                })
                .catch(function(error) {
                    console.log(error);
                });

            });
            /*
                        // 選擇版位
                        $('#slot_id').on('change', function() {
                            let element = $(this).find('option:selected');
                            let is_user_defined = element.attr('data-is-user-defined');
                            let slot_type = element.attr('data-slot-type');

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
                        });*/
        });
    </script>
@endsection
