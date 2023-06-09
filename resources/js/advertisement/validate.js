// 加入圖檔區塊欄位驗證
export let validateImageBlock = (row_no) => {
    $(`#image-block table > tbody [name="image_block_sort[${row_no}]"]`).rules("add", {
        required: true,
        digits: true,
        unique: ".unique_image_block_sort",
    });

    $(`#image-block table > tbody [name="image_block_image_name[${row_no}]"]`).rules("add", {
        required: {
            depends: function (element) {
                return !$(`#image-block table > tbody [name="image_block_image_name[${row_no}]"]`).prop("disabled");
            }
        },
        accept: "image/*",
        filesize: [2, 'MB'],
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
    $(`#image-block table > tbody [name="target_campaign_brief[${row_no}]"]`).rules("add", {
        required: {
            depends: function (element) {
                return $(
                    `#image-block table > tbody [name="image_block_image_action[${row_no}]"][value="M"]`
                ).is(":checked");
            }
        },
    });
}

// 加入文字區塊欄位驗證
export let validateTextBlock = (row_no) => {
    $(`#text-block table > tbody [name="text_block_sort[${row_no}]"]`).rules("add", {
        required: true,
        digits: true,
        unique: ".unique_text_block_sort",
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
    $(`#text-block table > tbody [name="target_campaign_brief[${row_no}]"]`).rules("add", {
        required: {
            depends: function (element) {
                return $(
                    `#text-block table > tbody [name="text_block_image_action[${row_no}]"][value="M"]`
                ).is(":checked");
            }
        },
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
export let validateProductBlockProduct = (row_no) => {
    $(`#tab-product table > tbody [name="product_block_product_sort[${row_no}]"]`).rules("add", {
        required: true,
        digits: true,
        unique: ".unique_product_block_product_sort",
    });

    $(`#tab-product table > tbody [name="product_block_product_product_id[${row_no}]"]`).rules("add", {
        required: true,
        unique: ".unique_product_block_product_product_id",
    });
}

// 加入商品區塊的指定分類欄位驗證
export let validateProductBlockCategory = (row_no) => {
    $(`#tab-category table > tbody [name="product_block_category_sort[${row_no}]"]`).rules("add", {
        required: true,
        digits: true,
        unique: ".unique_product_block_category_sort",
    });

    $(`#tab-category table > tbody [name="product_block_product_web_category_hierarchy_id[${row_no}]"]`).rules("add", {
        required: true,
        unique: ".unique_product_block_product_web_category_hierarchy_id",
    });
}

// 加入版位主色欄位驗證
export let validateSlotColorCode = () => {
    $("#slot_color_code").rules("add", {
        required: true,
    });
}
//加入版位標題顏色欄位驗證
export let validateTitleColorCode = () => {
    $("#slot_title_color").rules("add", {
        required: true,
    });
}

// 加入版位icon欄位驗證
export let validateSlotIconName = () => {
    $("#slot_icon_name").rules("add", {
        required: {
            depends: function (element) {
                return !$("#slot_icon_name").prop("disabled");
            }
        },
        accept: "image/*",
        filesize: [2, 'MB'],
        messages: {
            required: '請上傳圖片',
            accept: '請上傳圖片',
        },
    });
}

// 加入版位標題欄位驗證
export let validateSlotTitle = () => {
    $("#slot_title").rules("add", {
        required: true,
    });
}

// 移除版位主色欄位驗證
export let removeSlotColorCodeValidation = () => {
    $("#slot_color_code").rules("remove");
    $("#slot_color_code").closest(".form-group").removeClass("has-error").find('.help-block').hide();
}

// 移除版位標題顏色欄位驗證
export let removeTitleColorCodeValidation = () => {
    $("#slot_title_color").rules("remove");
    $("#slot_title_color").closest(".form-group").removeClass("has-error").find('.help-block').hide();
}
// 移除版位icon欄位驗證
export let removeSlotIconNameValidation = () => {
    $("#slot_icon_name").rules("remove");
    $("#slot_icon_name").closest(".form-group").removeClass("has-error").find('.help-block').hide();
}

// 移除版位標題欄位驗證
export let removeSlotTitleValidation = () => {
    $("#slot_title").rules("remove");
    $("#slot_title").closest(".form-group").removeClass("has-error").find('.help-block').hide();
}
