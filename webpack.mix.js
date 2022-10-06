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
    .js("resources/js/promotional_campaign/cart/*.js", "public/js/promotional_campaign/cart/main.js")
    .js("resources/js/promotional_campaign/prd/*.js", "public/js/promotional_campaign/prd/main.js")
    .js("resources/js/promotional_campaign/cart_v2/*.js", "public/js/promotional_campaign/cart_v2/main.js")
    .js("resources/js/misc-stock-request/*.js", "public/js/misc-stock-request/main.js")
    .vue({
        version: 2,
        extractStyles: true,
    })
    .sourceMaps(true, "source-map");

// 檔案路徑別名
mix.alias({
    '@': path.join(__dirname, 'resources/js'),
    '@components': path.join(__dirname, 'resources/js/components'),
    "@plugins": path.join(__dirname, "resources/js/plugins"),
});

if (mix.inProduction()) {
    mix.version();
}
