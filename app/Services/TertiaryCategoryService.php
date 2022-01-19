<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TertiaryCategoryService
{
    /**
     * 取得列表資料
     * @return \Illuminate\Support\Collection
     * @Author: Eric
     * @DateTime: 2022/1/19 上午 10:11
     */
    public function getIndex()
    {
        return DB::table('tertiary_categories as tc')
            ->select(['tc.id', 'pc.name as pc_name', 'c.name as c_name', 'tc.name as tc_name', 'tc.number as tc_number'])
            ->join('category as c', 'tc.category_id', '=', 'c.id')
            ->join('primary_category as pc', 'c.primary_category_id', '=', 'pc.id')
            ->where('tc.agent_id', Auth()->user()->agent_id)
            ->where('tc.active', 1)
            ->get();
    }

    /**
     * 取得大分類和中分類
     * @return \Illuminate\Support\Collection
     * @Author: Eric
     * @DateTime: 2022/1/19 上午 10:11
     */
    public function getPrimaryCategoryAndCategory()
    {
        return DB::table('category as c')
            ->select(['c.id as c_id', 'c.name as c_name', 'pc.name as pc_name'])
            ->join('primary_category as pc', 'pc.id', '=', 'c.primary_category_id')
            ->get();
    }

}
