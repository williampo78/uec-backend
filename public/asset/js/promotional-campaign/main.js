// 初始化資料
function init() {
    $("#campaign_type").select2();
    $(".select2-supplier-id").select2();
    $(".select2-product-type").select2();

    $("#datetimepicker_start_at").datetimepicker({
        format: "YYYY-MM-DD HH:mm",
        showClear: true,
    });

    $("#datetimepicker_start_at").on("dp.change", function (e) {
        if (e.oldDate === null) {
            $(this)
                .data("DateTimePicker")
                .date(new Date(e.date._d.setHours(0, 0, 0)));
        }
    });

    $("#datetimepicker_end_at").datetimepicker({
        format: "YYYY-MM-DD HH:mm",
        showClear: true,
    });

    $("#datetimepicker_end_at").on("dp.change", function (e) {
        if (e.oldDate === null) {
            $(this)
                .data("DateTimePicker")
                .date(new Date(e.date._d.setHours(23, 59, 59)));
        }
    });

    $("#datetimepicker-prd-modal-start-created-at").datetimepicker({
        format: "YYYY-MM-DD",
        showClear: true,
    });

    $("#datetimepicker-prd-modal-end-created-at").datetimepicker({
        format: "YYYY-MM-DD",
        showClear: true,
    });

    $("#datetimepicker-prd-modal-start-launched-at-start").datetimepicker({
        format: "YYYY-MM-DD",
        showClear: true,
    });

    $("#datetimepicker-prd-modal-start-launched-at-end").datetimepicker({
        format: "YYYY-MM-DD",
        showClear: true,
    });

    $("#datetimepicker-gift-modal-start-created-at").datetimepicker({
        format: "YYYY-MM-DD",
        showClear: true,
    });

    $("#datetimepicker-gift-modal-end-created-at").datetimepicker({
        format: "YYYY-MM-DD",
        showClear: true,
    });

    $("#datetimepicker-gift-modal-start-launched-at-start").datetimepicker({
        format: "YYYY-MM-DD",
        showClear: true,
    });

    $("#datetimepicker-gift-modal-start-launched-at-end").datetimepicker({
        format: "YYYY-MM-DD",
        showClear: true,
    });
}

// 渲染活動類型
function renderCampaignType(campaign_types) {
    // 清空活動類型
    $("#campaign_type").empty().append(`
        <option></option>
    `);

    $.each(campaign_types, function (key, value) {
        // 活動類型加入選項
        $("#campaign_type").append(`
            <option value='${value["code"]}'>${value["description"]}</option>
        `);
    });
}

// 渲染單品modal的供應商
function renderPrdModalSupplier(suppliers) {
    // 清空單品modal的供應商
    $("#prd-modal-supplier-id").empty().append(`
        <option></option>
    `);

    $.each(suppliers, function (key, value) {
        // 單品modal的供應商加入選項
        $("#prd-modal-supplier-id").append(`
            <option value='${value["id"]}'>【${value["display_number"]}】 ${value["name"]}</option>
        `);
    });
}

// 渲染贈品modal的供應商
function renderGiftModalSupplier(suppliers) {
    // 清空贈品modal的供應商
    $("#gift-modal-supplier-id").empty().append(`
        <option></option>
    `);

    $.each(suppliers, function (key, value) {
        // 贈品modal的供應商加入選項
        $("#gift-modal-supplier-id").append(`
            <option value='${value["id"]}'>【${value["display_number"]}】 ${value["name"]}</option>
        `);
    });
}

// 渲染單品modal的商品類型
function renderPrdModalProductType(product_types) {
    // 清空單品modal的商品類型
    $("#prd-modal-product-type").empty();

    $.each(product_types, function (key, value) {
        // 單品modal的商品類型加入選項
        $("#prd-modal-product-type").append(`
            <option value='${key}'>${value}</option>
        `);
    });
}

// 渲染贈品modal的商品類型
function renderGiftModalProductType(product_types) {
    // 清空贈品modal的商品類型
    $("#gift-modal-product-type").empty();

    $.each(product_types, function (key, value) {
        // 贈品modal的商品類型加入選項
        $("#gift-modal-product-type").append(`
            <option value='${key}'>${value}</option>
        `);
    });
}

// 渲染單品的商品清單
function renderPrdProductList(products) {
    let count = 1;

    // 清空單品的商品清單
    $("#prd-product-table > tbody").empty();

    $.each(products, function (id, product) {
        let product_no = product.product_no ? product.product_no : "";
        let product_name = product.product_name ? product.product_name : "";
        let selling_price = product.selling_price ? product.selling_price : "";
        let launched_at = product.launched_at ? product.launched_at : "";
        let launched_status = product.launched_status
            ? product.launched_status
            : "";
        let gross_margin = product.gross_margin ? product.gross_margin : "";

        // 單品清單加入商品
        $("#prd-product-table > tbody").append(`
            <tr data-id="${id}">
                <input type="hidden" name="prd_block_id[${id}]" value="${id}">
                <td>${count++}</td>
                <td>${product_no}</td>
                <td>${product_name}</td>
                <td>${selling_price}</td>
                <td>${launched_at}</td>
                <td>${launched_status}</td>
                <td>${gross_margin}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-delete-prd"><i class="fa-solid fa-trash-can"></i> 刪除</button>
                </td>
            </tr>
        `);
    });

    // 當下時間>=上架時間起，僅開放修改活動名稱、狀態、上架時間訖
    if (new Date() >= new Date($("#start_at").val()) && $("#start_at").is(':disabled')) {
        $(".btn-delete-prd").prop("disabled", true).parent("td").hide();
    }
}

// 渲染單品modal的商品清單
function renderPrdModalProductList(products) {
    let count = 1;

    // 清空單品modal的商品清單
    $("#prd-modal-product-table > tbody").empty();

    // 加入單品modal商品清單
    $.each(products, function (id, product) {
        let product_no = product.product_no ? product.product_no : "";
        let product_name = product.product_name ? product.product_name : "";
        let selling_price = product.selling_price ? product.selling_price : "";
        let launched_at = product.launched_at ? product.launched_at : "";
        let launched_status = product.launched_status
            ? product.launched_status
            : "";
        let gross_margin = product.gross_margin ? product.gross_margin : "";
        let supplier_name = product.supplier_name ? product.supplier_name : "";

        $("#prd-modal-product-table > tbody").append(`
            <tr data-id="${id}">
                <td>${count++}</td>
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
    });
}

// 渲染贈品的商品清單
function renderGiftProductList(products) {
    let count = 1;

    // 儲存贈品清單中輸入欄位的值
    $("#gift-product-table > tbody > tr").each(function () {
        let id = $(this).attr("data-id");

        if (products[id]) {
            products[id]["assigned_qty"] = $(this)
                .find('[name^="gift_block_assigned_qty"]')
                .val();
        }
    });

    // 清空贈品的商品清單
    $("#gift-product-table > tbody").empty();

    $.each(products, function (id, product) {
        let product_no = product.product_no ? product.product_no : "";
        let product_name = product.product_name ? product.product_name : "";
        let assigned_qty = product.assigned_qty ? product.assigned_qty : 1;

        // 贈品清單加入商品
        $("#gift-product-table > tbody").append(`
            <tr data-id="${id}">
                <input type="hidden" name="gift_block_id[${id}]" value="${id}">
                <td>${count++}</td>
                <td>${product_no}</td>
                <td>${product_name}</td>
                <td>
                    <div class="form-group">
                        <input type="number" class="form-control" name="gift_block_assigned_qty[${id}]" value="${assigned_qty}" min="1" />
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-delete-gift"><i class="fa-solid fa-trash-can"></i> 刪除</button>
                </td>
            </tr>
        `);
    });

    // 當下時間>=上架時間起，僅開放修改活動名稱、狀態、上架時間訖
    if (new Date() >= new Date($("#start_at").val()) && $("#start_at").is(':disabled')) {
        $('#gift-product-table > tbody [name^="gift_block_assigned_qty"]').prop(
            "disabled",
            true
        );
        $(".btn-delete-gift").prop("disabled", true).parent("td").hide();
    }

    validateGiftBlock();
}

// 渲染贈品modal的商品清單
function renderGiftModalProductList(products) {
    let count = 1;

    // 清空贈品modal的商品清單
    $("#gift-modal-product-table > tbody").empty();

    // 加入贈品modal商品清單
    $.each(products, function (id, product) {
        let product_no = product.product_no ? product.product_no : "";
        let product_name = product.product_name ? product.product_name : "";
        let selling_price = product.selling_price ? product.selling_price : "";
        let launched_at = product.launched_at ? product.launched_at : "";
        let launched_status = product.launched_status
            ? product.launched_status
            : "";
        let gross_margin = product.gross_margin ? product.gross_margin : "";
        let supplier_name = product.supplier_name ? product.supplier_name : "";

        $("#gift-modal-product-table > tbody").append(`
            <tr data-id="${id}">
                <td>${count++}</td>
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
    });
}

function getProducts(datas) {
    return axios
        .post("/backend/promotional_campaign/ajax/products", datas)
        .then(function (response) {
            return response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
}
