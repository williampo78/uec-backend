import lodash from "lodash";
import $ from "jquery";
import Swal from "sweetalert2";
import "select2";
import "datatables.net";
import "datatables.net-bs";
import "datatables.net-buttons";
import "datatables.net-buttons-bs";
import axios from "axios";
import "jquery-validation";
import "jquery-validation/dist/additional-methods";
import "jquery-validation/dist/localization/messages_zh_TW";
import flatpickr from "flatpickr";
import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect";
import { MandarinTraditional } from "flatpickr/dist/l10n/zh-tw.js";
import Croppie from "Croppie";

import Vue from "vue";

window._ = lodash;

/**
 * JQuery
 */
// 宣告全域物件
window.$ = window.jQuery = $;

/**
 * sweetalert2
 */
// 宣告全域物件
window.Swal = Swal;

/**
 * select2
 */
// 預設值
$.fn.select2.defaults.set("allowClear", true);
$.fn.select2.defaults.set("theme", "bootstrap");
$.fn.select2.defaults.set("placeholder", "請選擇");

/**
 * datatables
 */
// 預設中文語系
$.extend($.fn.dataTable.defaults, {
    language: {
        url: "/datatables.net-plugins/i18n/zh_Hant.json",
    },
});

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * jquery validation
 */
// 預設值
jQuery.validator.setDefaults({
    ignore: ":hidden, .ck",
});

/**
 * flatpickr
 */
window.flatpickr = flatpickr;
flatpickr.monthSelectPlugin = monthSelectPlugin;

flatpickr.setDefaults({
    allowInput: true,
    wrap: true,
    clickOpens: false,
    time_24hr: true,
    locale: MandarinTraditional,
    // onOpen: function(selectedDates, dateStr, instance) {
    //     let defaultDate = new Date();
    //     let minDate = instance.config.minDate;
    //     let maxDate = instance.config.maxDate;

    //     if (dateStr == '') {
    //         if (minDate) {
    //             if (minDate.getTime() > defaultDate.getTime()) {
    //                 defaultDate = minDate;
    //             }
    //         }

    //         if (maxDate) {
    //             if (maxDate.getTime() < defaultDate.getTime()) {
    //                 defaultDate = maxDate;
    //             }
    //         }

    //         instance.setDate(defaultDate, true);
    //     }
    // },
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

/**
 * 裁切圖片
 */
window.Croppie = Croppie;

/**
 * vue
 */
window.Vue = Vue;
