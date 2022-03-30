import * as validate from "./validate";

// 初始化資料
window.init = (datas = {}) => {
    let ad_slot_select_options = datas.ad_slot_select_options
        ? datas.ad_slot_select_options
        : "";
    let product_category_select_options = datas.product_category_select_options
        ? datas.product_category_select_options
        : "";
    let product_select_options = datas.product_select_options
        ? datas.product_select_options
        : "";

    if (ad_slot_select_options) {
        $("#slot_id").append(ad_slot_select_options);
    } else {
        $("#slot_id").prev("label").find("span").remove();
    }

    $(".js-select2-slot-id").select2();

    let start_at_flatpickr = flatpickr("#start_at_flatpickr", {
        dateFormat: "Y-m-d H:i:S",
        maxDate: $("#end_at").val(),
        enableTime: true,
        enableSeconds: true,
        defaultHour: 0,
        defaultMinute: 0,
        defaultSeconds: 0,
        onChange: function (selectedDates, dateStr, instance) {
            end_at_flatpickr.set('minDate', dateStr);

            if (!end_at_flatpickr.input.value) {
                end_at_flatpickr.hourElement.value = 23;
                end_at_flatpickr.minuteElement.value = 59;
                end_at_flatpickr.secondElement.value = 59;
            }
        },
    });

    let end_at_flatpickr = flatpickr("#end_at_flatpickr", {
        dateFormat: "Y-m-d H:i:S",
        minDate: $("#start_at").val(),
        enableTime: true,
        enableSeconds: true,
        defaultHour: 23,
        defaultMinute: 59,
        defaultSeconds: 59,
        onChange: function (selectedDates, dateStr, instance) {
            start_at_flatpickr.set('maxDate', dateStr);
        },
    });

    $(".colorpicker").colorpicker({
        format: "hex",
        align: "left",
        customClass: "colorpicker-2x",
        sliders: {
            saturation: {
                maxLeft: 200,
                maxTop: 200,
            },
            hue: {
                maxTop: 200,
            },
            alpha: {
                maxTop: 200,
            },
        },
    });

    // 點擊指定商品radio button
    $("#product_assigned_type_product").on("click", function () {
        $('#product-block-tab a[href="#tab-product"]').tab("show");
    });

    // 點擊指定分類radio button
    $("#product_assigned_type_category").on("click", function () {
        $('#product-block-tab a[href="#tab-category"]').tab("show");
    });

    // 點擊商品tab
    $('#product-block-tab a[href="#tab-product"]').on(
        "show.bs.tab",
        function (e) {
            $("#product_assigned_type_product").prop("checked", true);
        }
    );

    // 點擊分類tab
    $('#product-block-tab a[href="#tab-category"]').on(
        "show.bs.tab",
        function (e) {
            $("#product_assigned_type_category").prop("checked", true);
        }
    );

    // 新增圖檔
    $("#btn-new-image").on("click", function () {
        let datas = {
            id: $("#image-block-row-no").val(),
        };

        addImageBlock(product_category_select_options, datas);
    });

    // 刪除圖檔
    $(document).on("click", ".btn-delete-image", function () {
        if (confirm("確定要刪除嗎?")) {
            $(this).parents("tr").remove();
        }
    });

    // 新增文字
    $("#btn-new-text").on("click", function () {
        let datas = {
            id: $("#text-block-row-no").val(),
        };

        addTextBlock(product_category_select_options, datas);
    });

    // 刪除文字
    $(document).on("click", ".btn-delete-text", function () {
        if (confirm("確定要刪除嗎?")) {
            $(this).parents("tr").remove();
        }
    });

    // 新增商品的指定商品
    $("#btn-new-product-product").on("click", function () {
        let datas = {
            id: $("#product-block-product-row-no").val(),
        };

        addProductBlockProduct(product_select_options, datas);

        // 編輯才做
        if (!ad_slot_select_options) {
            // 當指定商品有項目時，隱藏指定分類的選項
            if ($(`#tab-product table > tbody > tr`).length > 0) {
                $("#product_assigned_type_category").parent("label").hide();
                $('#product-block-tab a[href="#tab-category"]')
                    .parent("li")
                    .hide();
            }
        }
    });

    // 刪除商品的指定商品
    $(document).on("click", ".btn-delete-product-product", function () {
        if (confirm("確定要刪除嗎?")) {
            $(this).parents("tr").remove();

            // 編輯才做
            if (!ad_slot_select_options) {
                // 當指定商品沒有任何項目時，顯示指定分類的選項
                if ($(`#tab-product table > tbody > tr`).length < 1) {
                    $("#product_assigned_type_category").parent("label").show();
                    $('#product-block-tab a[href="#tab-category"]')
                        .parent("li")
                        .show();

                    // 當指定分類有項目時，隱藏指定商品的選項
                    if ($(`#tab-category table > tbody > tr`).length > 0) {
                        $("#product_assigned_type_product")
                            .parent("label")
                            .hide();
                        $('#product-block-tab a[href="#tab-product"]')
                            .parent("li")
                            .hide();
                        $("#product_assigned_type_category").click();
                    }
                }
            }
        }
    });

    // 新增商品的指定分類
    $("#btn-new-product-category").on("click", function () {
        let datas = {
            id: $("#product-block-category-row-no").val(),
        };

        addProductBlockCategory(product_category_select_options, datas);

        // 編輯才做
        if (!ad_slot_select_options) {
            // 當指定分類有項目時，隱藏指定商品的選項
            if ($(`#tab-category table > tbody > tr`).length > 0) {
                $("#product_assigned_type_product").parent("label").hide();
                $('#product-block-tab a[href="#tab-product"]')
                    .parent("li")
                    .hide();
            }
        }
    });

    // 刪除商品的指定分類
    $(document).on("click", ".btn-delete-product-category", function () {
        if (confirm("確定要刪除嗎?")) {
            $(this).parents("tr").remove();

            // 編輯才做
            if (!ad_slot_select_options) {
                // 當指定分類沒有任何項目時，顯示指定商品的選項
                if ($(`#tab-category table > tbody > tr`).length < 1) {
                    $("#product_assigned_type_product").parent("label").show();
                    $('#product-block-tab a[href="#tab-product"]')
                        .parent("li")
                        .show();

                    // 當指定商品有項目時，隱藏指定分類的選項
                    if ($(`#tab-product table > tbody > tr`).length > 0) {
                        $("#product_assigned_type_category")
                            .parent("label")
                            .hide();
                        $('#product-block-tab a[href="#tab-category"]')
                            .parent("li")
                            .hide();
                        $("#product_assigned_type_product").click();
                    }
                }
            }
        }
    });

    // 切換圖檔連結內容選項時，清除其他選項的值
    $(document).on("click", '[name^="image_block_image_action"]', function () {
        let image_action = $(this).val();
        let form_group_element = $(this).closest(".form-group");

        switch (image_action) {
            case "X":
                form_group_element
                    .find('[name^="image_block_target_url"]')
                    .val("");
                form_group_element
                    .find('[name^="image_block_target_cate_hierarchy_id"]')
                    .val("")
                    .trigger("change");
                break;

            case "U":
                form_group_element
                    .find('[name^="image_block_target_cate_hierarchy_id"]')
                    .val("")
                    .trigger("change");
                break;

            case "C":
                form_group_element
                    .find('[name^="image_block_target_url"]')
                    .val("");
                break;
        }
    });

    // 切換文字連結內容選項時，清除其他選項的值
    $(document).on("click", '[name^="text_block_image_action"]', function () {
        let image_action = $(this).val();
        let form_group_element = $(this).closest(".form-group");

        switch (image_action) {
            case "X":
                form_group_element
                    .find('[name^="text_block_target_url"]')
                    .val("");
                form_group_element
                    .find('[name^="text_block_target_cate_hierarchy_id"]')
                    .val("")
                    .trigger("change");
                break;

            case "U":
                form_group_element
                    .find('[name^="text_block_target_cate_hierarchy_id"]')
                    .val("")
                    .trigger("change");
                break;

            case "C":
                form_group_element
                    .find('[name^="text_block_target_url"]')
                    .val("");
                break;
        }
    });

    // 選擇版位icon檔案
    $("#slot_icon_name").on("change", function () {
        const file = this.files[0];

        if (file) {
            $("#img_slot_icon_name").attr("src", URL.createObjectURL(file));
            $("#img_slot_icon_name, #btn-delete-slot-icon-name").show();
        }
    });

    // 刪除版位icon
    $("#btn-delete-slot-icon-name").on("click", function () {
        $("#img_slot_icon_name").attr("src", "");
        $("#img_slot_icon_name, #btn-delete-slot-icon-name").hide();
        $("#slot_icon_name").val("").show();
    });

    // 選擇圖片區的圖片檔案
    $(document).on("change", ".image_block_image_name", function () {
        const file = this.files[0];
        let photo_width = $('#slot_id').find('option:selected').attr('data-photo-width');
        let photo_height = $('#slot_id').find('option:selected').attr('data-photo-height');
        let vm = $(this);
        if (file) {
            if (photo_width && photo_height) { //顯示選擇照片的尺寸提醒
                var img;
                img = new Image();
                var objectUrl = URL.createObjectURL(file);
                img.onload = function () {
                    if (this.width !== parseInt(photo_width) || this.height !== parseInt(photo_height)) {
                        let show_text = '上傳尺寸 ' + this.width + '*' + this.height + ' 非預期，存檔後系統會自動壓縮成制式尺寸！';
                        vm.siblings('.select-img-size-box').show();
                        vm.siblings('.select-img-size-box').find('.select-img-size-text').text(show_text);
                    } else {
                        vm.siblings('.select-img-size-box').hide();
                        vm.siblings('.select-img-size-box').find('.select-img-size-text').text('');
                    }
                    URL.revokeObjectURL(objectUrl);
                };

                img.src = objectUrl;
            }

            $(this).siblings('.img_image_block_image_name').attr("src", URL.createObjectURL(file));
            $(this).siblings(".img_image_block_image_name, .btn-delete-image-block-image-name").show();
            $(this)
                .siblings(".img_image_block_image_name")
                .attr("src", URL.createObjectURL(file));
            $(this)
                .siblings(
                    ".img_image_block_image_name, .btn-delete-image-block-image-name"
                )
                .show();
        }
    });

    // 刪除圖片區的圖片
    $(document).on("click", ".btn-delete-image-block-image-name", function () {
        $(this).siblings(".img_image_block_image_name").attr("src", "").hide();
        $(this).siblings(".select-img-size-box").hide();
        $(this).siblings(".select-img-size-box").find('.select-img-size-text').text('');
        $(this).hide();
        $(this).siblings(".image_block_image_name").val("").show();
    });
};

// 取得版位下拉選項
window.getAdSlotSelectOptions = (datas = []) => {
    let options = "";
    $.each(datas, function (key, value) {
        options += `
            <option value='${value["id"]}'
            data-is-user-defined="${value["is_user_defined"]}"
            data-slot-type="${value["slot_type"]}"
            data-photo-width="${value["photo_width"]}"
            data-photo-height="${value["photo_height"]}"
            >【${value["slot_code"]}】${value["slot_desc"]}
            </option>
        `;
    });

    return options;
};

// 取得商品分類下拉選項
window.getProductCategorySelectOptions = (datas = []) => {
    let options = "";

    $.each(datas, function (key, value) {
        options += `
            <option value='${value["id"]}'>${value["name"]}</option>
        `;
    });

    return options;
};

// 取得商品下拉選項
window.getProductSelectOptions = (datas = []) => {
    let options = "";

    $.each(datas, function (key, value) {
        options += `
            <option value='${value["id"]}'>${value["product_no"]} ${value["product_name"]}</option>
        `;
    });

    return options;
};

window.addImageBlock = (product_category_select_options = "", datas = {}) => {
    let image_block_row_no = datas.id;
    let sort = datas.sort != null ? datas.sort : "";
    let image_name_url = datas.image_name_url ? datas.image_name_url : "";
    let image_alt = datas.image_alt ? datas.image_alt : "";
    let image_title = datas.image_title ? datas.image_title : "";
    let image_abstract = datas.image_abstract ? datas.image_abstract : "";
    let target_url = datas.target_url ? datas.target_url : "";

    $("#image-block table > tbody").append(`
        <tr>
            <input type="hidden" name="image_block_id[${image_block_row_no}]" value="${image_block_row_no}">
            <td class="sort">
                <div class="form-group">
                    <input type="number" class="form-control unique_image_block_sort" name="image_block_sort[${image_block_row_no}]" value="${sort}" />
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="file" name="image_block_image_name[${image_block_row_no}]" class="image_block_image_name" value="" />
                    <div class="select-img-size-box" style="
                        width: 100%;
                        height: 40px;
                        display:none;
                        text-align: center;
                        background-color:red;
                        ">
                        <span class="select-img-size-text" style="color:#FFFFFF;font-weight:bold; width: 100%; text-align: center; ">
                        </span>
                    </div>
                    <img src="${image_name_url}" class="img-responsive img_image_block_image_name" width="300" height="300" /><br />
                    <button type="button" class="btn btn-danger btn-delete-image-block-image-name" title="刪除"><i class="fa-solid fa-trash-can"></i></button>
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
                <button type="button" class="btn btn-danger btn-delete-image"><i class="fa-solid fa-trash-can"></i> 刪除</button>
            </td>
        </tr>
    `);

    $(".js-select2-image-block-product-category").select2();

    if (image_name_url) {
        $(
            `#image-block table > tbody [name="image_block_image_name[${image_block_row_no}]"]`
        ).hide();
    } else {
        $(
            `#image-block table > tbody [name="image_block_image_name[${image_block_row_no}]"]`
        )
            .siblings(
                ".img_image_block_image_name, .btn-delete-image-block-image-name"
            )
            .hide();
    }

    $("#image-block-row-no").val(parseInt(image_block_row_no) + 1);

    validate.validateImageBlock(image_block_row_no);
};

window.addTextBlock = (product_category_select_options, datas = {}) => {
    let text_block_row_no = datas.id;
    let sort = datas.sort != null ? datas.sort : "";
    let texts = datas.texts ? datas.texts : "";
    let target_url = datas.target_url ? datas.target_url : "";

    $("#text-block table > tbody").append(`
        <tr>
            <input type="hidden" name="text_block_id[${text_block_row_no}]" value="${text_block_row_no}">
            <td class="sort">
                <div class="form-group">
                    <input type="number" class="form-control unique_text_block_sort" name="text_block_sort[${text_block_row_no}]" value="${sort}" />
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
                <button type="button" class="btn btn-danger btn-delete-text"><i class="fa-solid fa-trash-can"></i> 刪除</button>
            </td>
        </tr>
    `);

    $(".js-select2-text-block-product-category").select2();

    $("#text-block-row-no").val(parseInt(text_block_row_no) + 1);

    validate.validateTextBlock(text_block_row_no);
};

window.addProductBlockProduct = (product_select_options, datas = {}) => {
    let product_block_product_row_no = datas.id;
    let sort = datas.sort != null ? datas.sort : "";

    $("#tab-product table > tbody").append(`
        <tr>
            <input type="hidden" name="product_block_product_id[${product_block_product_row_no}]" value="${product_block_product_row_no}">
            <td class="sort">
                <div class="form-group">
                    <input type="number" class="form-control unique_product_block_product_sort" name="product_block_product_sort[${product_block_product_row_no}]" value="${sort}" />
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select class="form-control js-select2-product-block-product unique_product_block_product_product_id" name="product_block_product_product_id[${product_block_product_row_no}]">
                        <option></option>
                        ${product_select_options}
                    </select>
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-delete-product-product"><i class="fa-solid fa-trash-can"></i> 刪除</button>
            </td>
        </tr>
    `);

    $(".js-select2-product-block-product").select2();

    $("#product-block-product-row-no").val(
        parseInt(product_block_product_row_no) + 1
    );

    validate.validateProductBlockProduct(product_block_product_row_no);
};

window.addProductBlockCategory = (
    product_category_select_options,
    datas = {}
) => {
    let product_block_category_row_no = datas.id;
    let sort = datas.sort != null ? datas.sort : "";

    $("#tab-category table > tbody").append(`
        <tr>
            <input type="hidden" name="product_block_category_id[${product_block_category_row_no}]" value="${product_block_category_row_no}">
            <td class="sort">
                <div class="form-group">
                    <input type="number" class="form-control unique_product_block_category_sort" name="product_block_category_sort[${product_block_category_row_no}]" value="${sort}" />
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select class="form-control js-select2-product-block-category unique_product_block_product_web_category_hierarchy_id" name="product_block_product_web_category_hierarchy_id[${product_block_category_row_no}]">
                        <option></option>
                        ${product_category_select_options}
                    </select>
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-delete-product-category"><i class="fa-solid fa-trash-can"></i> 刪除</button>
            </td>
        </tr>
    `);

    $(".js-select2-product-block-category").select2();

    $("#product-block-category-row-no").val(
        parseInt(product_block_category_row_no) + 1
    );

    validate.validateProductBlockCategory(product_block_category_row_no);
};

// 啟用版位主色
window.enableSlotColorCode = () => {
    $("#slot_color_code").prop("disabled", false);

    if ($("#slot_color_code").prev("label").find("span").length < 1) {
        $("#slot_color_code")
            .prev("label")
            .append(' <span style="color:red;">*</span>');
    }

    validate.validateSlotColorCode();
};

// 啟用版位icon
window.enableSlotIconName = () => {
    $("#slot_icon_name").prop("disabled", false);

    if (
        $("#slot_icon_name").closest(".form-group").find("label > span")
            .length < 1
    ) {
        $("#slot_icon_name")
            .closest(".form-group")
            .find("label")
            .append(' <span style="color:red;">*</span>');
    }

    validate.validateSlotIconName();
};

// 啟用版位標題
window.enableSlotTitle = () => {
    $("#slot_title").prop("disabled", false);

    if ($("#slot_title").prev("label").find("span").length < 1) {
        $("#slot_title")
            .prev("label")
            .append(' <span style="color:red;">*</span>');
    }

    validate.validateSlotTitle();
};

// 停用版位主色
window.disableSlotColorCode = () => {
    $("#slot_color_code")
        .prop("disabled", true)
        .val("")
        .prev("label")
        .find("span")
        .remove();

    validate.removeSlotColorCodeValidation();
};

// 停用版位icon
window.disableSlotIconName = () => {
    $("#slot_icon_name")
        .prop("disabled", true)
        .val("")
        .prev("label")
        .find("span")
        .remove();

    validate.removeSlotIconNameValidation();
};

// 停用版位標題
window.disableSlotTitle = () => {
    $("#slot_title")
        .prop("disabled", true)
        .val("")
        .prev("label")
        .find("span")
        .remove();

    validate.removeSlotTitleValidation();
};
