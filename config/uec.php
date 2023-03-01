<?php
return [

    /**
     *   web_category_hierarchy_levels 分類開關
     *   指定為2時，中分類清單不顯示﹝展小類﹞的按鈕
     *   指定為3時，中分類清單顯示﹝展小類﹞的按鈕
     */
    'web_category_hierarchy_levels' => env('UEC_CATEGORY_LEVEL', '2'),
    'batch_upload_path'=>env('BATCH_UPLOAD_PATH','uploads/batch/'),
    'batch_upload_log_path'=>env('BATCH_UPLOAD_LOG_PATH','log/batchLog/'),
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
        'TAPPAY_INSTAL'=>'分期付款',
        'TAPPAY_JKOPAY' => '街口支付',
    ],
    //付款方式有的預設鎖死不能更改
    'payment_method_options_lock' => [
        'TAPPAY_CREDITCARD' => true, //信用卡一次付清
        'TAPPAY_LINEPAY' => false,//LINE Pay
        'TAPPAY_INSTAL' => false,//分期付款
        'TAPPAY_JKOPAY' => false,//街口支付
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
        'CREATED' => '接獲訂單',
        'SHIPPING' => '備貨中',
        'STORE_ARRIVED' => '已到店',
        'SHIPPED' => '出貨中',
        'DELIVERED' => '已到貨',
        'REFUSED' => '客拒收 / 客未取',
        'CANCELLED' => '已取消',
        'VOIDED' => '已作廢',
        'M_CLOSED' => '異常結案',
    ],

    // 訂單類型
    'order_ship_from_whs_options' => [
        'SELF' => '商城出貨',
        'SUP' => '供應商出貨',
    ],

    // 資料範圍
    'data_range_options' => [
        'SHIPPED_AT_NULL' => '所有未出貨訂單',
        'DELIVERED_AT_NULL' => '所有未配達訂單',
    ],

    // 退貨成功-明細類型
    'data_type_options' => [
        'PRD' => '銷退',
        'CAMPAIGN' => '折扣加收',
        'INSTAL_FEE' => '分期付款手續費',
    ],

    // 退貨成功-備註
    'return_remark_options' => [
        '0' => '',
        '1' => '此退貨有經過協商',
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
        'PROCESSING' => '處理中',
        'COMPLETED' => '退貨完成',
        'VOIDED' => '已作廢',
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

    // 各種選項
    'options' => [
        // 狀態
        'actives' => [
            // 類型1
            'type1' => [
                0 => '關閉',
                1 => '啟用',
            ],
            // 類型2
            'type2' => [
                0 => '失效',
                1 => '生效',
            ],
        ],

        // 庫存申請單
        'misc_stock_requests' => [
            // 申請單狀態
            'request_statuses' => [
                // 入庫
                'in' => [
                    'DRAFTED' => '草稿',
                    'REVIEWING' => '審核中',
                    'COMPLETED' => '審核完成',
                    'CLOSED' => '已入庫',
                ],
                // 出庫
                'out' => [
                    'DRAFTED' => '草稿',
                    'REVIEWING' => '審核中',
                    'COMPLETED' => '審核完成',
                    'CLOSED' => '已出庫',
                ],
            ],
            // 供應商申請狀態
            'status_codes' => [
                // 入庫
                'in' => [
                    'DRAFTED' => '草稿',
                    'REVIEWING' => '審核中',
                    'APPROVED' => '已核准',
                    'REJECTED' => '已駁回',
                    'CLOSED' => '已入庫',
                ],
                // 出庫
                'out' => [
                    'DRAFTED' => '草稿',
                    'REVIEWING' => '審核中',
                    'APPROVED' => '已核准',
                    'REJECTED' => '已駁回',
                    'CLOSED' => '已出庫',
                ],
            ],
        ],

        // 稅別
        'taxes' => [
            '0' => '免稅',
            // '1' => '應稅',
            '2' => '應稅內含',
            '3' => '零稅率',
        ],

        // 簽核結果
        'review_results' => [
            'APPROVE' => '核准',
            'REJECT' => '駁回',
        ],

        // 存放溫層
        'storage_temperatures' => [
            'NORMAL' => '常溫',
            'AIR' => '空調',
            'CHILLED' => '冷藏',
        ],
    ],

    //購物車滿額折扣，攤提回單品計算
    'cart_p_discount_split' => 1,

    //異動類型
    'transaction_type' => [
        'PO_RCV' => '採購進貨',
        'PO_RTV' => '採購退貨',
        'ORDER_SHIP' => '訂單出庫',
        'ORDER_CANCEL' => '訂單取消',
        'ORDER_VOID' => '訂單作廢',
        'ORDER_RTN' => '訂單銷退',
        'MISC_REC' => '其他入庫',
        'MISC_ISSUE' => '其他出庫',
    ],

    //來源單據
    'source_table_name' => [
        'purchase_detail' => [
            'chinese_name' => '進貨單',
            'transaction_type' => null
        ],
        'misc_stock_request_details' => [
            'chinese_name' => '進貨退出單',
            'transaction_type' => 'PO_RTV'
        ],
        'buyout_stock_req_details' => [
            'chinese_name' => '寄售入庫申請單',
            'transaction_type' => 'MISC_REC'
        ],
        'sup_req_cs_stock_details' => [
            'chinese_name' => '寄售退倉申請單',
            'transaction_type' => 'MISC_ISSUE'
        ],
        'sup_req_stock_details' => [
            'chinese_name' => '轉單庫存異動申請單',
            'transaction_type' => null
        ],
        'order_details' => [
            'chinese_name' => '訂單',
            'transaction_type' => null
        ],
        'return_request_details' => [
            'chinese_name' => '退貨申請單',
            'transaction_type' => null
        ],
    ],

    //檢驗單狀態
    'return_examination_status_codes' => [
        'CREATED'        => '接獲申請',
        'VOIDED'         => '已作廢',
        'DISPATCHED'     => '派車回收',
        'COMPLETED'      => '檢驗完成',
        'FAILED'         => '檢驗異常',
        'NEGO_COMPLETED' => '完成協商',
        'CLOSED'         => '已結案',
        'M_CLOSED'       => '異常結案',
    ],

    //購物車拆車結帳
    'cart_billing_split' => 1,
];
