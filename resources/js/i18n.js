import VueI18n from "vue-i18n";
import dateTimeFormats from "@/locales/date-time-formats";
import numberFormats from "@/locales/number-formats";

Vue.use(VueI18n);

function loadLocaleMessages() {
    const locales = require.context(
        "./locales",
        true,
        /[A-Za-z0-9-_,\s]+\.json$/i
    );
    const messages = {};
    locales.keys().forEach((key) => {
        const matched = key.match(/([A-Za-z0-9-_]+)\/([A-Za-z0-9-_]+)\./i);
        if (matched && matched.length > 1) {
            const locale = matched[1];
            const file = matched[2];
            messages[locale] = { [file]: locales(key) };
        }
    });

    return messages;
}

export default new VueI18n({
    locale: "zh-TW",
    fallbackLocale: "zh-TW",
    messages: loadLocaleMessages(),
    dateTimeFormats,
    numberFormats,
});
