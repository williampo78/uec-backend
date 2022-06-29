const mix = require("laravel-mix");
const path = require('path');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

// 編譯css
mix.postCss("resources/css/app.css", "public/css")
    .postCss("resources/css/advertisement.css", "public/css");

// 編譯js
mix.js("resources/js/app.js", "public/js")
    .js("resources/js/advertisement/main.js", "public/js/advertisement.js")
    .js("resources/js/external_inventory_daily_report/*.js", "public/js/external_inventory_daily_report.js")
    .js("resources/js/inventory/*.js", "public/js/inventory.js")
    .js("resources/js/order/*.js", "public/js/order.js")
    .js("resources/js/order_payments_report/*.js", "public/js/order_payments_report.js")
    .js("resources/js/order_refund/*.js", "public/js/order_refund.js")
    .js("resources/js/promotional_campaign/cart/*.js", "public/js/promotional_campaign/cart/main.js")
    .js("resources/js/promotional_campaign/prd/*.js", "public/js/promotional_campaign/prd/main.js")
    .js("resources/js/promotional_campaign/cart_v2/*.js", "public/js/promotional_campaign/cart_v2/main.js")
    .js("resources/js/misc-stock-request/*.js", "public/js/misc-stock-request/main.js")
    .vue();

mix.copyDirectory(
    "node_modules/datatables.net-plugins/i18n",
    "public/datatables.net-plugins/i18n"
).copy(
    "node_modules/jquery-validation/dist/localization/*.min.js",
    "public/jquery-validation/dist/localization"
);

// 檔案路徑別名
mix.alias({
    '@': path.join(__dirname, 'resources/js'),
    '@components': path.join(__dirname, 'resources/js/components'),
});
