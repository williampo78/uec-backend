<?php

namespace App\Services;

use App\Models\SysConfig;

class SysConfigService
{
    public function getSysConfigs()
    {
        $result = SysConfig::get();

        return $result;
    }
}
