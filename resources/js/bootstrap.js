import $ from "jquery";
import Swal from "sweetalert2";
import "select2";
import "datatables.net";
import "datatables.net-bs";
import "datatables.net-buttons";
import "datatables.net-buttons-bs";
import "jquery-validation";
import "jquery-validation/dist/additional-methods"
import "jquery-validation/dist/localization/messages_zh_TW";

window._ = require("lodash");

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

window.axios = require("axios");

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

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
