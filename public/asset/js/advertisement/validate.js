jQuery.validator.addMethod("greaterThan", function (value, element, params) {
    if (!/Invalid|NaN/.test(new Date(value))) {
        return new Date(value) > new Date(params);
    }

    return isNaN(value) && isNaN(params) ||
        (Number(value) > Number(params));
}, '必須大於 {0}');

jQuery.validator.addMethod("dateGreaterThanNow", function (value, element, params) {
    return new Date(value) > new Date();
}, '必須大於目前時間');

// 加入圖檔區塊欄位驗證
function validateImageBlock(row_no) {
    $(`#image-block table > tbody [name="image_block_sort[${row_no}]"]`).rules("add", {
        required: true,
        digits: true,
    });

    $(`#image-block table > tbody [name="image_block_image_name[${row_no}]"]`).rules("add", {
        required: {
            depends: function (element) {
                return $(`#image-block table > tbody [name="image_block_image_name[${row_no}]"]`).closest('.form-group').find('img').length <= 0;
            }
        },
        accept: "image/*",
        messages: {
            required: '請上傳圖片',
            accept: '請上傳圖片',
        },
    });

    $(`#image-block table > tbody [name="image_block_image_action[${row_no}]"]`).rules("add", {
        required: true,
    });

    $(`#image-block table > tbody [name="image_block_target_url[${row_no}]"]`).rules("add", {
        required: {
            depends: function (element) {
                return $(
                    `#image-block table > tbody [name="image_block_image_action[${row_no}]"][value="U"]`
                ).is(":checked");
            }
        },
        url: true,
    });

    $(`#image-block table > tbody [name="image_block_target_cate_hierarchy_id[${row_no}]"]`).rules("add", {
        required: {
            depends: function (element) {
                return $(
                    `#image-block table > tbody [name="image_block_image_action[${row_no}]"][value="C"]`
                ).is(":checked");
            }
        },
    });
}

// 加入文字區塊欄位驗證
function validateTextBlock(row_no) {
    $(`#text-block table > tbody [name="text_block_sort[${row_no}]"]`).rules("add", {
        required: true,
        digits: true,
    });

    $(`#text-block table > tbody [name="text_block_texts[${row_no}]"]`).rules("add", {
        required: true,
    });

    $(`#text-block table > tbody [name="text_block_image_action[${row_no}]"]`).rules("add", {
        required: true,
    });

    $(`#text-block table > tbody [name="text_block_target_url[${row_no}]"]`).rules("add", {
        required: {
            depends: function (element) {
                return $(
                    `#text-block table > tbody [name="text_block_image_action[${row_no}]"][value="U"]`
                ).is(":checked");
            }
        },
        url: true,
    });

    $(`#text-block table > tbody [name="text_block_target_cate_hierarchy_id[${row_no}]"]`).rules("add", {
        required: {
            depends: function (element) {
                return $(
                    `#text-block table > tbody [name="text_block_image_action[${row_no}]"][value="C"]`
                ).is(":checked");
            }
        },
    });
}

// 加入商品區塊的指定商品欄位驗證
function validateProductBlockProduct(row_no) {
    $(`#tab-product table > tbody [name="product_block_product_sort[${row_no}]"]`).rules("add", {
        required: true,
        digits: true,
    });

    $(`#tab-product table > tbody [name="product_block_product_product_id[${row_no}]"]`).rules("add", {
        required: true,
    });
}

// 加入商品區塊的指定分類欄位驗證
function validateProductBlockCategory(row_no) {
    $(`#tab-category table > tbody [name="product_block_category_sort[${row_no}]"]`).rules("add", {
        required: true,
        digits: true,
    });

    $(`#tab-category table > tbody [name="product_block_product_web_category_hierarchy_id[${row_no}]"]`).rules("add", {
        required: true,
    });
}

// 加入使用者自定義區塊欄位驗證
function validateUserDefinedBlock() {
    $("#slot_color_code").rules("add", {
        required: true,
    });

    $("#slot_icon_name").rules("add", {
        required: {
            depends: function (element) {
                return $("#slot_icon_name").closest('.form-group').find('img').length <= 0;
            }
        },
        accept: "image/*",
        messages: {
            required: '請上傳圖片',
            accept: '請上傳圖片',
        },
    });

    $("#slot_title").rules("add", {
        required: true,
    });
}

// 移除使用者自定義區塊欄位驗證
function removeUserDefinedBlockValidation() {
    $("#slot_color_code").rules("remove");
    $("#slot_color_code").closest(".form-group").removeClass("has-error").find('.help-block').hide();

    $("#slot_icon_name").rules("remove");
    $("#slot_icon_name").closest(".form-group").removeClass("has-error").find('.help-block').hide();

    $("#slot_title").rules("remove");
    $("#slot_title").closest(".form-group").removeClass("has-error").find('.help-block').hide();
}
