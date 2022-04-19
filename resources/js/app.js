import "./bootstrap";
import "./my-jquery-validate-methods";
import vSelect from "./components/VueSelect.vue";

// selec2
$(".select2-default").select2();

// ajax
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

/**
 * vue-select
 */
Vue.component("v-select", vSelect);
