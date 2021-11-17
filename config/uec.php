<?php
return array(

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
);
