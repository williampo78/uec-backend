<?php

namespace App\Services;

use App\Models\SysConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SysConfigService
{
    /**
     * 取得系統設定檔
     *
     * @param array $data
     * @return Collection
     */
    public function getSysConfigs(array $data = []) :Collection
    {
        $sysConfigs = SysConfig::where('active', 1)->get();

        return $sysConfigs;
    }

    /**
     * 藉由config_key，取得系統設定檔
     *
     * @param string $configKey
     * @return Model|null
     */
    public function getSysConfigByConfigKey(string $configKey): ?Model
    {
        $sysConfig = SysConfig::where('active', 1)
            ->where('config_key', $configKey)
            ->first();

        return $sysConfig;
    }

    /**
     * 藉由config_key，取得系統設定檔的值
     *
     * @param string $configKey
     * @return string|null
     */
    public function getConfigValueByConfigKey(string $configKey): ?string
    {
        $sysConfig = SysConfig::where('active', 1)
            ->where('config_key', $configKey)
            ->first();

        $configValue = isset($sysConfig) ? $sysConfig->config_value : null;
        
        return $configValue;
    }
}
