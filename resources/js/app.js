import "./bootstrap";
import "./my-jquery-validate-methods";
import i18n from "./i18n";

import Select2 from "@components/Select2.vue";
import VueFlatPickr from "@components/VueFlatPickr.vue";
import VueTreeselect from "@components/VueTreeselect.vue";
import BaseModal from "@components/BaseModal.vue";

import Format from "@plugins/format.js";

// selec2 共用
$(".select2-default").select2();

// ajax
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

Vue.component("select2", Select2);
Vue.component("vue-flat-pickr", VueFlatPickr);
Vue.component("treeselect", VueTreeselect);
Vue.component("base-modal", BaseModal);

Vue.use(Format);

window.i18n = i18n;
