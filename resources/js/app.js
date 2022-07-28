import "./bootstrap";
import "./my-jquery-validate-methods";
import Select2 from "@components/Select2.vue";
import VueFlatPickr from "@components/VueFlatPickr.vue";
import VueTreeselect from "@components/VueTreeselect.vue";

// selec2
$(".select2-default").select2();

// ajax
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

/**
 * select2
 */
Vue.component("select2", Select2);

/**
 * vue flatpickr
 */
Vue.component("vue-flat-pickr", VueFlatPickr);

/**
 * vue treeselect
 */
 Vue.component("treeselect", VueTreeselect);
