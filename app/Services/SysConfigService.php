<?php

namespace App\Services;

use App\Models\SysConfig;
use Illuminate\Database\Eloquent\Collection;

class SysConfigService
{
    /**
     * 取得系統設定檔
     *
     * @param array $datas
     * @return Collection
     */
    public function getSysConfigs(array $datas = []) :Collection
    {
        $sys_configs = SysConfig::where('active', 1)->get();

        return $sys_configs;
    }
}
