<?php
return [

    /**
     *   web_category_hierarchy_levels 分類開關
     *   指定為2時，中分類清單不顯示﹝展小類﹞的按鈕
     *   指定為3時，中分類清單顯示﹝展小類﹞的按鈕
     */
    'web_category_hierarchy_levels' => '2',

    /*
     * 預設為測試環境
     *
     */
    'isTesting' => env('UEC_TEST', 'true'),
    'mailPrefix' => "[ 電商測試 ]",

    // 版位類型
    'ad_slot_type_option' => [
        'I' => '圖檔',
        'II' => '母子圖檔',
        'T' => '文字',
        'S' => '商品',
        'IS' => '圖檔 + 商品',
        'X' => '非人工上稿',
    ],

    //  狀態
    'active_option' => [
        1 => '啟用',
        0 => '關閉',
    ],

    //  狀態2
    'active2_option' => [
        1 => '生效',
        0 => '失效',
    ],

    // 商品類型
    'product_type_option' => [
        'N' => '一般品',
        'G' => '贈品',
        'A' => '加購品',
    ],

    // 訂單狀態
    'order_status_code_options' => [
        'CREATED' => '新建立',
        'PROCESSING' => '處理中',
        'SHIPPED' => '已出貨',
        'CLOSED' => '已結案',
        'REFUSED' => '客拒收 / 客未取',
        'CANCELLED' => '已取消',
        'VOIDED' => '已作廢',
    ],

    // 訂單付款金流狀態
    'order_pay_status_options' => [
        'PENDING' => '待請款',
        'COMPLETED' => '請款成功',
        'FAILED' => '請款失敗',
        'VOIDED' => '已作廢',
    ],

    // 付款方式
    'order_payment_method_options' => [
        'TAPPAY_CREDITCARD' => '信用卡一次付清',
        'TAPPAY_LINEPAY' => 'LINE Pay',
    ],

    // 物流方式
    'order_lgst_method_options' => [
        'HOME' => '宅配',
        'FAMILY' => '全家',
    ],

    // 訂單明細身分
    'order_record_identity_options' => [
        'M' => '主商品',
        'G' => '贈品',
        'A' => '加購品',
    ],

    // 出貨單狀態
    'shipment_status_code_options' => [
        'CREATED' => '新建立',
        'SHIPPING' => '備貨中',
        'STORE_ARRIVED' => '已到店',
        'SHIPPED' => '已出貨',
        'DELIVERED' => '已配達',
        'REFUSED' => '客拒收 / 客未取',
        'VOIDED' => '已作廢',
    ],

    'tax_option' => [
        '0' => '免稅',
        // 1 => '應稅',
        '2' => '應稅內含',
        '3' => '零稅率',
    ],
];
