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
    $('#prd-modal-supplier-id').append(supplier_select_options);
    $('#gift-modal-supplier-id').append(supplier_select_options);

    let product_type_select_options = getProductTypeSelectOptions(product_type_option);
    $('#prd-modal-product-type').append(product_type_select_options);
    $('#gift-modal-product-type').append(product_type_select_options);
    $('#prd-modal-product-type option[value="A"]').remove(); // 移除加購品
    $('#prd-modal-product-type option[value="G"]').prop('selected', true); // 預設為贈品
    $('#gift-modal-product-type option[value="A"]').remove(); // 移除加購品
    $('#gift-modal-product-type option[value="G"]').prop('selected', true); // 預設為贈品

    $('#campaign_type').select2({
        allowClear: true,
        theme: "bootstrap",
        placeholder: '',
    });

    $('.select2-supplier-id').select2({
        allowClear: true,
        theme: "bootstrap",
        placeholder: '',
    });

    $('.select2-product-type').select2({
        theme: "bootstrap",
    });


    $('#datetimepicker_start_at').datetimepicker({
        format: 'YYYY-MM-DD HH:mm',
        showClear: true,
    });

    $('#datetimepicker_start_at').on('dp.change', function (e) {
        if (e.oldDate === null) {
            $(this).data('DateTimePicker').date(new Date(e.date._d.setHours(00, 00, 00)));
        }
    });

    $('#datetimepicker_end_at').datetimepicker({
        format: 'YYYY-MM-DD HH:mm',
        showClear: true,
    });

    $('#datetimepicker_end_at').on('dp.change', function (e) {
        if (e.oldDate === null) {
            $(this).data('DateTimePicker').date(new Date(e.date._d.setHours(23, 59, 59)));
        }
    });

    $('#datetimepicker-prd-modal-start-created-at').datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
    });

    $('#datetimepicker-prd-modal-end-created-at').datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
    });

    $('#datetimepicker-prd-modal-start-launched-at').datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
    });

    $('#datetimepicker-prd-modal-end-launched-at').datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
    });

    $('#datetimepicker-gift-modal-start-created-at').datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
    });

    $('#datetimepicker-gift-modal-end-created-at').datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
    });

    $('#datetimepicker-gift-modal-start-launched-at').datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
    });

    $('#datetimepicker-gift-modal-end-launched-at').datetimepicker({
        format: 'YYYY-MM-DD',
        showClear: true,
    });
}

// 取得活動類型下拉選項
function getCampaignTypeSelectOptions(datas) {
    let options = '';

    $.each(datas, function (key, value) {
        options += `
            <option value='${value['code']}'>${value['description']}</option>
        `;
    });

    return options;
}

// 取得供應商下拉選項
function getSupplierSelectOptions(datas) {
    let options = '';

    $.each(datas, function (key, value) {
        options += `
            <option value='${value['id']}'>【${value['display_number']}】 ${value['name']}</option>
        `;
    });

    return options;
}

// 取得商品類型下拉選項
function getProductTypeSelectOptions(datas) {
    let options = '';

    $.each(datas, function (key, value) {
        options += `
            <option value='${key}'>${value}</option>
        `;
    });

    return options;
}

// 渲染單品的商品清單
function renderPrdProductList(products) {
    let count = 1;

    // 清空單品的商品清單
    $('#prd-product-table > tbody').empty();

    $.each(products, function (id, product) {
        let product_no = product.product_no ? product.product_no : '';
        let product_name = product.product_name ? product.product_name : '';
        let selling_price = product.selling_price ? product.selling_price : '';
        let launched_at = product.launched_at ? product.launched_at : '';
        let launched_status = product.launched_status ? product.launched_status : '';
        let gross_margin = product.gross_margin ? product.gross_margin : '';

        // 單品清單加入商品
        $('#prd-product-table > tbody').append(`
            <tr data-id="${id}">
                <input type="hidden" name="prd_block_id[${id}]" value="${id}">
                <td>${count}</td>
                <td>${product_no}</td>
                <td>${product_name}</td>
                <td>${selling_price}</td>
                <td>${launched_at}</td>
                <td>${launched_status}</td>
                <td>${gross_margin}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-delete-prd"><i class='fa fa-ban'></i> 刪除</button>
                </td>
            </tr>
        `);

        count++;
    });
}

// 渲染單品modal的商品清單
function renderPrdModalProductList(products) {
    let count = 1;

    // 清空單品modal的商品清單
    $('#prd-modal-product-table > tbody').empty();

    // 加入單品modal商品清單
    $.each(products, function (id, product) {
        let product_no = product.product_no ? product.product_no : '';
        let product_name = product.product_name ? product.product_name : '';
        let selling_price = product.selling_price ? product.selling_price : '';
        let launched_at = product.launched_at ? product.launched_at : '';
        let launched_status = product.launched_status ? product.launched_status : '';
        let gross_margin = product.gross_margin ? product.gross_margin : '';
        let supplier_name = product.supplier_name ? product.supplier_name : '';

        $('#prd-modal-product-table > tbody').append(`
            <tr data-id="${id}">
                <td>${count}</td>
                <td class="text-center">
                    <input type="checkbox" name="choose_product" style="width: 20px;height: 20px;cursor: pointer;" />
                </td>
                <td>${product_no}</td>
                <td>${product_name}</td>
                <td>${selling_price}</td>
                <td>${launched_at}</td>
                <td>${launched_status}</td>
                <td>${gross_margin}</td>
                <td>${supplier_name}</td>
            </tr>
        `);

        count++;
    });
}

// 渲染贈品的商品清單
function renderGiftProductList(products) {
    let count = 1;

    // 儲存贈品清單中輸入欄位的值
    $('#gift-product-table > tbody > tr').each(function () {
        let id = $(this).attr('data-id');

        if (products[id]) {
            products[id]['assigned_qty'] = $(this).find('[name^="gift_block_assigned_qty"]').val();
        }
    });

    // 清空贈品的商品清單
    $('#gift-product-table > tbody').empty();

    $.each(products, function (id, product) {
        let product_no = product.product_no ? product.product_no : '';
        let product_name = product.product_name ? product.product_name : '';
        let assigned_qty = product.assigned_qty ? product.assigned_qty : 1;

        // 贈品清單加入商品
        $('#gift-product-table > tbody').append(`
            <tr data-id="${id}">
                <input type="hidden" name="gift_block_id[${id}]" value="${id}">
                <td>${count}</td>
                <td>${product_no}</td>
                <td>${product_name}</td>
                <td>
                    <div class="form-group">
                        <input type="number" class="form-control" name="gift_block_assigned_qty[${id}]" value="${assigned_qty}" min="1" />
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-delete-gift"><i class='fa fa-ban'></i> 刪除</button>
                </td>
            </tr>
        `);

        count++;
    });

    validateGiftBlock();
}

// 渲染贈品modal的商品清單
function renderGiftModalProductList(products) {
    let count = 1;

    // 清空贈品modal的商品清單
    $('#gift-modal-product-table > tbody').empty();

    // 加入贈品modal商品清單
    $.each(products, function (id, product) {
        let product_no = product.product_no ? product.product_no : '';
        let product_name = product.product_name ? product.product_name : '';
        let selling_price = product.selling_price ? product.selling_price : '';
        let launched_at = product.launched_at ? product.launched_at : '';
        let launched_status = product.launched_status ? product.launched_status : '';
        let gross_margin = product.gross_margin ? product.gross_margin : '';
        let supplier_name = product.supplier_name ? product.supplier_name : '';

        $('#gift-modal-product-table > tbody').append(`
            <tr data-id="${id}">
                <td>${count}</td>
                <td class="text-center">
                    <input type="checkbox" name="choose_product" style="width: 20px;height: 20px;cursor: pointer;" />
                </td>
                <td>${product_no}</td>
                <td>${product_name}</td>
                <td>${selling_price}</td>
                <td>${launched_at}</td>
                <td>${launched_status}</td>
                <td>${gross_margin}</td>
                <td>${supplier_name}</td>
            </tr>
        `);

        count++;
    });
}

function getProducts(query_datas) {
    return axios.post('/backend/promotional_campaign/ajax/products', {
        supplier_id: query_datas.supplier_id,
        product_no: query_datas.product_no,
        product_name: query_datas.product_name,
        selling_price_min: query_datas.selling_price_min,
        selling_price_max: query_datas.selling_price_max,
        start_created_at: query_datas.start_created_at,
        end_created_at: query_datas.end_created_at,
        start_launched_at: query_datas.start_launched_at,
        end_launched_at: query_datas.end_launched_at,
        product_type: query_datas.product_type,
        limit: query_datas.limit,
        exist_products: query_datas.exist_products,
    })
        .then(function (response) {
            return response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
}
