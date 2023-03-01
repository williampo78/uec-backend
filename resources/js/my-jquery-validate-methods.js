// 驗證密碼格式
jQuery.validator.addMethod(
    "drowssapCheck",
    function (value, element, params) {
        return /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d\!\@\#\$\%\^\&\*\(\)\.\-\=\_\~]{8,20}$/.test(
            value
        );
    },
    "需包含英文和數字，且介於8~20個字元，符號可輸入：!@#$%^&*().-=_~"
);

// 比較日期
jQuery.validator.addMethod(
    "compareDates",
    function (date1, element, params) {
        let defaultParams = {
            date2: moment(),
            sign: ">",
            unit: "",
        };
        let result;
        let errorMessage = "";

        params = Object.assign({}, defaultParams, params);

        switch (params.sign) {
            case "<":
                result = moment(date1).isBefore(params.date2, params.unit);
                errorMessage = `必需小於`;
                break;

            case "<=":
                result = moment(date1).isSameOrBefore(
                    params.date2,
                    params.unit
                );
                errorMessage = `必需小於等於`;
                break;

            case "=":
                result = moment(date1).isSame(params.date2, params.unit);
                errorMessage = `必需等於`;
                break;

            case ">":
                result = moment(date1).isAfter(params.date2, params.unit);
                errorMessage = `必需大於`;
                break;

            case ">=":
                result = moment(date1).isSameOrAfter(params.date2, params.unit);
                errorMessage = `必需大於等於`;
                break;
        }

        $.validator.messages.compareDates = `${errorMessage} ${moment(
            params.date2
        ).format("YYYY-MM-DD HH:mm:ss")}`;

        return result;
    },
    $.validator.messages.compareDates
);

// 比較輸入值
jQuery.validator.addMethod(
    "compareValues",
    function (value1, element, params) {
        let defaultParams = {
            value2: 0,
            sign: ">",
            dataType: "number",
        };
        let result;
        let errorMessage = "";

        params = Object.assign({}, defaultParams, params);

        switch (params.dataType) {
            case "string":
                value1 = String(value1);
                params.value2 = String(params.value2);
                break;

            default:
                value1 = Number(value1);
                params.value2 = Number(params.value2);
                break;
        }

        switch (params.sign) {
            case "<":
                result = value1 < params.value2;
                errorMessage = `必需小於`;
                break;

            case "<=":
                result = value1 <= params.value2;
                errorMessage = `必需小於等於`;
                break;

            case "=":
                result = value1 == params.value2;
                errorMessage = `必需等於`;
                break;

            case ">":
                result = value1 > params.value2;
                errorMessage = `必需大於`;
                break;

            case ">=":
                result = value1 >= params.value2;
                errorMessage = `必需大於等於`;
                break;
        }

        $.validator.messages.compareValues = `${errorMessage} ${params.value2}`;

        return result;
    },
    $.validator.messages.compareValues
);

// 比較輸入值(介於兩值之間)
jQuery.validator.addMethod(
    "betweenValues",
    function (value1, element, params) {
        let defaultParams = {
            valueMin: 0,
            signMin: ">=",
            valueMax: 999999,
            signMax: "<=",
            dataType: "number",
            TypeName: null,
        };
        let result;
        let errorMinMessage = "";
        let errorMaxMessage = "";

        params = Object.assign({}, defaultParams, params);

        switch (params.dataType) {
            case "string":
                value1 = String(value1);
                params.valueMin = String(params.valueMin);
                params.valueMax = String(params.valueMax);
                break;

            default:
                value1 = Number(value1);
                params.valueMin = Number(params.valueMin);
                params.valueMax = Number(params.valueMax);
                break;
        }

        switch (params.signMin) {
            case "=":
                result = value1 == params.valueMin;
                errorMinMessage = `必需等於`;
                break;

            case ">":
                result = value1 > params.valueMin;
                errorMinMessage = `必需大於`;
                break;

            case ">=":
                result = value1 >= params.valueMin;
                errorMinMessage = `必需大於等於`;
                break;
        }

        switch (params.signMax) {
            case "<":
                result = value1 < params.valueMax;
                errorMaxMessage = `必需小於`;
                break;

            case "<=":
                result = value1 <= params.valueMax;
                errorMaxMessage = `必需小於等於`;
                break;

            case "=":
                result = value1 == params.valueMax;
                errorMaxMessage = `必需等於`;
                break;
        }

        if (params.cnMinName && params.cnMaxName) {
            $.validator.messages.betweenValues = `${errorMinMessage} ${params.cnMinName}, ${errorMaxMessage} ${params.cnMaxName}`;
        } else if (params.cnMinName) {
            $.validator.messages.betweenValues = `${errorMinMessage} ${params.cnMinName}`;
        } else if (params.cnMaxName) {
            $.validator.messages.betweenValues = `${errorMaxMessage} ${params.cnMaxName}`;
        } else {
            $.validator.messages.betweenValues = `${errorMinMessage} ${params.valueMin}, ${errorMaxMessage} ${params.valueMax}`;
        }

        return result;
    },
    $.validator.messages.betweenValues
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
    function (value, element, params) {
        let filesize = 0;

        if (element.files[0]) {
            filesize = element.files[0].size;

            switch (params[1]) {
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

        return this.optional(element) || filesize <= params[0];
    },
    function (params, element) {
        if (params[1]) {
            return `檔案大小不可超過${params[0]}${params[1]}`;
        }

        return `檔案大小不可超過${params[0]}Bytes`;
    }
);

/**
 * 驗證圖片寬度
 */
jQuery.validator.addMethod(
    "minImageWidth",
    function (value, element, minWidth) {
        if (element.files.length == 0) {
            return true;
        }

        return ($(element).attr("data-image-width") || 0) >= minWidth;
    },
    function (minWidth, element) {
        return `圖片寬度必需大於等於${minWidth}px`;
    }
);

/**
 * 驗證圖片高度
 */
jQuery.validator.addMethod(
    "minImageHeight",
    function (value, element, minHeight) {
        if (element.files.length == 0) {
            return true;
        }

        return ($(element).attr("data-image-height") || 0) >= minHeight;
    },
    function (minHeight, element) {
        return `圖片寬度必需大於等於${minHeight}px`;
    }
);

/**
 * 驗證圖片比例
 */
jQuery.validator.addMethod(
    "imageRatio",
    function (value, element, params) {
        if (element.files.length == 0) {
            return true;
        }

        let width = $(element).attr("data-image-width");
        let height = $(element).attr("data-image-height");
        let ratio = width / height;
        let ratioLimit = params[0] / params[1];

        return ratio == ratioLimit;
    },
    function (params, element) {
        return `圖片比例須為${params[0]}:${params[1]}`;
    }
);

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
        console.log(startEndRange, setTime);
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
        if (!params.depends) {
            return true;
        }
        if (!/Invalid|NaN/.test(new Date(value))) {
            return new Date(value) >= new Date(params.date);
        }
        return (
            (isNaN(value) && isNaN(params.date)) ||
            Number(value) > Number(params.date)
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

// 驗證統一編號格式
jQuery.validator.addMethod(
    "isGUINumber",
    function (value, element, params) {
        let regex = /^[0-9]{8}$/;

        return regex.test(value);
    },
    "統一編號必須為8個數字"
);

// 驗證手機號碼格式
jQuery.validator.addMethod(
    "isCellPhoneNumber",
    function (value, element, params) {
        let regex = /^[0-9]{10}$/;

        return regex.test(value);
    },
    "手機號碼必須為10個數字"
);

// 驗證英文、數字格式
jQuery.validator.addMethod(
    "isAlphaNumeric",
    function (value, element, params) {
        let regex = /^[A-Za-z0-9]+$/;

        return regex.test(value);
    },
    "只能輸入英文及數字"
);

jQuery.validator.addMethod(
    "notChinese",
    function (value, element, obj) {
        var regexp = /.*[\u4e00-\u9fa5]+.*$/;
        if (regexp.test(obj.text)) {
            //中文不給過
            return false;
        } else {
            return true;
        }
    },
    function (params, element) {
        return `不能輸入中文`;
    }
);

// 驗證英文、數字、下底線、連字號格式
jQuery.validator.addMethod(
    "isAlphaNumericUnderscoreHyphen",
    function (value, element, params) {
        let regex = /^[a-zA-Z0-9-_]+$/;

        return regex.test(value);
    },
    "只能輸入英文、數字、下底線( _ )、連字號( - )"
);

// 驗證發票號碼格式
jQuery.validator.addMethod(
    "isInvoiceNumber",
    function (value, element, params) {
        let regex = /^[A-Z]{2}[0-9]{8}$/;

        return regex.test(value);
    },
    "必須為前2碼大寫英文、後8碼數字"
);

jQuery.validator.addMethod(
    "notOnlyZero",
    function (value, element, params) {
        if(params){
            return this.optional(element) || parseInt(value) > 0;
        }else{
            return true ;
        }
    },
    "不能為 0"
);

jQuery.validator.addMethod(
    "needZero",
    function (value, element, params) {
        if(params){
            return value == 0;
        }else{
            return true ;
        }
    },
    "只能為 0"
);
