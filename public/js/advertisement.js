/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/js/advertisement/validate.js":
/*!************************************************!*\
  !*** ./resources/js/advertisement/validate.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "validateImageBlock": () => (/* binding */ validateImageBlock),
/* harmony export */   "validateTextBlock": () => (/* binding */ validateTextBlock),
/* harmony export */   "validateProductBlockProduct": () => (/* binding */ validateProductBlockProduct),
/* harmony export */   "validateProductBlockCategory": () => (/* binding */ validateProductBlockCategory),
/* harmony export */   "validateSlotColorCode": () => (/* binding */ validateSlotColorCode),
/* harmony export */   "validateSlotIconName": () => (/* binding */ validateSlotIconName),
/* harmony export */   "validateSlotTitle": () => (/* binding */ validateSlotTitle),
/* harmony export */   "removeSlotColorCodeValidation": () => (/* binding */ removeSlotColorCodeValidation),
/* harmony export */   "removeSlotIconNameValidation": () => (/* binding */ removeSlotIconNameValidation),
/* harmony export */   "removeSlotTitleValidation": () => (/* binding */ removeSlotTitleValidation)
/* harmony export */ });
// 加入圖檔區塊欄位驗證
var validateImageBlock = function validateImageBlock(row_no) {
  $("#image-block table > tbody [name=\"image_block_sort[".concat(row_no, "]\"]")).rules("add", {
    required: true,
    digits: true,
    unique: ".unique_image_block_sort"
  });
  $("#image-block table > tbody [name=\"image_block_image_name[".concat(row_no, "]\"]")).rules("add", {
    required: {
      depends: function depends(element) {
        return !$("#image-block table > tbody [name=\"image_block_image_name[".concat(row_no, "]\"]")).prop("disabled");
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

var validateTextBlock = function validateTextBlock(row_no) {
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

var validateProductBlockProduct = function validateProductBlockProduct(row_no) {
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

var validateProductBlockCategory = function validateProductBlockCategory(row_no) {
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

var validateSlotColorCode = function validateSlotColorCode() {
  $("#slot_color_code").rules("add", {
    required: true
  });
}; // 加入版位icon欄位驗證

var validateSlotIconName = function validateSlotIconName() {
  $("#slot_icon_name").rules("add", {
    required: {
      depends: function depends(element) {
        return !$("#slot_icon_name").prop("disabled");
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

var validateSlotTitle = function validateSlotTitle() {
  $("#slot_title").rules("add", {
    required: true
  });
}; // 移除版位主色欄位驗證

var removeSlotColorCodeValidation = function removeSlotColorCodeValidation() {
  $("#slot_color_code").rules("remove");
  $("#slot_color_code").closest(".form-group").removeClass("has-error").find('.help-block').hide();
}; // 移除版位icon欄位驗證

var removeSlotIconNameValidation = function removeSlotIconNameValidation() {
  $("#slot_icon_name").rules("remove");
  $("#slot_icon_name").closest(".form-group").removeClass("has-error").find('.help-block').hide();
}; // 移除版位標題欄位驗證

var removeSlotTitleValidation = function removeSlotTitleValidation() {
  $("#slot_title").rules("remove");
  $("#slot_title").closest(".form-group").removeClass("has-error").find('.help-block').hide();
};

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!********************************************!*\
  !*** ./resources/js/advertisement/main.js ***!
  \********************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _validate__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./validate */ "./resources/js/advertisement/validate.js");
 // 初始化資料

window.init = function () {
  var datas = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var ad_slot_select_options = datas.ad_slot_select_options ? datas.ad_slot_select_options : "";
  var product_category_select_options = datas.product_category_select_options ? datas.product_category_select_options : "";
  var product_select_options = datas.product_select_options ? datas.product_select_options : "";

  if (ad_slot_select_options) {
    $("#slot_id").append(ad_slot_select_options);
  } else {
    $("#slot_id").prev("label").find("span").remove();
  }

  $(".js-select2-slot-id").select2();
  var start_at_flatpickr = flatpickr("#start_at_flatpickr", {
    dateFormat: "Y-m-d H:i:S",
    maxDate: $("#end_at").val(),
    enableTime: true,
    enableSeconds: true,
    defaultHour: 0,
    defaultMinute: 0,
    defaultSeconds: 0,
    onChange: function onChange(selectedDates, dateStr, instance) {
      end_at_flatpickr.set('minDate', dateStr);

      if (!end_at_flatpickr.input.value) {
        end_at_flatpickr.hourElement.value = 23;
        end_at_flatpickr.minuteElement.value = 59;
        end_at_flatpickr.secondElement.value = 59;
      }
    }
  });
  var end_at_flatpickr = flatpickr("#end_at_flatpickr", {
    dateFormat: "Y-m-d H:i:S",
    minDate: $("#start_at").val(),
    enableTime: true,
    enableSeconds: true,
    defaultHour: 23,
    defaultMinute: 59,
    defaultSeconds: 59,
    onChange: function onChange(selectedDates, dateStr, instance) {
      start_at_flatpickr.set('maxDate', dateStr);
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
    var datas = {
      id: $("#image-block-row-no").val()
    };
    addImageBlock(product_category_select_options, datas);
  }); // 刪除圖檔

  $(document).on("click", ".btn-delete-image", function () {
    if (confirm("確定要刪除嗎?")) {
      $(this).parents("tr").remove();
    }
  }); // 新增文字

  $("#btn-new-text").on("click", function () {
    var datas = {
      id: $("#text-block-row-no").val()
    };
    addTextBlock(product_category_select_options, datas);
  }); // 刪除文字

  $(document).on("click", ".btn-delete-text", function () {
    if (confirm("確定要刪除嗎?")) {
      $(this).parents("tr").remove();
    }
  }); // 新增商品的指定商品

  $("#btn-new-product-product").on("click", function () {
    var datas = {
      id: $("#product-block-product-row-no").val()
    };
    addProductBlockProduct(product_select_options, datas); // 編輯才做

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
    var datas = {
      id: $("#product-block-category-row-no").val()
    };
    addProductBlockCategory(product_category_select_options, datas); // 編輯才做

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
    var target_campaign_btn_div = form_group_element.find('.target_campaign_btn_div');
    target_campaign_btn_div.hide();

    switch (image_action) {
      case "X":
        form_group_element.find('[name^="image_block_target_url"]').val("");
        form_group_element.find('[name^="image_block_target_cate_hierarchy_id"]').val("").trigger("change");
        form_group_element.find('[name^="target_campaign_name"]').val("");
        form_group_element.find('[name^="target_campaign_id"]').val("");
        break;

      case "U":
        form_group_element.find('[name^="image_block_target_cate_hierarchy_id"]').val("").trigger("change");
        form_group_element.find('[name^="target_campaign_name"]').val("");
        form_group_element.find('[name^="target_campaign_id"]').val("");
        break;

      case "C":
        form_group_element.find('[name^="image_block_target_url"]').val("");
        form_group_element.find('[name^="target_campaign_name"]').val("");
        form_group_element.find('[name^="target_campaign_id"]').val("");
        break;

      case "M":
        form_group_element.find('[name^="image_block_target_url"]').val("");
        form_group_element.find('[name^="image_block_target_cate_hierarchy_id"]').val("").trigger("change");
        target_campaign_btn_div.show();
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
  }); // 選擇版位icon檔案

  $("#slot_icon_name").on("change", function () {
    var file = this.files[0];

    if (file) {
      $("#img_slot_icon_name").attr("src", URL.createObjectURL(file));
      $("#img_slot_icon_name, #btn-delete-slot-icon-name").show();
    }
  }); // 刪除版位icon

  $("#btn-delete-slot-icon-name").on("click", function () {
    $("#img_slot_icon_name").attr("src", "");
    $("#img_slot_icon_name, #btn-delete-slot-icon-name").hide();
    $("#slot_icon_name").val("").show();
  }); // 選擇圖片區的圖片檔案

  $(document).on("change", ".image_block_image_name", function () {
    var file = this.files[0];
    var photo_width = $('#slot_id').find('option:selected').attr('data-photo-width');
    var photo_height = $('#slot_id').find('option:selected').attr('data-photo-height');
    var vm = $(this);

    if (file) {
      if (photo_width && photo_height) {
        //顯示選擇照片的尺寸提醒
        var img;
        img = new Image();
        var objectUrl = URL.createObjectURL(file);

        img.onload = function () {
          if (this.width !== parseInt(photo_width) || this.height !== parseInt(photo_height)) {
            var show_text = '上傳尺寸 ' + this.width + '*' + this.height + ' 非預期，存檔後系統會自動壓縮成制式尺寸！';
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
      $(this).siblings(".img_image_block_image_name").attr("src", URL.createObjectURL(file));
      $(this).siblings(".img_image_block_image_name, .btn-delete-image-block-image-name").show();
    }
  });
  $(document).on("click", ".target_campaign_btn", function () {
    $('#now_row_num').val($(this).data('rownum'));
    $('#promotion_campaign_model').modal('toggle');
  });
  $(document).on("click", ".search_btn", function () {
    $("#promotion_campaign_model_list").empty();
    var type = $(this).data('type');
    var promotional_campaigns_key_word = $('#promotional_campaigns_key_word').val();
    var promotional_campaigns_time_type = $('#promotional_campaigns_time_type').val();
    var level_code = 'CART_P';

    switch (type) {
      case 'promotion_campaign':
        axios.post('/backend/advertisemsement_launch/ajax/search-promotion-campaign', {
          'promotional_campaigns_key_word': promotional_campaigns_key_word,
          'promotional_campaigns_time_type': promotional_campaigns_time_type,
          'level_code': level_code
        }).then(function (response) {
          var html = '';
          $.each(response.data.data, function (key, value) {
            html += "<tr>\n                            <td>\n                                <button type=\"button\" class=\"btn btn-primary btn_add_promotion_campaign\"\n                                data-id=\"".concat(value.id, "\" data-name=\"").concat(value.campaign_name, "\"\n                                data-dismiss=\"modal\">\u5E36\u5165\n                                </button>\n                            </td>\n                            <td>").concat(value.campaign_name, "</td>\n                            <td>").concat(value.start_at, " ~ ").concat(value.end_at, "\u4E0A\u67B6\u6642\u9593</td>\n                            <td>").concat(value.id, "</td>\n                        </tr>");
          });
          $('#promotion_campaign_model_list').append(html);
        })["catch"](function (error) {
          console.log('ERROR');
        });
        break;
    }
  });
  $(document).on("click", ".btn_add_promotion_campaign", function () {
    var now_row_num = $('#now_row_num').val();
    $('.target_campaign_name_' + now_row_num).val($(this).data('name'));
    $('.target_campaign_id_' + now_row_num).val($(this).data('id'));
  }); // 刪除圖片區的圖片

  $(document).on("click", ".btn-delete-image-block-image-name", function () {
    $(this).siblings(".img_image_block_image_name").attr("src", "").hide();
    $(this).siblings(".select-img-size-box").hide();
    $(this).siblings(".select-img-size-box").find('.select-img-size-text').text('');
    $(this).hide();
    $(this).siblings(".image_block_image_name").val("").show();
  });
}; // 取得版位下拉選項


window.getAdSlotSelectOptions = function () {
  var datas = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var options = "";
  $.each(datas, function (key, value) {
    options += "\n            <option value='".concat(value["id"], "'\n            data-is-user-defined=\"").concat(value["is_user_defined"], "\"\n            data-slot-type=\"").concat(value["slot_type"], "\"\n            data-photo-width=\"").concat(value["photo_width"], "\"\n            data-photo-height=\"").concat(value["photo_height"], "\"\n            >\u3010").concat(value["slot_code"], "\u3011").concat(value["slot_desc"], "\n            </option>\n        ");
  });
  return options;
}; // 取得商品分類下拉選項


window.getProductCategorySelectOptions = function () {
  var datas = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var options = "";
  $.each(datas, function (key, value) {
    options += "\n            <option value='".concat(value["id"], "'>").concat(value["name"], "</option>\n        ");
  });
  return options;
}; // 取得商品下拉選項


window.getProductSelectOptions = function () {
  var datas = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var options = "";
  $.each(datas, function (key, value) {
    options += "\n            <option value='".concat(value["id"], "'>").concat(value["product_no"], " ").concat(value["product_name"], "</option>\n        ");
  });
  return options;
};

window.addImageBlock = function () {
  var product_category_select_options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";
  var datas = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  console.log("addImageBlock");
  var image_block_row_no = datas.id;
  var sort = datas.sort != null ? datas.sort : "";
  var image_name_url = datas.image_name_url ? datas.image_name_url : "";
  var image_alt = datas.image_alt ? datas.image_alt : "";
  var image_title = datas.image_title ? datas.image_title : "";
  var image_abstract = datas.image_abstract ? datas.image_abstract : "";
  var target_url = datas.target_url ? datas.target_url : "";
  var target_campaign_name = '賣場名稱';
  var target_campaign_id = '18';
  $("#image-block table > tbody").append("\n        <tr>\n            <input type=\"hidden\" name=\"image_block_id[".concat(image_block_row_no, "]\" value=\"").concat(image_block_row_no, "\">\n            <td class=\"sort\">\n                <div class=\"form-group\">\n                    <input type=\"number\" class=\"form-control unique_image_block_sort\" name=\"image_block_sort[").concat(image_block_row_no, "]\" value=\"").concat(sort, "\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <input type=\"file\" name=\"image_block_image_name[").concat(image_block_row_no, "]\" class=\"image_block_image_name\" value=\"\" />\n                    <div class=\"select-img-size-box\" style=\"\n                        width: 100%;\n                        height: 40px;\n                        display:none;\n                        text-align: center;\n                        background-color:red;\n                        \">\n                        <span class=\"select-img-size-text\" style=\"color:#FFFFFF;font-weight:bold; width: 100%; text-align: center; \">\n                        </span>\n                    </div>\n                    <img src=\"").concat(image_name_url, "\" class=\"img-responsive img_image_block_image_name\" width=\"300\" height=\"300\" /><br />\n                    <button type=\"button\" class=\"btn btn-danger btn-delete-image-block-image-name\" title=\"\u522A\u9664\"><i class=\"fa-solid fa-trash-can\"></i></button>\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <input type=\"text\" class=\"form-control\" name=\"image_block_image_alt[").concat(image_block_row_no, "]\" value=\"").concat(image_alt, "\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <input type=\"text\" class=\"form-control\" name=\"image_block_image_title[").concat(image_block_row_no, "]\" value=\"").concat(image_title, "\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <textarea class=\"form-control\" rows=\"3\" name=\"image_block_image_abstract[").concat(image_block_row_no, "]\">").concat(image_abstract, "</textarea>\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <div class=\"radio\">\n                        <label>\n                            <input type=\"radio\" name=\"image_block_image_action[").concat(image_block_row_no, "]\" value=\"X\" checked />\n                            \u7121\u9023\u7D50\n                        </label>\n                    </div>\n                    <div class=\"row\">\n                        <div class=\"col-sm-4\">\n                            <div class=\"radio\">\n                                <label>\n                                    <input type=\"radio\" name=\"image_block_image_action[").concat(image_block_row_no, "]\" value=\"U\" />\n                                    URL\n                                </label>\n                            </div>\n                        </div>\n                        <div class=\"col-sm-8\">\n                            <div class=\"form-group\">\n                                <input type=\"text\" class=\"form-control\" name=\"image_block_target_url[").concat(image_block_row_no, "]\" value=\"").concat(target_url, "\" />\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"row\">\n                        <div class=\"col-sm-4\">\n                            <div class=\"radio\">\n                                <label>\n                                    <input type=\"radio\" name=\"image_block_image_action[").concat(image_block_row_no, "]\" value=\"C\" />\n                                    \u5546\u54C1\u5206\u985E\u9801\n                                </label>\n                            </div>\n                        </div>\n                        <div class=\"col-sm-8\">\n                            <div class=\"form-group\">\n                                <select class=\"form-control js-select2-image-block-product-category\" name=\"image_block_target_cate_hierarchy_id[").concat(image_block_row_no, "]\">\n                                    <option></option>\n                                    ").concat(product_category_select_options, "\n                                </select>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"row\">\n                        <div class=\"col-sm-4\">\n                            <div class=\"radio\">\n                                <label>\n                                    <input type=\"radio\" name=\"image_block_image_action[").concat(image_block_row_no, "]\" value=\"M\" />\n                                    \u6D3B\u52D5\u8CE3\u5834\n                                </label>\n                            </div>\n                        </div>\n                        <div class=\"col-sm-8\">\n                            <div class=\"form-group\">\n                                <input type=\"text\" class=\"form-control target_campaign_name_").concat(image_block_row_no, "\" name=\"target_campaign_name[").concat(image_block_row_no, "]\" value=\"").concat(target_campaign_name, "\" readonly/>\n                                <input type=\"hidden\" class=\"form-control target_campaign_id_").concat(image_block_row_no, "\" name=\"target_campaign_id[").concat(image_block_row_no, "]\" value=\"").concat(target_campaign_id, "\" readonly/>\n                            </div>\n                        </div>\n                        <div class=\"col-sm-12 target_campaign_btn_div\" style=\"display:none;\">\n                            <button type=\"button\" class=\"btn btn-warning target_campaign_btn\" data-rownum=\"").concat(image_block_row_no, "\" >\u6311\u9078\u8CE3\u5834</button>\n                        </div>\n                    </div>\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <div class=\"checkbox\">\n                        <label>\n                            <input type=\"checkbox\" name=\"image_block_is_target_blank[").concat(image_block_row_no, "]\" value=\"enabled\" style=\"width: 20px;height: 20px;cursor: pointer;\" />\n                        </label>\n                    </div>\n                </div>\n            </td>\n            <td>\n                <button type=\"button\" class=\"btn btn-danger btn-delete-image\"><i class=\"fa-solid fa-trash-can\"></i> \u522A\u9664</button>\n            </td>\n        </tr>\n    "));
  $(".js-select2-image-block-product-category").select2();

  if (image_name_url) {
    $("#image-block table > tbody [name=\"image_block_image_name[".concat(image_block_row_no, "]\"]")).hide();
  } else {
    $("#image-block table > tbody [name=\"image_block_image_name[".concat(image_block_row_no, "]\"]")).siblings(".img_image_block_image_name, .btn-delete-image-block-image-name").hide();
  }

  $("#image-block-row-no").val(parseInt(image_block_row_no) + 1);
  _validate__WEBPACK_IMPORTED_MODULE_0__.validateImageBlock(image_block_row_no);
};

window.addTextBlock = function (product_category_select_options) {
  var datas = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  console.log("addTextBlock");
  var text_block_row_no = datas.id;
  var sort = datas.sort != null ? datas.sort : "";
  var texts = datas.texts ? datas.texts : "";
  var target_url = datas.target_url ? datas.target_url : "";
  $("#text-block table > tbody").append("\n        <tr>\n            <input type=\"hidden\" name=\"text_block_id[".concat(text_block_row_no, "]\" value=\"").concat(text_block_row_no, "\">\n            <td class=\"sort\">\n                <div class=\"form-group\">\n                    <input type=\"number\" class=\"form-control unique_text_block_sort\" name=\"text_block_sort[").concat(text_block_row_no, "]\" value=\"").concat(sort, "\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <input type=\"text\" class=\"form-control\" name=\"text_block_texts[").concat(text_block_row_no, "]\" value=\"").concat(texts, "\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <div class=\"radio\">\n                        <label>\n                            <input type=\"radio\" name=\"text_block_image_action[").concat(text_block_row_no, "]\" value=\"X\" checked />\n                            \u7121\u9023\u7D50\n                        </label>\n                    </div>\n                    <div class=\"row\">\n                        <div class=\"col-sm-4\">\n                            <div class=\"radio\">\n                                <label>\n                                    <input type=\"radio\" name=\"text_block_image_action[").concat(text_block_row_no, "]\" value=\"U\" />\n                                    URL\n                                </label>\n                            </div>\n                        </div>\n                        <div class=\"col-sm-8\">\n                            <div class=\"form-group\">\n                                <input type=\"text\" class=\"form-control\" name=\"text_block_target_url[").concat(text_block_row_no, "]\" value=\"").concat(target_url, "\" />\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"row\">\n                        <div class=\"col-sm-4\">\n                            <div class=\"radio\">\n                                <label>\n                                    <input type=\"radio\" name=\"text_block_image_action[").concat(text_block_row_no, "]\" value=\"C\" />\n                                    \u5546\u54C1\u5206\u985E\u9801\n                                </label>\n                            </div>\n                        </div>\n                        <div class=\"col-sm-8\">\n                            <div class=\"form-group\">\n                                <select class=\"form-control js-select2-text-block-product-category\" name=\"text_block_target_cate_hierarchy_id[").concat(text_block_row_no, "]\">\n                                    <option></option>\n                                    ").concat(product_category_select_options, "\n                                </select>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <div class=\"checkbox\">\n                        <label>\n                            <input type=\"checkbox\" name=\"text_block_is_target_blank[").concat(text_block_row_no, "]\" value=\"enabled\" style=\"width: 20px;height: 20px;cursor: pointer;\" />\n                        </label>\n                    </div>\n                </div>\n            </td>\n            <td>\n                <button type=\"button\" class=\"btn btn-danger btn-delete-text\"><i class=\"fa-solid fa-trash-can\"></i> \u522A\u9664</button>\n            </td>\n        </tr>\n    "));
  $(".js-select2-text-block-product-category").select2();
  $("#text-block-row-no").val(parseInt(text_block_row_no) + 1);
  _validate__WEBPACK_IMPORTED_MODULE_0__.validateTextBlock(text_block_row_no);
};

window.addProductBlockProduct = function (product_select_options) {
  var datas = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var product_block_product_row_no = datas.id;
  var sort = datas.sort != null ? datas.sort : "";
  $("#tab-product table > tbody").append("\n        <tr>\n            <input type=\"hidden\" name=\"product_block_product_id[".concat(product_block_product_row_no, "]\" value=\"").concat(product_block_product_row_no, "\">\n            <td class=\"sort\">\n                <div class=\"form-group\">\n                    <input type=\"number\" class=\"form-control unique_product_block_product_sort\" name=\"product_block_product_sort[").concat(product_block_product_row_no, "]\" value=\"").concat(sort, "\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <select class=\"form-control js-select2-product-block-product unique_product_block_product_product_id\" name=\"product_block_product_product_id[").concat(product_block_product_row_no, "]\">\n                        <option></option>\n                        ").concat(product_select_options, "\n                    </select>\n                </div>\n            </td>\n            <td>\n                <button type=\"button\" class=\"btn btn-danger btn-delete-product-product\"><i class=\"fa-solid fa-trash-can\"></i> \u522A\u9664</button>\n            </td>\n        </tr>\n    "));
  $(".js-select2-product-block-product").select2();
  $("#product-block-product-row-no").val(parseInt(product_block_product_row_no) + 1);
  _validate__WEBPACK_IMPORTED_MODULE_0__.validateProductBlockProduct(product_block_product_row_no);
};

window.addProductBlockCategory = function (product_category_select_options) {
  var datas = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var product_block_category_row_no = datas.id;
  var sort = datas.sort != null ? datas.sort : "";
  $("#tab-category table > tbody").append("\n        <tr>\n            <input type=\"hidden\" name=\"product_block_category_id[".concat(product_block_category_row_no, "]\" value=\"").concat(product_block_category_row_no, "\">\n            <td class=\"sort\">\n                <div class=\"form-group\">\n                    <input type=\"number\" class=\"form-control unique_product_block_category_sort\" name=\"product_block_category_sort[").concat(product_block_category_row_no, "]\" value=\"").concat(sort, "\" />\n                </div>\n            </td>\n            <td>\n                <div class=\"form-group\">\n                    <select class=\"form-control js-select2-product-block-category unique_product_block_product_web_category_hierarchy_id\" name=\"product_block_product_web_category_hierarchy_id[").concat(product_block_category_row_no, "]\">\n                        <option></option>\n                        ").concat(product_category_select_options, "\n                    </select>\n                </div>\n            </td>\n            <td>\n                <button type=\"button\" class=\"btn btn-danger btn-delete-product-category\"><i class=\"fa-solid fa-trash-can\"></i> \u522A\u9664</button>\n            </td>\n        </tr>\n    "));
  $(".js-select2-product-block-category").select2();
  $("#product-block-category-row-no").val(parseInt(product_block_category_row_no) + 1);
  _validate__WEBPACK_IMPORTED_MODULE_0__.validateProductBlockCategory(product_block_category_row_no);
}; // 啟用版位主色


window.enableSlotColorCode = function () {
  $("#slot_color_code").prop("disabled", false);

  if ($("#slot_color_code").prev("label").find("span").length < 1) {
    $("#slot_color_code").prev("label").append(' <span style="color:red;">*</span>');
  }

  _validate__WEBPACK_IMPORTED_MODULE_0__.validateSlotColorCode();
}; // 啟用版位icon


window.enableSlotIconName = function () {
  $("#slot_icon_name").prop("disabled", false);

  if ($("#slot_icon_name").closest(".form-group").find("label > span").length < 1) {
    $("#slot_icon_name").closest(".form-group").find("label").append(' <span style="color:red;">*</span>');
  }

  _validate__WEBPACK_IMPORTED_MODULE_0__.validateSlotIconName();
}; // 啟用版位標題


window.enableSlotTitle = function () {
  $("#slot_title").prop("disabled", false);

  if ($("#slot_title").prev("label").find("span").length < 1) {
    $("#slot_title").prev("label").append(' <span style="color:red;">*</span>');
  }

  _validate__WEBPACK_IMPORTED_MODULE_0__.validateSlotTitle();
}; // 停用版位主色


window.disableSlotColorCode = function () {
  $("#slot_color_code").prop("disabled", true).val("").prev("label").find("span").remove();
  _validate__WEBPACK_IMPORTED_MODULE_0__.removeSlotColorCodeValidation();
}; // 停用版位icon


window.disableSlotIconName = function () {
  $("#slot_icon_name").prop("disabled", true).val("").prev("label").find("span").remove();
  _validate__WEBPACK_IMPORTED_MODULE_0__.removeSlotIconNameValidation();
}; // 停用版位標題


window.disableSlotTitle = function () {
  $("#slot_title").prop("disabled", true).val("").prev("label").find("span").remove();
  _validate__WEBPACK_IMPORTED_MODULE_0__.removeSlotTitleValidation();
};
})();

/******/ })()
;