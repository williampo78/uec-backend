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

        #image-block .select2-container,
        #text-block .select2-container {
            max-width: 9em !important;
        }

    </style>
@endsection

@section('content')
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
    <script>
        $(function() {
            // 版位下拉選項
            let ad_slots = @json($ad_slots);
            let ad_slots_select_option = '';

            $.each(ad_slots, function(key, value) {
                ad_slots_select_option += `
                        <option value='${value['id']}' data-is-user-defined="${value['is_user_defined']}" data-slot-type="${value['slot_type']}">【${value['slot_code']}】${value['slot_desc']}</option>
                    `;
            });

            // 商品分類下拉選項
            let product_category = @json($product_category);
            let product_category_select_option = '';

            $.each(product_category, function(key, value) {
                product_category_select_option += `
                        <option value='${value['id']}'>${value['name']}</option>
                    `;
            });

            // 商品下拉選項
            let products = @json($products);
            let products_select_option = '';

            $.each(products, function(key, value) {
                products_select_option += `
                        <option value='${value['id']}'>${value['product_no']} ${value['product_name']}</option>
                    `;
            });

            // 添加資料
            $('#slot').append(ad_slots_select_option).prev('label').append(' <span style="color:red;">*</span>');
            $('#start_at').parents('.form-group').children('label').append(' <span style="color:red;">*</span>');
            $('#active_enabled').parents('.form-group').children('label').append(
                ' <span style="color:red;">*</span>');
            $('#image-block table > thead th').filter(function(i) {
                return $.inArray(i, [0, 1, 5]) > -1;
            }).append(' <span style="color:red;">*</span>');

            // 加入欄位驗證
            $('#slot').addClass('validate[required]');
            $('#start_at').addClass('validate[required]');
            $('#end_at').addClass('validate[required]');
            $('#active_enabled').addClass('validate[required]');
            $('#active_disabled').addClass('validate[required]');
            $('#slot_color_code').addClass('validate[required]');
            $('#slot_icon_name').addClass('validate[required]');
            $('#slot_title').addClass('validate[required]');

            $('.js-select2-slot').select2({
                allowClear: true,
                theme: "bootstrap",
                placeholder: '',
            });

            $('#datetimepicker_start_at').datetimepicker({
                format: 'YYYY-MM-DD HH:mm',
                showClear: true,
            });

            $('#datetimepicker_end_at').datetimepicker({
                format: 'YYYY-MM-DD HH:mm',
                showClear: true,
            });

            $("#datetimepicker_start_at").on("dp.change", function(e) {
                if ($('#end_at').val()) {
                    $('#datetimepicker_end_at').datetimepicker('minDate', e.date);
                }
            });

            $("#datetimepicker_end_at").on("dp.change", function(e) {
                if ($('#start_at').val()) {
                    $('#datetimepicker_start_at').datetimepicker('maxDate', e.date);
                }
            });

            $('.colorpicker').colorpicker({
                format: "hex",
                align: "left",
                customClass: 'colorpicker-2x',
                sliders: {
                    saturation: {
                        maxLeft: 200,
                        maxTop: 200
                    },
                    hue: {
                        maxTop: 200
                    },
                    alpha: {
                        maxTop: 200
                    }
                }
            });

            // 選擇版位
            $('#slot').on('change', function() {
                let element = $(this).find('option:selected');
                let is_user_defined = element.attr('data-is-user-defined');
                let slot_type = element.attr('data-slot-type');

                // 關閉編輯 版位主色、版位icon、版位標題
                if (is_user_defined == 0) {
                    $('#slot_color_code').prop('disabled', true);
                    $('#slot_icon_name').prop('disabled', true);
                    $('#slot_title').prop('disabled', true);
                }
                // 開放編輯 版位主色、版位icon、版位標題，皆為必填
                else {
                    $('#slot_color_code').prop('disabled', false);
                    $('#slot_icon_name').prop('disabled', false);
                    $('#slot_title').prop('disabled', false);
                }

                switch (slot_type) {
                    // 圖檔
                    case 'I':
                        $('#image-block').show();
                        $('#text-block').hide();
                        $('#product-block').hide();

                        if ($('#image-block table > tbody > tr').length <= 0) {
                            $('#btn-new-image').click();
                        }
                        break;
                        // 文字
                    case 'T':
                        $('#image-block').hide();
                        $('#text-block').show();
                        $('#product-block').hide();

                        if ($('#text-block table > tbody > tr').length <= 0) {
                            $('#btn-new-text').click();
                        }
                        break;
                        // 商品
                    case 'S':
                        $('#image-block').hide();
                        $('#text-block').hide();
                        $('#product-block').show();

                        if ($('#tab-product table > tbody > tr').length <= 0) {
                            $('#btn-new-product-product').click();
                        }

                        if ($('#tab-category table > tbody > tr').length <= 0) {
                            $('#btn-new-product-category').click();
                        }
                        break;
                        // 圖檔+商品
                    case 'IS':
                        $('#image-block').show();
                        $('#text-block').hide();
                        $('#product-block').show();

                        if ($('#image-block table > tbody > tr').length <= 0) {
                            $('#btn-new-image').click();
                        }

                        if ($('#tab-product table > tbody > tr').length <= 0) {
                            $('#btn-new-product-product').click();
                        }

                        if ($('#tab-category table > tbody > tr').length <= 0) {
                            $('#btn-new-product-category').click();
                        }
                        break;
                    default:
                        $('#image-block').hide();
                        $('#text-block').hide();
                        $('#product-block').hide();
                        break;
                }
            });

            // 新增圖檔
            $('#btn-new-image').on('click', function() {
                let image_block_row_no = $('#image-block-row-no').val();

                $('#image-block table > tbody').append(`
                        <tr>
                            <input type="hidden" name="image_block_id[${image_block_row_no}]" value="new">
                            <td>
                                <input type="text" class="form-control" name="image_block_sort[${image_block_row_no}]" value="" />
                            </td>
                            <td>
                                <input type="file" name="image_block_image_name[${image_block_row_no}]" value="" />
                            </td>
                            <td>
                                <input type="text" class="form-control" name="image_block_image_alt[${image_block_row_no}]" value="" />
                            </td>
                            <td>
                                <input type="text" class="form-control" name="image_block_image_title[${image_block_row_no}]" value="" />
                            </td>
                            <td>
                                <textarea class="form-control" rows="3" name="image_block_image_abstract[${image_block_row_no}]"></textarea>
                            </td>
                            <td>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="image_block_image_action[${image_block_row_no}]" value="X" checked />
                                        無連結
                                    </label>
                                </div>
                                <div class="form-inline text-nowrap">
                                    <div class="form-group">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="image_block_image_action[${image_block_row_no}]" value="U" />
                                                URL
                                            </label>
                                        </div>
                                        <input type="text" class="form-control" name="image_block_target_url[${image_block_row_no}]" value="" />
                                    </div>
                                </div>
                                <div class="form-inline text-nowrap">
                                    <div class="form-group">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="image_block_image_action[${image_block_row_no}]" value="C" />
                                                商品分類頁
                                            </label>
                                        </div>
                                        <select class="form-control js-select2-image-block-product-category" name="image_block_target_cate_hierarchy_id[${image_block_row_no}]">
                                            <option></option>
                                            ${product_category_select_option}
                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="image_block_is_target_blank[${image_block_row_no}]" value="enabled" />
                                    </label>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-delete-image"><i class='fa fa-ban'></i> 刪除</button>
                            </td>
                        </tr>
                    `);

                $('.js-select2-image-block-product-category').select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: '',
                });

                $('#image-block-row-no').val(parseInt(image_block_row_no) + 1);

                // 加入欄位驗證
                //$(`#image-block table > tbody [name="image_block_sort[${image_block_row_no}]"]`).addClass('validate[required]');
            });

            // 刪除圖檔
            $(document).on('click', '.btn-delete-image', function() {
                $(this).parents('tr').remove();
            });

            // 新增文字
            $('#btn-new-text').on('click', function() {
                let text_block_row_no = $('#text-block-row-no').val();

                $('#text-block table > tbody').append(`
                        <tr>
                            <input type="hidden" name="text_block_id[${text_block_row_no}]" value="new">
                            <td>
                                <input type="text" class="form-control" name="text_block_sort[${text_block_row_no}]" value="" />
                            </td>
                            <td>
                                <input type="text" class="form-control" name="text_block_texts[${text_block_row_no}]" value="" />
                            </td>
                            <td>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="text_block_image_action[${text_block_row_no}]" value="X" checked />
                                        無連結
                                    </label>
                                </div>
                                <div class="form-inline text-nowrap">
                                    <div class="form-group">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="text_block_image_action[${text_block_row_no}]" value="U" />
                                                URL
                                            </label>
                                            <input type="text" class="form-control" name="text_block_target_url[${text_block_row_no}]" value="" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-inline text-nowrap">
                                    <div class="form-group">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="text_block_image_action[${text_block_row_no}]" value="C" />
                                                商品分類頁
                                            </label>
                                            <select class="form-control js-select2-text-block-product-category" name="text_block_target_cate_hierarchy_id[${text_block_row_no}]">
                                                <option></option>
                                                ${product_category_select_option}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="text_block_is_target_blank[${text_block_row_no}]" value="enabled" />
                                    </label>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-delete-text"><i class='fa fa-ban'></i> 刪除</button>
                            </td>
                        </tr>
                    `);

                $('.js-select2-text-block-product-category').select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: '',
                });

                $('#text-block-row-no').val(parseInt(text_block_row_no) + 1);
            });

            // 刪除文字
            $(document).on('click', '.btn-delete-text', function() {
                $(this).parents('tr').remove();
            });

            // 點擊指定商品radio button
            $('#product_assigned_type_product').on('click', function() {
                $('#product-block-tab a[href="#tab-product"]').tab('show');
            });

            // 點擊指定分類radio button
            $('#product_assigned_type_category').on('click', function() {
                $('#product-block-tab a[href="#tab-category"]').tab('show');
            });

            // 點擊商品tab
            $('#product-block-tab a[href="#tab-product"]').on('show.bs.tab', function(e) {
                $('#product_assigned_type_product').prop('checked', true);
            });

            // 點擊分類tab
            $('#product-block-tab a[href="#tab-category"]').on('show.bs.tab', function(e) {
                $('#product_assigned_type_category').prop('checked', true);
            });

            // 新增商品的指定商品
            $('#btn-new-product-product').on('click', function() {
                let product_block_product_row_no = $('#product-block-product-row-no').val();

                $('#tab-product table > tbody').append(`
                        <tr>
                            <input type="hidden" name="product_block_product_id[${product_block_product_row_no}]" value="new">
                            <td>
                                <input type="text" class="form-control" name="product_block_product_sort[${product_block_product_row_no}]" value="" />
                            </td>
                            <td>
                                <select class="form-control js-select2-product-block-product" name="product_block_product_product_id[${product_block_product_row_no}]">
                                    <option></option>
                                    ${products_select_option}
                                </select>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-delete-product-product"><i class='fa fa-ban'></i> 刪除</button>
                            </td>
                        </tr>
                    `);

                $('.js-select2-product-block-product').select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: '',
                });

                $('#product-block-product-row-no').val(parseInt(product_block_product_row_no) + 1);
            });

            // 刪除商品的指定商品
            $(document).on('click', '.btn-delete-product-product', function() {
                $(this).parents('tr').remove();
            });

            // 新增商品的指定分類
            $('#btn-new-product-category').on('click', function() {
                let product_block_category_row_no = $('#product-block-category-row-no').val();

                $('#tab-category table > tbody').append(`
                        <tr>
                            <input type="hidden" name="product_block_category_id[${product_block_category_row_no}]" value="new">
                            <td>
                                <input type="text" class="form-control" name="product_block_category_sort[${product_block_category_row_no}]" value="" />
                            </td>
                            <td>
                                <select class="form-control js-select2-product-block-category" name="product_block_product_web_category_hierarchy_id[${product_block_category_row_no}]">
                                    <option></option>
                                    ${product_category_select_option}
                                </select>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-delete-product-category"><i class='fa fa-ban'></i> 刪除</button>
                            </td>
                        </tr>
                    `);

                $('.js-select2-product-block-category').select2({
                    allowClear: true,
                    theme: "bootstrap",
                    placeholder: '',
                });

                $('#product-block-category-row-no').val(parseInt(product_block_category_row_no) + 1);
            });

            // 刪除商品的指定分類
            $(document).on('click', '.btn-delete-product-category', function() {
                $(this).parents('tr').remove();
            });

            $("#create-form").validationEngine({
                scroll: false,
            });

            $('#btn-save').on('click', function() {
                $("#create-form").submit();
            });

            $('#btn-cancel').on('click', function() {
                location.href = "{{ route('advertisemsement_launch') }}";
            });
        });
    </script>
@endsection
