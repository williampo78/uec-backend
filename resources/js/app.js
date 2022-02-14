import "./bootstrap";
import "./my-jquery-validate-methods";

// selec2
$(".select2-default").select2();

// ajax
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});
