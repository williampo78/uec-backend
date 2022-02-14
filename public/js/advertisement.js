/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other entry modules.
(() => {
/*!********************************************!*\
  !*** ./resources/js/advertisement/main.js ***!
  \********************************************/
// 初始化資料
init = function init() {
  var datas = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var ad_slot_select_options = datas.ad_slot_select_options ? datas.ad_slot_select_options : "";
  var product_category_select_options = datas.product_category_select_options ? datas.product_category_select_options : "";
  var product_select_options = datas.product_select_options ? datas.product_select_options : "";

  if (ad_slot_select_options) {
    $("#slot_id").append(ad_slot_select_options);
  } else {
    $("#slot_id").prev("label").find("span").remove();
  }

  $(".js-select2-slot-id").select2({
    allowClear: true,
    theme: "bootstrap",
    placeholder: ""
  });
  $("#datetimepicker_start_at").datetimepicker({
    format: "YYYY-MM-DD HH:mm",
    showClear: true
  });
  $("#datetimepicker_start_at").on("dp.change", function (e) {
    if (e.oldDate === null) {
      $(this).data("DateTimePicker").date(new Date(e.date._d.setHours(0, 0, 0)));
    }
  });
  $("#datetimepicker_end_at").datetimepicker({
    format: "YYYY-MM-DD HH:mm",
    showClear: true
  });
  $("#datetimepicker_end_at").on("dp.change", function (e) {
    if (e.oldDate === null) {
      $(this).data("DateTimePicker").date(new Date(e.date._d.setHours(23, 59, 59)));
    }
  });
  $(".colorpicker").colorpicker({
    format: "hex",
    align: "left",
    customClass: "colorpicker-2x",
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
  }); // 點擊指定商品radio button

  $("#product_assigned_type_product").on("click", function () {
    $('#product-block-tab a[href="#tab-product"]').tab("show");
  }); // 點擊指定分類radio button

  $("#product_assigned_type_category").on("click", function () {
    $('#product-block-tab a[href="#tab-category"]').tab("show");
  }); // 點擊商品tab

  $('#product-block-tab a[href="#tab-product"]').on("show.bs.tab", function (e) {
    $("#product_assigned_type_product").prop("checked", true);
  }); // 點擊分類tab

  $('#product-block-tab a[href="#tab-category"]').on("show.bs.tab", function (e) {
    $("#product_assigned_type_category").prop("checked", true);
  }); // 新增圖檔

  $("#btn-new-image").on("click", function () {
    addImageBlock(product_category_select_options);
  }); // 刪除圖檔

  $(document).on("click", ".btn-delete-image", function () {
    if (confirm("確定要刪除嗎?")) {
      $(this).parents("tr").remove();
    }
  }); // 新增文字

  $("#btn-new-text").on("click", function () {
    addTextBlock(product_category_select_options);
  }); // 刪除文字

  $(document).on("click", ".btn-delete-text", function () {
    if (confirm("確定要刪除嗎?")) {
      $(this).parents("tr").remove();
    }
  }); // 新增商品的指定商品

  $("#btn-new-product-product").on("click", function () {
    addProductBlockProduct(product_select_options); // 編輯才做

    if (!ad_slot_select_options) {
      // 當指定商品有項目時，隱藏指定分類的選項
      if ($("#tab-product table > tbody > tr").length > 0) {
        $("#product_assigned_type_category").parent("label").hide();
        $('#product-block-tab a[href="#tab-category"]').parent("li").hide();
      }
    }
  }); // 刪除商品的指定商品

  $(document).on("click", ".btn-delete-product-product", function () {
    if (confirm("確定要刪除嗎?")) {
      $(this).parents("tr").remove(); // 編輯才做

      if (!ad_slot_select_options) {
        // 當指定商品沒有任何項目時，顯示指定分類的選項
        if ($("#tab-product table > tbody > tr").length < 1) {
          $("#product_assigned_type_category").parent("label").show();
          $('#product-block-tab a[href="#tab-category"]').parent("li").show(); // 當指定分類有項目時，隱藏指定商品的選項

          if ($("#tab-category table > tbody > tr").length > 0) {
            $("#product_assigned_type_product").parent("label").hide();
            $('#product-block-tab a[href="#tab-product"]').parent("li").hide();
            $("#product_assigned_type_category").click();
          }
        }
      }
    }
  }); // 新增商品的指定分類

  $("#btn-new-product-category").on("click", function () {
    addProductBlockCategory(product_category_select_options); // 編輯才做

    if (!ad_slot_select_options) {
      // 當指定分類有項目時，隱藏指定商品的選項
      if ($("#tab-category table > tbody > tr").length > 0) {
        $("#product_assigned_type_product").parent("label").hide();
        $('#product-block-tab a[href="#tab-product"]').parent("li").hide();
      }
    }
  }); // 刪除商品的指定分類

  $(document).on("click", ".btn-delete-product-category", function () {
    if (confirm("確定要刪除嗎?")) {
      $(this).parents("tr").remove(); // 編輯才做

      if (!ad_slot_select_options) {
        // 當指定分類沒有任何項目時，顯示指定商品的選項
        if ($("#tab-category table > tbody > tr").length < 1) {
          $("#product_assigned_type_product").parent("label").show();
          $('#product-block-tab a[href="#tab-product"]').parent("li").show(); // 當指定商品有項目時，隱藏指定分類的選項

          if ($("#tab-product table > tbody > tr").length > 0) {
            $("#product_assigned_type_category").parent("label").hide();
            $('#product-block-tab a[href="#tab-category"]').parent("li").hide();
            $("#product_assigned_type_product").click();
          }
        }
      }
    }
  }); // 切換圖檔連結內容選項時，清除其他選項的值

  $(document).on("click", '[name^="image_block_image_action"]', function () {
    var image_action = $(this).val();
    var form_group_element = $(this).closest(".form-group");

    switch (image_action) {
      case "X":
        form_group_element.find('[name^="image_block_target_url"]').val("");
        form_group_element.find('[name^="image_block_target_cate_hierarchy_id"]').val("").trigger("change");
        break;

      case "U":
        form_group_element.find('[name^="image_block_target_cate_hierarchy_id"]').val("").trigger("change");
        break;

      case "C":
        form_group_element.find('[name^="image_block_target_url"]').val("");
        break;
    }
  }); // 切換文字連結內容選項時，清除其他選項的值

  $(document).on("click", '[name^="text_block_image_action"]', function () {
    var image_action = $(this).val();
    var form_group_element = $(this).closest(".form-group");

    switch (image_action) {
      case "X":
        form_group_element.find('[name^="text_block_target_url"]').val("");
        form_group_element.find('[name^="text_block_target_cate_hierarchy_id"]').val("").trigger("change");
        break;

      case "U":
        form_group_element.find('[name^="text_block_target_cate_hierarchy_id"]').val("").trigger("change");
        break;

      case "C":
        form_group_element.find('[name^="text_block_target_url"]').val("");
        break;
    }
  });
}; // 取得版位下拉選項


getAdSlotSelectOptions = function getAdSlotSelectOptions() {
  var datas = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var options = "";
  $.each(datas, function (key, value) {
    options += "\n            <option value='".concat(value["id"], "' data-is-user-defined=\"").concat(value["is_user_defined"], "\" data-slot-type=\"").concat(value["slot_type"], "\">\u3010").concat(value["slot_code"], "\u3011").concat(value["slot_desc"], "</option>\n        ");
  });
  return options;
}; // 取得商品分類下拉選項


getProductCategorySelectOptions = function getProductCategorySelectOptions() {
  var datas = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var options = "";
  $.each(datas, function (key, value) {
    options += "\n            <option value='".concat(value["id"], "'>").concat(value["name"], "</option>\n        ");
  });
  return options;
}; // 取得商品下拉選項


getProductSelectOptions = function getProductSelectOptions() {
  var datas = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var options = "";
  $.each(datas, function (key, value) {
    options += "\n            <option value='".concat(value["id"], "'>").concat(value["product_no"], " ").concat(value["product_name"], "</option>\n        ");
  });
  return options;
};

addImageBlock = function addImageBlock() {
  var product_category_select_options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";
  var datas = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var image_block_row_no = datas.id ? datas.id : $("#image-block-row-no").val();
  var image_block_id = datas.id ? datas.id : "new";
  var sort = datas.sort !== null ? datas.sort : "";
  var image_name_url = datas.image_name_url ? "<img src=\"".concat(datas.image_name_url, "\" class=\"img-responsive\" width=\"400\" height=\"400\" />") : "";
  var image_alt = datas.image_alt ? datas.image_alt : "";
  var image_title = datas.image_title ? datas.image_title : "";
  var image_abstract = datas.image_abstract ? datas.image_abstract : "";
  var target_url = datas.target_url ? datas.target_url : "";
  $("#image-block table > tbody").append("\n        <tr>\n            <input type=\"hidden\" name=\"image_block_id[".concat(image_block_row_no, "]\" value=\"").concat(image_block_id, "\">\n            <td class=\"sort\">\n                <div class=\"form-group\">\n                    <input type=\"number\" class=\"form-control unique_image_block_sort\" name=\"image_block_sort[").concat(image_block_row_no, "]\" value=\"").concat(sort, "\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    ").concat(image_name_url, "\n                    <input type=\"file\" name=\"image_block_image_name[").concat(image_block_row_no, "]\" value=\"\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <input type=\"text\" class=\"form-control\" name=\"image_block_image_alt[").concat(image_block_row_no, "]\" value=\"").concat(image_alt, "\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <input type=\"text\" class=\"form-control\" name=\"image_block_image_title[").concat(image_block_row_no, "]\" value=\"").concat(image_title, "\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <textarea class=\"form-control\" rows=\"3\" name=\"image_block_image_abstract[").concat(image_block_row_no, "]\">").concat(image_abstract, "</textarea>\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <div class=\"radio\">\n                        <label>\n                            <input type=\"radio\" name=\"image_block_image_action[").concat(image_block_row_no, "]\" value=\"X\" checked />\n                            \u7121\u9023\u7D50\n                        </label>\n                    </div>\n                    <div class=\"row\">\n                        <div class=\"col-sm-4\">\n                            <div class=\"radio\">\n                                <label>\n                                    <input type=\"radio\" name=\"image_block_image_action[").concat(image_block_row_no, "]\" value=\"U\" />\n                                    URL\n                                </label>\n                            </div>\n                        </div>\n                        <div class=\"col-sm-8\">\n                            <div class=\"form-group\">\n                                <input type=\"text\" class=\"form-control\" name=\"image_block_target_url[").concat(image_block_row_no, "]\" value=\"").concat(target_url, "\" />\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"row\">\n                        <div class=\"col-sm-4\">\n                            <div class=\"radio\">\n                                <label>\n                                    <input type=\"radio\" name=\"image_block_image_action[").concat(image_block_row_no, "]\" value=\"C\" />\n                                    \u5546\u54C1\u5206\u985E\u9801\n                                </label>\n                            </div>\n                        </div>\n                        <div class=\"col-sm-8\">\n                            <div class=\"form-group\">\n                                <select class=\"form-control js-select2-image-block-product-category\" name=\"image_block_target_cate_hierarchy_id[").concat(image_block_row_no, "]\">\n                                    <option></option>\n                                    ").concat(product_category_select_options, "\n                                </select>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <div class=\"checkbox\">\n                        <label>\n                            <input type=\"checkbox\" name=\"image_block_is_target_blank[").concat(image_block_row_no, "]\" value=\"enabled\" style=\"width: 20px;height: 20px;cursor: pointer;\" />\n                        </label>\n                    </div>\n                </div>\n            </td>\n            <td>\n                <button type=\"button\" class=\"btn btn-danger btn-delete-image\"><i class='fa fa-trash-o'></i> \u522A\u9664</button>\n            </td>\n        </tr>\n    "));
  $(".js-select2-image-block-product-category").select2({
    allowClear: true,
    theme: "bootstrap",
    placeholder: ""
  });
  $("#image-block-row-no").val(parseInt(image_block_row_no) + 1);
  validateImageBlock(image_block_row_no);
};

addTextBlock = function addTextBlock(product_category_select_options) {
  var datas = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var text_block_row_no = datas.id ? datas.id : $("#text-block-row-no").val();
  var text_block_id = datas.id ? datas.id : "new";
  var sort = datas.sort !== null ? datas.sort : "";
  var texts = datas.texts ? datas.texts : "";
  var target_url = datas.target_url ? datas.target_url : "";
  $("#text-block table > tbody").append("\n        <tr>\n            <input type=\"hidden\" name=\"text_block_id[".concat(text_block_row_no, "]\" value=\"").concat(text_block_id, "\">\n            <td class=\"sort\">\n                <div class=\"form-group\">\n                    <input type=\"number\" class=\"form-control unique_text_block_sort\" name=\"text_block_sort[").concat(text_block_row_no, "]\" value=\"").concat(sort, "\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <input type=\"text\" class=\"form-control\" name=\"text_block_texts[").concat(text_block_row_no, "]\" value=\"").concat(texts, "\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <div class=\"radio\">\n                        <label>\n                            <input type=\"radio\" name=\"text_block_image_action[").concat(text_block_row_no, "]\" value=\"X\" checked />\n                            \u7121\u9023\u7D50\n                        </label>\n                    </div>\n                    <div class=\"row\">\n                        <div class=\"col-sm-4\">\n                            <div class=\"radio\">\n                                <label>\n                                    <input type=\"radio\" name=\"text_block_image_action[").concat(text_block_row_no, "]\" value=\"U\" />\n                                    URL\n                                </label>\n                            </div>\n                        </div>\n                        <div class=\"col-sm-8\">\n                            <div class=\"form-group\">\n                                <input type=\"text\" class=\"form-control\" name=\"text_block_target_url[").concat(text_block_row_no, "]\" value=\"").concat(target_url, "\" />\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"row\">\n                        <div class=\"col-sm-4\">\n                            <div class=\"radio\">\n                                <label>\n                                    <input type=\"radio\" name=\"text_block_image_action[").concat(text_block_row_no, "]\" value=\"C\" />\n                                    \u5546\u54C1\u5206\u985E\u9801\n                                </label>\n                            </div>\n                        </div>\n                        <div class=\"col-sm-8\">\n                            <div class=\"form-group\">\n                                <select class=\"form-control js-select2-text-block-product-category\" name=\"text_block_target_cate_hierarchy_id[").concat(text_block_row_no, "]\">\n                                    <option></option>\n                                    ").concat(product_category_select_options, "\n                                </select>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <div class=\"checkbox\">\n                        <label>\n                            <input type=\"checkbox\" name=\"text_block_is_target_blank[").concat(text_block_row_no, "]\" value=\"enabled\" style=\"width: 20px;height: 20px;cursor: pointer;\" />\n                        </label>\n                    </div>\n                </div>\n            </td>\n            <td>\n                <button type=\"button\" class=\"btn btn-danger btn-delete-text\"><i class='fa fa-trash-o'></i> \u522A\u9664</button>\n            </td>\n        </tr>\n    "));
  $(".js-select2-text-block-product-category").select2({
    allowClear: true,
    theme: "bootstrap",
    placeholder: ""
  });
  $("#text-block-row-no").val(parseInt(text_block_row_no) + 1);
  validateTextBlock(text_block_row_no);
};

addProductBlockProduct = function addProductBlockProduct(product_select_options) {
  var datas = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var product_block_product_row_no = datas.id ? datas.id : $("#product-block-product-row-no").val();
  var product_block_product_id = datas.id ? datas.id : "new";
  var sort = datas.sort !== null ? datas.sort : "";
  $("#tab-product table > tbody").append("\n        <tr>\n            <input type=\"hidden\" name=\"product_block_product_id[".concat(product_block_product_row_no, "]\" value=\"").concat(product_block_product_id, "\">\n            <td class=\"sort\">\n                <div class=\"form-group\">\n                    <input type=\"number\" class=\"form-control unique_product_block_product_sort\" name=\"product_block_product_sort[").concat(product_block_product_row_no, "]\" value=\"").concat(sort, "\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <select class=\"form-control js-select2-product-block-product unique_product_block_product_product_id\" name=\"product_block_product_product_id[").concat(product_block_product_row_no, "]\">\n                        <option></option>\n                        ").concat(product_select_options, "\n                    </select>\n                </div>\n            </td>\n            <td>\n                <button type=\"button\" class=\"btn btn-danger btn-delete-product-product\"><i class='fa fa-trash-o'></i> \u522A\u9664</button>\n            </td>\n        </tr>\n    "));
  $(".js-select2-product-block-product").select2({
    allowClear: true,
    theme: "bootstrap",
    placeholder: ""
  });
  $("#product-block-product-row-no").val(parseInt(product_block_product_row_no) + 1);
  validateProductBlockProduct(product_block_product_row_no);
};

addProductBlockCategory = function addProductBlockCategory(product_category_select_options) {
  var datas = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var product_block_category_row_no = datas.id ? datas.id : $("#product-block-category-row-no").val();
  var product_block_category_id = datas.id ? datas.id : "new";
  var sort = datas.sort !== null ? datas.sort : "";
  $("#tab-category table > tbody").append("\n        <tr>\n            <input type=\"hidden\" name=\"product_block_category_id[".concat(product_block_category_row_no, "]\" value=\"").concat(product_block_category_id, "\">\n            <td class=\"sort\">\n                <div class=\"form-group\">\n                    <input type=\"number\" class=\"form-control unique_product_block_category_sort\" name=\"product_block_category_sort[").concat(product_block_category_row_no, "]\" value=\"").concat(sort, "\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <select class=\"form-control js-select2-product-block-category unique_product_block_product_web_category_hierarchy_id\" name=\"product_block_product_web_category_hierarchy_id[").concat(product_block_category_row_no, "]\">\n                        <option></option>\n                        ").concat(product_category_select_options, "\n                    </select>\n                </div>\n            </td>\n            <td>\n                <button type=\"button\" class=\"btn btn-danger btn-delete-product-category\"><i class='fa fa-trash-o'></i> \u522A\u9664</button>\n            </td>\n        </tr>\n    "));
  $(".js-select2-product-block-category").select2({
    allowClear: true,
    theme: "bootstrap",
    placeholder: ""
  });
  $("#product-block-category-row-no").val(parseInt(product_block_category_row_no) + 1);
  validateProductBlockCategory(product_block_category_row_no);
}; // 啟用版位主色


enableSlotColorCode = function enableSlotColorCode() {
  $("#slot_color_code").prop("disabled", false);

  if ($("#slot_color_code").prev("label").find("span").length < 1) {
    $("#slot_color_code").prev("label").append(' <span style="color:red;">*</span>');
  }

  validateSlotColorCode();
}; // 啟用版位icon


enableSlotIconName = function enableSlotIconName() {
  $("#slot_icon_name").prop("disabled", false);

  if ($("#slot_icon_name").closest(".form-group").find("label > span").length < 1) {
    $("#slot_icon_name").closest(".form-group").find("label").append(' <span style="color:red;">*</span>');
  }

  validateSlotIconName();
}; // 啟用版位標題


enableSlotTitle = function enableSlotTitle() {
  $("#slot_title").prop("disabled", false);

  if ($("#slot_title").prev("label").find("span").length < 1) {
    $("#slot_title").prev("label").append(' <span style="color:red;">*</span>');
  }

  validateSlotTitle();
}; // 停用版位主色


disableSlotColorCode = function disableSlotColorCode() {
  $("#slot_color_code").prop("disabled", true).val('').prev("label").find("span").remove();
  removeSlotColorCodeValidation();
}; // 停用版位icon


disableSlotIconName = function disableSlotIconName() {
  $("#slot_icon_name").prop("disabled", true).val('').prev("label").find("span").remove();
  removeSlotIconNameValidation();
}; // 停用版位標題


disableSlotTitle = function disableSlotTitle() {
  $("#slot_title").prop("disabled", true).val('').prev("label").find("span").remove();
  removeSlotTitleValidation();
};
})();

// This entry need to be wrapped in an IIFE because it need to be isolated against other entry modules.
(() => {
/*!************************************************!*\
  !*** ./resources/js/advertisement/validate.js ***!
  \************************************************/
// 加入圖檔區塊欄位驗證
validateImageBlock = function validateImageBlock(row_no) {
  $("#image-block table > tbody [name=\"image_block_sort[".concat(row_no, "]\"]")).rules("add", {
    required: true,
    digits: true,
    unique: ".unique_image_block_sort"
  });
  $("#image-block table > tbody [name=\"image_block_image_name[".concat(row_no, "]\"]")).rules("add", {
    required: {
      depends: function depends(element) {
        return $("#image-block table > tbody [name=\"image_block_image_name[".concat(row_no, "]\"]")).closest('.form-group').find('img').length <= 0;
      }
    },
    accept: "image/*",
    filesize: [2, 'MB'],
    messages: {
      required: '請上傳圖片',
      accept: '請上傳圖片'
    }
  });
  $("#image-block table > tbody [name=\"image_block_image_action[".concat(row_no, "]\"]")).rules("add", {
    required: true
  });
  $("#image-block table > tbody [name=\"image_block_target_url[".concat(row_no, "]\"]")).rules("add", {
    required: {
      depends: function depends(element) {
        return $("#image-block table > tbody [name=\"image_block_image_action[".concat(row_no, "]\"][value=\"U\"]")).is(":checked");
      }
    },
    url: true
  });
  $("#image-block table > tbody [name=\"image_block_target_cate_hierarchy_id[".concat(row_no, "]\"]")).rules("add", {
    required: {
      depends: function depends(element) {
        return $("#image-block table > tbody [name=\"image_block_image_action[".concat(row_no, "]\"][value=\"C\"]")).is(":checked");
      }
    }
  });
}; // 加入文字區塊欄位驗證


validateTextBlock = function validateTextBlock(row_no) {
  $("#text-block table > tbody [name=\"text_block_sort[".concat(row_no, "]\"]")).rules("add", {
    required: true,
    digits: true,
    unique: ".unique_text_block_sort"
  });
  $("#text-block table > tbody [name=\"text_block_texts[".concat(row_no, "]\"]")).rules("add", {
    required: true
  });
  $("#text-block table > tbody [name=\"text_block_image_action[".concat(row_no, "]\"]")).rules("add", {
    required: true
  });
  $("#text-block table > tbody [name=\"text_block_target_url[".concat(row_no, "]\"]")).rules("add", {
    required: {
      depends: function depends(element) {
        return $("#text-block table > tbody [name=\"text_block_image_action[".concat(row_no, "]\"][value=\"U\"]")).is(":checked");
      }
    },
    url: true
  });
  $("#text-block table > tbody [name=\"text_block_target_cate_hierarchy_id[".concat(row_no, "]\"]")).rules("add", {
    required: {
      depends: function depends(element) {
        return $("#text-block table > tbody [name=\"text_block_image_action[".concat(row_no, "]\"][value=\"C\"]")).is(":checked");
      }
    }
  });
}; // 加入商品區塊的指定商品欄位驗證


validateProductBlockProduct = function validateProductBlockProduct(row_no) {
  $("#tab-product table > tbody [name=\"product_block_product_sort[".concat(row_no, "]\"]")).rules("add", {
    required: true,
    digits: true,
    unique: ".unique_product_block_product_sort"
  });
  $("#tab-product table > tbody [name=\"product_block_product_product_id[".concat(row_no, "]\"]")).rules("add", {
    required: true,
    unique: ".unique_product_block_product_product_id"
  });
}; // 加入商品區塊的指定分類欄位驗證


validateProductBlockCategory = function validateProductBlockCategory(row_no) {
  $("#tab-category table > tbody [name=\"product_block_category_sort[".concat(row_no, "]\"]")).rules("add", {
    required: true,
    digits: true,
    unique: ".unique_product_block_category_sort"
  });
  $("#tab-category table > tbody [name=\"product_block_product_web_category_hierarchy_id[".concat(row_no, "]\"]")).rules("add", {
    required: true,
    unique: ".unique_product_block_product_web_category_hierarchy_id"
  });
}; // 加入版位主色欄位驗證


validateSlotColorCode = function validateSlotColorCode() {
  $("#slot_color_code").rules("add", {
    required: true
  });
}; // 加入版位icon欄位驗證


validateSlotIconName = function validateSlotIconName() {
  $("#slot_icon_name").rules("add", {
    required: {
      depends: function depends(element) {
        return $("#slot_icon_name").closest('.form-group').find('img').length <= 0;
      }
    },
    accept: "image/*",
    filesize: [2, 'MB'],
    messages: {
      required: '請上傳圖片',
      accept: '請上傳圖片'
    }
  });
}; // 加入版位標題欄位驗證


validateSlotTitle = function validateSlotTitle() {
  $("#slot_title").rules("add", {
    required: true
  });
}; // 移除版位主色欄位驗證


removeSlotColorCodeValidation = function removeSlotColorCodeValidation() {
  $("#slot_color_code").rules("remove");
  $("#slot_color_code").closest(".form-group").removeClass("has-error").find('.help-block').hide();
}; // 移除版位icon欄位驗證


removeSlotIconNameValidation = function removeSlotIconNameValidation() {
  $("#slot_icon_name").rules("remove");
  $("#slot_icon_name").closest(".form-group").removeClass("has-error").find('.help-block').hide();
}; // 移除版位標題欄位驗證


removeSlotTitleValidation = function removeSlotTitleValidation() {
  $("#slot_title").rules("remove");
  $("#slot_title").closest(".form-group").removeClass("has-error").find('.help-block').hide();
};
})();

/******/ })()
;