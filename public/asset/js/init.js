$(".select2-default").select2({
    allowClear: true,
    theme: "bootstrap",
    placeholder: "請選擇"
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
