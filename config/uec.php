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

    // 版位類型，I：圖檔(image)、II：母子圖檔(image+image)、T：文字(text)、S：商品、IS：圖檔+商品、X：非人工上稿
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
];
