/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
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
/******/ })()
;