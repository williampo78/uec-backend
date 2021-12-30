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
    let startTime = new Date(obj.start_time);
    let endTime = new Date(obj.end_time);
    endTime.setMonth(endTime.getMonth() - obj.month_num);
    return startTime > endTime;
}, function (params, element) {
    return `起訖最多不可超過 ${params.month_num} 個月`;
});