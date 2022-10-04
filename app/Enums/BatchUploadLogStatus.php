<?php

namespace App\Enums;

final class BatchUploadLogStatus
{
    const STATUS_DEFAULT = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_FAILED = 2;

    const STATUS_ARR = [
        self::STATUS_DEFAULT    =>  '待執行',
        self::STATUS_COMPLETED  =>  '已完成',
        self::STATUS_FAILED     =>  '已失敗'
    ];
}
