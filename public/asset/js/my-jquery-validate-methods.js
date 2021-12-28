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
    if (obj.endTime !== '') {
        let startTime = new Date(obj.startTime);
        let endTime = new Date(obj.endTime);
        let compare = new Date(obj.endTime);
        compare.setMonth(compare.getMonth() - obj.monthNum);
        return startTime > compare;
    }
}, '起訖最多不可超過6個月');

