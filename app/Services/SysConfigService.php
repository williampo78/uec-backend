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

    /**
     * 藉由config_key，取得系統設定檔
     *
     * @param string $config_key
     * @return object|null
     */
    public function getSysConfigByConfigKey(string $config_key): ?object
    {
        $sys_config = SysConfig::where('active', 1)
            ->where('config_key', $config_key)
            ->first();

        return $sys_config;
    }
}
