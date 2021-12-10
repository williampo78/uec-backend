<?php
return [

    /**
     *   web_category_hierarchy_levels 分類開關
     *   指定為2時，中分類清單不顯示﹝展小類﹞的按鈕
     *   指定為3時，中分類清單顯示﹝展小類﹞的按鈕
     */
    'web_category_hierarchy_levels' => '3',

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
];
