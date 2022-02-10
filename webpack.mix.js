const mix = require("laravel-mix");

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

mix.postCss("resources/css/app.css", "public/css", [
    //
]);

mix.js("resources/js/app.js", "public/js")
    .js("resources/js/advertisement/main.js", "public/js/advertisement")
    .js("resources/js/advertisement/validate.js", "public/js/advertisement");

mix.copyDirectory(
    "node_modules/datatables.net-plugins/i18n",
    "public/datatables.net-plugins/i18n"
).copy(
    "node_modules/jquery-validation/dist/localization/*.min.js",
    "public/jquery-validation/dist/localization"
);
