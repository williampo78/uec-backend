// 初始化資料
function init(datas = {}) {
    let campaign_types = datas.campaign_types ? datas.campaign_types : '';
    let suppliers = datas.suppliers ? datas.suppliers : '';
    let product_type_option = datas.product_type_option ? datas.product_type_option : '';

    if (campaign_types) {
        let campaign_type_select_options = getCampaignTypeSelectOptions(campaign_types);
        $('#campaign_type').append(campaign_type_select_options);
    }

    let supplier_select_options = getSupplierSelectOptions(suppliers);
    $('#supplier_id').append(supplier_select_options);

    let product_type_select_options = getProductTypeSelectOptions(product_type_option);
    $('#product_type').append(product_type_select_options);


    $('.js-select2-campaign-type').select2({
        allowClear: true,
        theme: "bootstrap",
        placeholder: '',
    });

    $('.js-select2-supplier-id').select2({
        allowClear: true,
        theme: "bootstrap",
        placeholder: '',
    });

    $('.js-select2-product-type').select2({
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

    $('#datetimepicker_start_created_at').datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
    });

    $('#datetimepicker_end_created_at').datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
    });

    $('#datetimepicker_start_launched_at').datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
    });

    $('#datetimepicker_end_launched_at').datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
    });

    // 新增圖檔
    // $('#btn-new-image').on('click', function () {
    //     addImageBlock(product_category_select_options);
    // });

    // 刪除圖檔
    // $(document).on('click', '.btn-delete-image', function () {
    //     if (confirm('確定要刪除嗎?')) {
    //         $(this).parents('tr').remove();
    //     }
    // });
}

// 取得活動類型下拉選項
function getCampaignTypeSelectOptions(datas = []) {
    let options = '';

    $.each(datas, function (key, value) {
        options += `
            <option value='${value['code']}'>${value['description']}</option>
        `;
    });

    return options;
}

// 取得供應商下拉選項
function getSupplierSelectOptions(datas = []) {
    let options = '';

    $.each(datas, function (key, value) {
        options += `
            <option value='${value['id']}'>【${value['display_number']}】 ${value['name']}</option>
        `;
    });

    return options;
}

// 取得商品類型下拉選項
function getProductTypeSelectOptions(datas = []) {
    let options = '';

    $.each(datas, function (key, value) {
        options += `
            <option value='${key}'>${value}</option>
        `;
    });

    return options;
}
