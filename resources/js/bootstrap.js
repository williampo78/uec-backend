import lodash from "lodash";
import $ from "jquery";
import "select2";
import Select2Language from "select2/src/js/select2/i18n/zh-TW";
import "datatables.net";
import "datatables.net-bs";
import DatatableLanguage from "datatables.net-plugins/i18n/zh_Hant.json";
import "datatables.net-buttons";
import "datatables.net-buttons-bs";
import axios from "axios";
import "jquery-validation";
import "jquery-validation/dist/additional-methods";
import "jquery-validation/dist/localization/messages_zh_TW";
import flatpickr from "flatpickr";
import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect";
import { MandarinTraditional } from "flatpickr/dist/l10n/zh-tw";
import Croppie from "Croppie";
import moment from "moment";
import { saveAs } from "file-saver";
import camelcaseKeys from "camelcase-keys";
import snakecaseKeys from "snakecase-keys";

import Vue from "vue";
import VueAffix from "vue-affix";
import VueScrollactive from "vue-scrollactive";
import VueDraggable from "vuedraggable";
import VueSweetalert2 from "vue-sweetalert2";
import VueModal from "vue-js-modal";
import { CollapseTransition } from "@ivanv/vue-collapse-transition";
import FloatingVue from "floating-vue";

window._ = lodash;

/**
 * JQuery
 */
// 宣告全域物件
window.$ = window.jQuery = $;

/**
 * bootstrap 3
 */
require("bootstrap-3");
// 讓 modal 內的元件可以獲得焦點
$.fn.modal.Constructor.prototype.enforceFocus = function () {};

/**
 * select2
 */
// 預設值
$.fn.select2.defaults.set("allowClear", true);
$.fn.select2.defaults.set("theme", "bootstrap");
$.fn.select2.defaults.set("placeholder", "請選擇");
$.fn.select2.defaults.set("language", Select2Language);

/**
 * datatable
 */
// 預設值
$.extend($.fn.dataTable.defaults, {
    language: DatatableLanguage,
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
    disableMobile: "true",
});

window.moment = moment;
window.camelcaseKeys = camelcaseKeys;
window.snakecaseKeys = snakecaseKeys;

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

Vue.use(VueAffix);
Vue.use(VueScrollactive);

Vue.use(VueSweetalert2, {
    showConfirmButton: true,
    confirmButtonText: "確定",
    confirmButtonColor: "#5cb85c",
    cancelButtonText: "取消",
    cancelButtonColor: "#d9534f",
    allowOutsideClick: false,
    allowEscapeKey: false,
});
// 設定sweetalert2 toast
window.Toast = Vue.swal.mixin({
    toast: true,
    position: "bottom-end",
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener("mouseenter", Vue.swal.stopTimer);
        toast.addEventListener("mouseleave", Vue.swal.resumeTimer);
    },
});

Vue.use(VueModal);
Vue.use(FloatingVue);

Vue.component("draggable", VueDraggable);
Vue.component("collapse-transition", CollapseTransition);
