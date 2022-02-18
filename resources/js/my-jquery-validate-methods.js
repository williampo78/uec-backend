// 驗證密碼格式
jQuery.validator.addMethod(
    "passwordCheck",
    function (value, element, params) {
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/.test(value);
    },
    "密碼格式錯誤"
);

// 比較輸入的兩個日期或兩個數字
jQuery.validator.addMethod(
    "greaterThan",
    function (value, element, params) {
        if (!/Invalid|NaN/.test(new Date(value))) {
            return new Date(value) > new Date(params);
        }

        return (
            (isNaN(value) && isNaN(params)) || Number(value) > Number(params)
        );
    },
    "必須大於 {0}"
);
// 比較輸入的兩個日期或兩個數字
jQuery.validator.addMethod(
    "greaterSameThan",
    function (value, element, params) {
        if (!/Invalid|NaN/.test(new Date(value))) {
            return new Date(value) >= new Date(params);
        }
        return (
            (isNaN(value) && isNaN(params)) || Number(value) > Number(params)
        );
    },
    "必須大於 {0}"
);
// 比較輸入的日期和當前時間
jQuery.validator.addMethod(
    "dateGreaterThanNow",
    function (value, element, params) {
        return new Date(value) > new Date();
    },
    "必須大於目前時間"
);

// 驗證欄位是否重複
jQuery.validator.addMethod(
    "unique",
    function (value, element, params) {
        var selector = jQuery.validator.format(
            "{0}[name!='{1}']",
            params,
            element.name
        );
        var matches = new Array();

        $(selector).each(function (index, item) {
            if (value == $(item).val()) {
                matches.push(item);
            }
        });

        return matches.length == 0;
    },
    "不可輸入重複的內容"
);

// 驗證檔案大小限制
jQuery.validator.addMethod(
    "filesize",
    function (value, element, param) {
        let filesize = 0;

        if (element.files[0]) {
            filesize = element.files[0].size;

            switch (param[1]) {
                case "KB":
                    filesize = filesize / 1024;
                    break;

                case "MB":
                    filesize = filesize / 1024 / 1024;
                    break;

                case "GB":
                    filesize = filesize / 1024 / 1024 / 1024;
                    break;
            }
        }

        return this.optional(element) || filesize <= param[0];
    },
    function (params, element) {
        if (params[1]) {
            return `檔案大小不可超過${params[0]}${params[1]}`;
        }

        return `檔案大小不可超過${params[0]}Bytes`;
    }
);

/**
 *  let obj  = {
        startTime :$('#trade_date_start').val(),
        endTime : $('#trade_date_end').val() ,
        monthNum : 6 ,
    }
 */
jQuery.validator.addMethod(
    "monthIntervalVerify",
    function (value, element, obj) {
        if (!obj.isExecution) {
            return true;
        }
        let startTime = new Date(obj.startTime);
        let endTime = new Date(obj.endTime);
        let startEndRange = endTime - startTime;
        let nowTime = new Date();
        let RangeTime = new Date();
        let setTime = RangeTime - nowTime;
        console.log(startEndRange , setTime)
        return startEndRange >= setTime;
    },
    function (params, element) {
        console.log(element);
        return `起訖最多不可超過 ${params.monthNum} 個月`;
    }
);
// 比較大於等於時間
jQuery.validator.addMethod(
    "dateGreaterEqualThan",
    function (value, element, params) {
        if(!params.depends){
            return true ;
        }
        if (!/Invalid|NaN/.test(new Date(value))) {
            return new Date(value) >= new Date(params.date);
        }
        return (
            (isNaN(value) && isNaN(params.date)) || Number(value) > Number(params.date)
        );
    },
    "必須大於 {0}"
);

jQuery.validator.addMethod(
    "notRepeating",
    function (value, element, params) {
        var fund = $(element).data("va");
        var selector = jQuery.validator.format(
            "[name!='{0}'][data-va='" + fund + "']",
            element.name
        );
        var matches = new Array();
        $(selector).each(function (index, item) {
            if (value == $(item).val()) {
                matches.push(item);
            }
        });
        return matches.length == 0;
    },
    function (params, element) {
        return `已跟其他輸入的欄位重複`;
    }
);

jQuery.validator.addMethod(
    "isTWCompanyNumber",
    function (value, element, obj) {
        var regexp = /^[0-9]{8}$/;
        return regexp.test(obj.number);
    },
    function (params, element) {
        return `統一編號必須為8碼、只能輸入數字`;
    }
);

jQuery.validator.addMethod(
    "isEnglishNumber",
    function (value, element, obj) {
        var regexp = /^[A-Za-z0-9]+$/;
        return regexp.test(obj.number);
    },
    function (params, element) {
        return `只能輸入英文以及數字`;
    }
);
jQuery.validator.addMethod(
    "notChinese",
    function (value, element, obj) {
        var regexp =/.*[\u4e00-\u9fa5]+.*$/;
        if (regexp.test(obj.text)) {//中文不給過
            return false
          } else {
            return true
          }
    },
    function (params, element) {
        return `不能輸入中文`;
    }
);
