// 密碼格式驗證
jQuery.validator.addMethod("passwordCheck", function (value, element, params) {
    return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/.test(value);
}, '密碼格式錯誤');

// 比較輸入的兩個日期或兩個數字
jQuery.validator.addMethod("greaterThan", function (value, element, params) {
    if (!/Invalid|NaN/.test(new Date(value))) {
        return new Date(value) > new Date(params);
    }

    return isNaN(value) && isNaN(params) ||
        (Number(value) > Number(params));
}, '必須大於 {0}');

// 比較輸入的日期和當前時間
jQuery.validator.addMethod("dateGreaterThanNow", function (value, element, params) {
    return new Date(value) > new Date();
}, '必須大於目前時間');
/**
 *  let obj  = {
        startTime :$('#trade_date_start').val(),
        endTime : $('#trade_date_end').val() , 
        monthNum : 6 ,
    }
 */
jQuery.validator.addMethod("monthIntervalVerify", function (value, element, obj) {
    if(!obj.isExecution){
        return true ;
    }
    let startTime = new Date(obj.startTime);
    let endTime = new Date(obj.endTime);
    let startEndRange = endTime - startTime ; 
    let nowTime = new Date() ;
    let RangeTime = new Date(); 
    let setTime = RangeTime - nowTime ;
    return startEndRange > setTime;
}, function (params, element) {
    console.log(element) ; 
    return `起訖最多不可超過 ${params.monthNum} 個月`;
});
jQuery.validator.addMethod("notRepeating", function (value, element, params) {
    var prefix = params;
    var fund =  $(element).data('va') ;
    var selector = jQuery.validator.format("[name!='{0}'][data-va='"+fund+"']", element.name, prefix);
    var matches = new Array();
    $(selector).each(function(index, item) {
        if (value == $(item).val()) {
            matches.push(item);
        }
    });
    return matches.length == 0;
}, function (params, element) {
    return `已跟其他輸入的欄位重複`;
});

jQuery.validator.addMethod("isTWCompanyNumber", function (value, element, obj) {
    var regexp = /^[0-9]{8}$/;
    return regexp.test(obj.number);
}, function (params, element) {
    return `統一編號必須為8碼、只能輸入數字`;
});
jQuery.validator.addMethod("isEnglishNumber", function (value, element, obj) {
    var regexp = /^[A-Za-z0-9]+$/
    return regexp.test(obj.number);
}, function (params, element) {
    return `只能輸入英文以及數字`;
});
// ^[A-Za-z0-9]+$
