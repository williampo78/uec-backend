<?php
return [
    'url'=>env('TAP_PAY_API_URI'),

    'partner_key' => env('TAP_PAY_PARTNER_KEY'),

    // TapPay 收款商家
    'merchant_id' => [
        'TAPPAY_CREDITCARD' => 'uarktech_CTBC',
        'TAPPAY_LINEPAY' => 'uarktech_LINEPAY'
    ],

    //銀行對帳系統交易明細
    'details' => env('TAP_PAY_DETAILS'),

    'frontend_redirect_url'=>env('TAP_PAY_RESULT_URL'),
    'backend_notify_url'=>env('TAP_PAY_NOTIFY_URL'),
];
