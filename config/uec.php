<?php
return [

    /**
     *   web_category_hierarchy_levels 分類開關
     *   指定為2時，中分類清單不顯示﹝展小類﹞的按鈕
     *   指定為3時，中分類清單顯示﹝展小類﹞的按鈕
     */
    'web_category_hierarchy_levels' => env('UEC_CATEGORY_LEVEL','2'),

    'config_key' => 'EC_WAREHOUSE_GOODS',

    /*
     * 預設為測試環境
     *
     */
    'isTesting' => env('UEC_TEST', 'true'),
    'mailPrefix' => env('MAIL_PREFIX', '[ 電商測試 ]'),
    'mailFrom' => env('MAIL_FROM_ADDRESS'),
    'mailTo' => env('MAIL_TO_ADDRESS') ? array_filter(explode(',', env('MAIL_TO_ADDRESS'))) : [],
    'swithBackendUrl' => env('SWITCH_BACKEND_URL'),
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
    'active_options' => [
        1 => '啟用',
        0 => '關閉',
    ],

    //  狀態2
    'active2_options' => [
        1 => '生效',
        0 => '失效',
    ],

    // 上下架狀態
    'launch_status_options' => [
        'prepare_to_launch' => '待上架',
        'launched' => '已上架',
        'no_launch' => '下架',
        'disabled' => '關閉',
    ],

    // 商品類型
    'product_type_options' => [
        'N' => '一般品',
        'G' => '贈品',
        'A' => '加購品',
    ],

    // 庫存類型
    'stock_type_options' => [
        'A' => '買斷',
        'B' => '寄售',
        'T' => '轉單',
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
    'payment_method_options' => [
        'TAPPAY_CREDITCARD' => '信用卡一次付清',
        'TAPPAY_LINEPAY' => 'LINE Pay',
        'TAPPAY_JKOPAY' => '街口支付',
    ],

    // 物流方式
    'lgst_method_options' => [
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
        'CANCELLED' => '已取消',
        'VOIDED' => '已作廢',
    ],

    // 發票用途
    'invoice_usage_options' => [
        'P' => '個人電子發票',
        'D' => '發票捐贈',
        'C' => '公司戶電子發票',
    ],

    // 載具類型
    'carrier_type_options' => [
        '1' => '商城線上載具',
        '2' => '自然人憑證載具',
        '3' => '手機條碼載具',
    ],

    // 課稅類別
    'tax_type_options' => [
        'TAXABLE' => '應稅',
        'ZERO_RATED' => '零稅率',
        'NON_TAXABLE' => '免稅',
    ],

    // 金流類型
    'payment_type_options' => [
        'PAY' => '請款',
        'REFUND' => '退款',
    ],

    // 金流狀態
    'payment_status_options' => [
        'NA' => '不須處理',
        'PENDING' => '待請款 / 待退款',
        'COMPLETED' => '請款成功 / 退款成功',
        'FAILED' => '請款失敗 / 退款失敗',
        'VOIDED' => '已作廢',
    ],

    // 金流狀態-請款
    'payment_pay_status_options' => [
        'NA' => '不須處理',
        'PENDING' => '待請款',
        'COMPLETED' => '請款成功',
        'FAILED' => '請款失敗',
        'VOIDED' => '已作廢',
    ],

    // 金流狀態-退款
    'payment_refund_status_options' => [
        'NA' => '不須處理',
        'PENDING' => '待退款',
        'COMPLETED' => '退款成功',
        'FAILED' => '退款失敗',
        'VOIDED' => '已作廢',
    ],

    // 活動階層
    'campaign_level_code_options' => [
        'PRD' => '單品',
        'CART' => '滿額',
        'CART_P' => '購物車滿額',
    ],

    // 物流公司
    'lgst_company_code_options' => [
        'CHOICE' => '秋雨',
    ],

    'tax_option' => [
        '0' => '免稅',
        // 1 => '應稅',
        '2' => '應稅內含',
        '3' => '零稅率',
    ],

    //退貨申請單狀態
    'return_request_status_options' => [
        'CREATED' => '新建立',
        'VOIDED' => '已作廢',
        'PROCESSING' => '處理中',
        'COMPLETED' => '退貨完成',
        'FAILED' => '退貨失敗',
    ],

    //orders狀態
    'order_refund_status_options' => [
        'PENDING' => '待退款',
        'COMPLETED' => '退款成功',
        'FAILED' => '退款失敗',
        'VOIDED' => '已作廢',
    ],

    //order_payments資料新增原因
    'order_payment_record_created_reason' => [
        'ORDER_CREATED' => '訂單成立',
        'ORDER_CANCELLED' => '訂單取消',
        'RETURNED' => '銷退',
        'ORDER_VOIDED' => '訂單作廢',
    ],

    // 供應商合約狀態
    'supplier_contract_status_code_options' => [
        'CREATED' => '未啟動',
        'PROCESSING' => '用印中',
        'APPROVED' => '已合作',
        'EXPIRED' => '已過期',
    ],
];
