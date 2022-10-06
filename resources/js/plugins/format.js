export default {
    install(Vue, options) {
        Vue.prototype.$format = {
            truncate(value, length = 30, suffix = "...") {
                if (!value) {
                    return value;
                }

                if (value.length <= length) {
                    return value;
                }

                return value.substr(0, length) + suffix;
            },
            date(value) {
                if (!value) {
                    return value;
                }

                return moment(value).format("YYYY-MM-DD");
            },
            dateTime(value) {
                if (!value) {
                    return value;
                }

                return moment(value).format("YYYY-MM-DD HH:mm");
            },
        };
    },
};
