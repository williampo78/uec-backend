<?php

namespace App\Services;

use App\Models\SysConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SysConfigService
{
    /**
     * 取得全部系統設定檔
     *
     * @param array $data
     * @return Collection
     */
    public function getSysConfigs(array $data = []) :Collection
    {
        $configs = SysConfig::where('active', 1)->get();

        return $configs;
    }

    /**
     * 取得單筆系統設定檔
     *
     * @param string $configKey
     * @return Model|null
     */
    public function getConfig(string $configKey): ?Model
    {
        $config = SysConfig::where('active', 1)
            ->where('config_key', $configKey)
            ->first();

        return $config;
    }

    /**
     * 取得系統設定檔的值
     *
     * @param string $configKey
     * @return string|null
     */
    public function getConfigValue(string $configKey): ?string
    {
        $config = SysConfig::where('active', 1)
            ->where('config_key', $configKey)
            ->first();

        $configValue = isset($config) ? $config->config_value : null;

        return $configValue;
    }
}
