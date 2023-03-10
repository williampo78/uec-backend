<?php

namespace App\Enums;

final class ApiResponseErrorsEnum
{
    const CHECKOUT_CODE = [
        'payment_method'    => 1001,
        'tappay_prime'      => 1002,
        'lgst_method'       => 1003,
        'store_no'          => 1004,
        'invoice.usage'     => 1005,
        'invoice.carrier_type' => 1006,
        'invoice.carrier_no' => 1007,
        'invoice.donated_code' => 1008,
        'invoice.buyer_gui_number' => 1009,
        'invoice.buyer_title' => 1010,
        'buyer.name'        => 1011,
        'buyer.mobile'      => 1012,
        'buyer.email'       => 1013,
        'buyer.zip'         => 1014,
        'buyer.city'        => 1015,
        'buyer.district'    => 1016,
        'buyer.address'     => 1017,
        'receiver.name'     => 1018,
        'receiver.mobile'   => 1020,
        'receiver.zip'      => 1021,
        'receiver.city'     => 1022,
        'receiver.district' => 1023,
        'receiver.address'  => 1024,
        'total_price'       => 1025,
        'cart_campaign_discount' => 1026,
        'point_discount'    => 1027,
        'shipping_fee'      => 1028,
        'points'            => 1029,
        'utm.source'        => 1030,
        'utm.medium'        => 1031,
        'utm.campaign'      => 1032,
        'utm.content'       => 1033,
        'utm.time'          => 1034,
        'stock_type'        => 1035,
        'installment_info.bank_id' => 1036,
        'installment_info.number_of_installments' => 1037,
        'installment_info.fee_of_installments' => 1038,
        'buyer_remark'      => 1039,
        'utm.term'       => 1040,
    ];

}
