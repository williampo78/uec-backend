
// 初始化資料
function init(datas = {}) {
    let ad_slot_select_options = datas.ad_slot_select_options ? datas.ad_slot_select_options : '';
    let product_category_select_options = datas.product_category_select_options ? datas.product_category_select_options : '';
    let product_select_options = datas.product_select_options ? datas.product_select_options : '';

    if (ad_slot_select_options) {
        $('#slot_id').append(ad_slot_select_options).prev('label').append(' <span style="color:red;">*</span>');
    }

    $('#start_at').closest('.row').prev('label').append(' <span style="color:red;">*</span>');
    $('#active_enabled').closest('.form-group').children('label').append(
        ' <span style="color:red;">*</span>');

    $('#image-block table > thead th').filter(function (i) {
        return $.inArray(i, [0, 1, 5]) > -1;
    }).append(' <span style="color:red;">*</span>');

    $('#text-block table > thead th').filter(function (i) {
        return $.inArray(i, [0, 1, 2]) > -1;
    }).append(' <span style="color:red;">*</span>');

    $('#tab-product table > thead th').filter(function (i) {
        return $.inArray(i, [0, 1]) > -1;
    }).append(' <span style="color:red;">*</span>');

    $('#tab-category table > thead th').filter(function (i) {
        return $.inArray(i, [0, 1]) > -1;
    }).append(' <span style="color:red;">*</span>');

    $('.js-select2-slot-id').select2({
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

    // 點擊指定商品radio button
    $('#product_assigned_type_product').on('click', function () {
        $('#product-block-tab a[href="#tab-product"]').tab('show');
    });

    // 點擊指定分類radio button
    $('#product_assigned_type_category').on('click', function () {
        $('#product-block-tab a[href="#tab-category"]').tab('show');
    });

    // 點擊商品tab
    $('#product-block-tab a[href="#tab-product"]').on('show.bs.tab', function (e) {
        $('#product_assigned_type_product').prop('checked', true);
    });

    // 點擊分類tab
    $('#product-block-tab a[href="#tab-category"]').on('show.bs.tab', function (e) {
        $('#product_assigned_type_category').prop('checked', true);
    });

    // 新增圖檔
    $('#btn-new-image').on('click', function () {
        addImageBlock(product_category_select_options);
    });

    // 刪除圖檔
    $(document).on('click', '.btn-delete-image', function () {
        if (confirm('確定要刪除嗎?')) {
            $(this).parents('tr').remove();
        }
    });

    // 新增文字
    $('#btn-new-text').on('click', function () {
        addTextBlock(product_category_select_options);
    });

    // 刪除文字
    $(document).on('click', '.btn-delete-text', function () {
        if (confirm('確定要刪除嗎?')) {
            $(this).parents('tr').remove();
        }
    });

    // 新增商品的指定商品
    $('#btn-new-product-product').on('click', function () {
        addProductBlockProduct(product_select_options);
    });

    // 刪除商品的指定商品
    $(document).on('click', '.btn-delete-product-product', function () {
        if (confirm('確定要刪除嗎?')) {
            $(this).parents('tr').remove();
        }
    });

    // 新增商品的指定分類
    $('#btn-new-product-category').on('click', function () {
        addProductBlockCategory(product_category_select_options);
    });

    // 刪除商品的指定分類
    $(document).on('click', '.btn-delete-product-category', function () {
        if (confirm('確定要刪除嗎?')) {
            $(this).parents('tr').remove();
        }
    });
}

// 取得版位下拉選項
function getAdSlotSelectOptions(datas = []) {
    let options = '';

    $.each(datas, function (key, value) {
        options += `
            <option value='${value['id']}' data-is-user-defined="${value['is_user_defined']}" data-slot-type="${value['slot_type']}">【${value['slot_code']}】${value['slot_desc']}</option>
        `;
    });

    return options;
}

// 取得商品分類下拉選項
function getProductCategorySelectOptions(datas = []) {
    let options = '';

    $.each(datas, function (key, value) {
        options += `
            <option value='${value['id']}'>${value['name']}</option>
        `;
    });

    return options;
}

// 取得商品下拉選項
function getProductSelectOptions(datas = []) {
    let options = '';

    $.each(datas, function (key, value) {
        options += `
            <option value='${value['id']}'>${value['product_no']} ${value['product_name']}</option>
        `;
    });

    return options;
}

function addImageBlock(product_category_select_options = '', datas = {}) {
    let image_block_row_no = datas.id ? datas.id : $('#image-block-row-no').val();
    let image_block_id = datas.id ? datas.id : 'new';
    let sort = datas.sort ? datas.sort : '';
    let image_name_url = datas.image_name_url ? `<img src="${datas.image_name_url}" class="img-responsive" width="400" height="400" />` : '';
    let image_alt = datas.image_alt ? datas.image_alt : '';
    let image_title = datas.image_title ? datas.image_title : '';
    let image_abstract = datas.image_abstract ? datas.image_abstract : '';
    let target_url = datas.target_url ? datas.target_url : '';

    $('#image-block table > tbody').append(`
        <tr>
            <input type="hidden" name="image_block_id[${image_block_row_no}]" value="${image_block_id}">
            <td>
                <div class="form-group">
                    <input type="number" class="form-control" name="image_block_sort[${image_block_row_no}]" value="${sort}" />
                </div>
            </td>
            <td>
                <div class="form-group">
                    ${image_name_url}
                    <input type="file" name="image_block_image_name[${image_block_row_no}]" value="" />
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" class="form-control" name="image_block_image_alt[${image_block_row_no}]" value="${image_alt}" />
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" class="form-control" name="image_block_image_title[${image_block_row_no}]" value="${image_title}" />
                </div>
            </td>
            <td>
                <div class="form-group">
                    <textarea class="form-control" rows="3" name="image_block_image_abstract[${image_block_row_no}]">${image_abstract}</textarea>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <div class="radio">
                        <label>
                            <input type="radio" name="image_block_image_action[${image_block_row_no}]" value="X" checked />
                            無連結
                        </label>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="image_block_image_action[${image_block_row_no}]" value="U" />
                                    URL
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group">
                                <input type="text" class="form-control" name="image_block_target_url[${image_block_row_no}]" value="${target_url}" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="image_block_image_action[${image_block_row_no}]" value="C" />
                                    商品分類頁
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group">
                                <select class="form-control js-select2-image-block-product-category" name="image_block_target_cate_hierarchy_id[${image_block_row_no}]">
                                    <option></option>
                                    ${product_category_select_options}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="image_block_is_target_blank[${image_block_row_no}]" value="enabled" style="width: 20px;height: 20px;cursor: pointer;" />
                        </label>
                    </div>
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

    validateImageBlock(image_block_row_no);
}

function addTextBlock(product_category_select_options, datas = {}) {
    let text_block_row_no = datas.id ? datas.id : $('#text-block-row-no').val();
    let text_block_id = datas.id ? datas.id : 'new';
    let sort = datas.sort ? datas.sort : '';
    let texts = datas.texts ? datas.texts : '';
    let target_url = datas.target_url ? datas.target_url : '';

    $('#text-block table > tbody').append(`
        <tr>
            <input type="hidden" name="text_block_id[${text_block_row_no}]" value="${text_block_id}">
            <td>
                <div class="form-group">
                    <input type="number" class="form-control" name="text_block_sort[${text_block_row_no}]" value="${sort}" />
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="text" class="form-control" name="text_block_texts[${text_block_row_no}]" value="${texts}" />
                </div>
            </td>
            <td>
                <div class="form-group">
                    <div class="radio">
                        <label>
                            <input type="radio" name="text_block_image_action[${text_block_row_no}]" value="X" checked />
                            無連結
                        </label>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="text_block_image_action[${text_block_row_no}]" value="U" />
                                    URL
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group">
                                <input type="text" class="form-control" name="text_block_target_url[${text_block_row_no}]" value="${target_url}" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="text_block_image_action[${text_block_row_no}]" value="C" />
                                    商品分類頁
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group">
                                <select class="form-control js-select2-text-block-product-category" name="text_block_target_cate_hierarchy_id[${text_block_row_no}]">
                                    <option></option>
                                    ${product_category_select_options}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="text_block_is_target_blank[${text_block_row_no}]" value="enabled" style="width: 20px;height: 20px;cursor: pointer;" />
                        </label>
                    </div>
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

    validateTextBlock(text_block_row_no);
}

function addProductBlockProduct(product_select_options, datas = {}) {
    let product_block_product_row_no = datas.id ? datas.id : $('#product-block-product-row-no').val();
    let product_block_product_id = datas.id ? datas.id : 'new';
    let sort = datas.sort ? datas.sort : '';

    $('#tab-product table > tbody').append(`
        <tr>
            <input type="hidden" name="product_block_product_id[${product_block_product_row_no}]" value="${product_block_product_id}">
            <td>
                <div class="form-group">
                    <input type="number" class="form-control" name="product_block_product_sort[${product_block_product_row_no}]" value="${sort}" />
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select class="form-control js-select2-product-block-product" name="product_block_product_product_id[${product_block_product_row_no}]">
                        <option></option>
                        ${product_select_options}
                    </select>
                </div>
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

    validateProductBlockProduct(product_block_product_row_no);
}

function addProductBlockCategory(product_category_select_options, datas = {}) {
    let product_block_category_row_no = datas.id ? datas.id : $('#product-block-category-row-no').val();
    let product_block_category_id = datas.id ? datas.id : 'new';
    let sort = datas.sort ? datas.sort : '';

    $('#tab-category table > tbody').append(`
        <tr>
            <input type="hidden" name="product_block_category_id[${product_block_category_row_no}]" value="${product_block_category_id}">
            <td>
                <div class="form-group">
                    <input type="number" class="form-control" name="product_block_category_sort[${product_block_category_row_no}]" value="${sort}" />
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select class="form-control js-select2-product-block-category" name="product_block_product_web_category_hierarchy_id[${product_block_category_row_no}]">
                        <option></option>
                        ${product_category_select_options}
                    </select>
                </div>
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

    validateProductBlockCategory(product_block_category_row_no);
}
