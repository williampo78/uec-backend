@extends('Backend.master')

@section('title', '編輯廣告上架')

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
                    <div class="panel-body" id="requisitions_vue_app">
                        <form role="form" id="update-form" method="post"
                            action="{{ route('advertisemsement_launch.update', $ad_slot_content['content']['slot_content_id']) }}"
                            enctype="multipart/form-data">
                            @method('PUT')
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
                                                @if ($share_role_auth['auth_update'])
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
            if ($('#error-message').length) {
                alert($('#error-message').text().trim());
            }

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

            // 新增圖檔
            $('#btn-new-image').on('click', function() {
                image_block_row_no = $('#image-block-row-no').val();

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
                if (confirm('確定要刪除嗎?')) {
                    $(this).parents('tr').remove();
                }
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
                if (confirm('確定要刪除嗎?')) {
                    $(this).parents('tr').remove();
                }
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
                if (confirm('確定要刪除嗎?')) {
                    $(this).parents('tr').remove();
                }
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
                if (confirm('確定要刪除嗎?')) {
                    $(this).parents('tr').remove();
                }
            });

            let content = @json($ad_slot_content['content']);
            let details = @json($ad_slot_content['details']);

            $('#slot_id').empty().append(`<option>【${content.slot_code}】${content.slot_desc}</option>`).prop(
                'disabled', true);
            $('#start_at').val(content.start_at);
            $('#end_at').val(content.end_at);

            if (content.slot_content_active == 1) {
                $('#active_enabled').prop('checked', true);
            } else {
                $('#active_disabled').prop('checked', true);
            }

            // 關閉編輯 版位主色、版位icon、版位標題
            if (content.is_user_defined == 1) {
                $('#slot_color_code').prop('disabled', false).val(content.slot_color_code);
                $('#slot_icon_name').prop('disabled', false);
                $(`<img src="${content.slot_icon_name_url}" class="img-responsive" width="400" height="400" />`)
                    .insertBefore('#slot_icon_name');
                $('#slot_title').prop('disabled', false).val(content.slot_title);
            }

            $.each(details, function(key, value) {
                let sort = value.sort ? value.sort : '';
                let image_name_url = value.image_name_url ?
                    `<img src="${value.image_name_url}" class="img-responsive" width="400" height="400" />` :
                    '';
                let image_alt = value.image_alt ? value.image_alt : '';
                let image_title = value.image_title ? value.image_title : '';
                let image_abstract = value.image_abstract ? value.image_abstract : '';
                let target_url = value.target_url ? value.target_url : '';
                let texts = value.texts ? value.texts : '';

                switch (value.data_type) {
                    case 'IMG':
                        $('#image-block table > tbody').append(`
                            <tr>
                                <input type="hidden" name="image_block_id[${value.id}]" value="${value.id}">
                                <td>
                                    <input type="text" class="form-control" name="image_block_sort[${value.id}]" value="${sort}" />
                                </td>
                                <td>
                                    ${image_name_url}
                                    <input type="file" name="image_block_image_name[${value.id}]" value="" />
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="image_block_image_alt[${value.id}]" value="${image_alt}" />
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="image_block_image_title[${value.id}]" value="${image_title}" />
                                </td>
                                <td>
                                    <textarea class="form-control" rows="3" name="image_block_image_abstract[${value.id}]">${image_abstract}</textarea>
                                </td>
                                <td>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="image_block_image_action[${value.id}]" value="X" />
                                            無連結
                                        </label>
                                    </div>
                                    <div class="form-inline text-nowrap">
                                        <div class="form-group">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="image_block_image_action[${value.id}]" value="U" />
                                                    URL
                                                </label>
                                            </div>
                                            <input type="text" class="form-control" name="image_block_target_url[${value.id}]" value="${target_url}" />
                                        </div>
                                    </div>
                                    <div class="form-inline text-nowrap">
                                        <div class="form-group">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="image_block_image_action[${value.id}]" value="C" />
                                                    商品分類頁
                                                </label>
                                            </div>
                                            <select class="form-control js-select2-image-block-product-category" name="image_block_target_cate_hierarchy_id[${value.id}]">
                                                <option></option>
                                                ${product_category_select_option}
                                            </select>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="image_block_is_target_blank[${value.id}]" value="enabled" />
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-delete-image"><i class='fa fa-ban'></i> 刪除</button>
                                </td>
                            </tr>
                        `);

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
                        }

                        if (value.target_cate_hierarchy_id) {
                            $(`#image-block table > tbody [name="image_block_target_cate_hierarchy_id[${value.id}]"] option[value="${value.target_cate_hierarchy_id}"]`)
                                .prop('selected', true);
                        }

                        if (value.is_target_blank == 1) {
                            $(`#image-block table > tbody [name="image_block_is_target_blank[${value.id}]"]`)
                                .prop('checked', true);
                        }

                        $('.js-select2-image-block-product-category').select2({
                            allowClear: true,
                            theme: "bootstrap",
                            placeholder: '',
                        });

                        $('#image-block-row-no').val(parseInt(value.id) + 1);
                        break;
                    case 'TXT':
                        $('#text-block table > tbody').append(`
                            <tr>
                                <input type="hidden" name="text_block_id[${value.id}]" value="${value.id}">
                                <td>
                                    <input type="text" class="form-control" name="text_block_sort[${value.id}]" value="${sort}" />
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="text_block_texts[${value.id}]" value="${texts}" />
                                </td>
                                <td>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="text_block_image_action[${value.id}]" value="X" />
                                            無連結
                                        </label>
                                    </div>
                                    <div class="form-inline text-nowrap">
                                        <div class="form-group">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="text_block_image_action[${value.id}]" value="U" />
                                                    URL
                                                </label>
                                                <input type="text" class="form-control" name="text_block_target_url[${value.id}]" value="${target_url}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-inline text-nowrap">
                                        <div class="form-group">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="text_block_image_action[${value.id}]" value="C" />
                                                    商品分類頁
                                                </label>
                                                <select class="form-control js-select2-text-block-product-category" name="text_block_target_cate_hierarchy_id[${value.id}]">
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
                                            <input type="checkbox" name="text_block_is_target_blank[${value.id}]" value="enabled" />
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-delete-text"><i class='fa fa-ban'></i> 刪除</button>
                                </td>
                            </tr>
                        `);

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
                        }

                        if (value.target_cate_hierarchy_id) {
                            $(`#text-block table > tbody [name="text_block_target_cate_hierarchy_id[${value.id}]"] option[value="${value.target_cate_hierarchy_id}"]`)
                                .prop('selected', true);
                        }

                        if (value.is_target_blank == 1) {
                            $(`#text-block table > tbody [name="text_block_is_target_blank[${value.id}]"]`)
                                .prop('checked', true);
                        }

                        $('.js-select2-text-block-product-category').select2({
                            allowClear: true,
                            theme: "bootstrap",
                            placeholder: '',
                        });

                        $('#text-block-row-no').val(parseInt(value.id) + 1);
                        break;
                    case 'PRD':
                        if (value.product_id) {
                            $('#tab-product table > tbody').append(`
                                <tr>
                                    <input type="hidden" name="product_block_product_id[${value.id}]" value="${value.id}">
                                    <td>
                                        <input type="text" class="form-control" name="product_block_product_sort[${value.id}]" value="${sort}" />
                                    </td>
                                    <td>
                                        <select class="form-control js-select2-product-block-product" name="product_block_product_product_id[${value.id}]">
                                            <option></option>
                                            ${products_select_option}
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-delete-product-product"><i class='fa fa-ban'></i> 刪除</button>
                                    </td>
                                </tr>
                            `);

                            $(`#tab-product table > tbody [name="product_block_product_product_id[${value.id}]"] option[value="${value.product_id}"]`)
                                .prop('selected', true);

                            $('.js-select2-product-block-product').select2({
                                allowClear: true,
                                theme: "bootstrap",
                                placeholder: '',
                            });

                            $('#product-block-product-row-no').val(parseInt(value.id) + 1);
                        }

                        if (value.web_category_hierarchy_id) {
                            $('#tab-category table > tbody').append(`
                                <tr>
                                    <input type="hidden" name="product_block_category_id[${value.id}]" value="${value.id}">
                                    <td>
                                        <input type="text" class="form-control" name="product_block_category_sort[${value.id}]" value="${sort}" />
                                    </td>
                                    <td>
                                        <select class="form-control js-select2-product-block-category" name="product_block_product_web_category_hierarchy_id[${value.id}]">
                                            <option></option>
                                            ${product_category_select_option}
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-delete-product-category"><i class='fa fa-ban'></i> 刪除</button>
                                    </td>
                                </tr>
                            `);

                            $(`#tab-category table > tbody [name="product_block_product_web_category_hierarchy_id[${value.id}]"] option[value="${value.web_category_hierarchy_id}"]`)
                                .prop('selected', true);

                            $('.js-select2-product-block-category').select2({
                                allowClear: true,
                                theme: "bootstrap",
                                placeholder: '',
                            });

                            $('#product-block-category-row-no').val(parseInt(value.id) + 1);
                        }
                        break;
                }
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

            switch (content.product_assigned_type) {
                // 指定商品
                case 'P':
                    $('#product_assigned_type_product').click();
                    break;
                    // 指定分類
                case 'C':
                    $('#product_assigned_type_category').click();
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

            $("#update-form").validationEngine({
                scroll: false,
            });

            $('#btn-save').on('click', function() {
                $("#update-form").submit();
            });

            $('#btn-cancel').on('click', function() {
                location.href = "{{ route('advertisemsement_launch') }}";
            });
        });
    </script>
@endsection
