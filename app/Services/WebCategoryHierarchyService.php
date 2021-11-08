<?php

namespace App\Services;

use App\Models\CategoryHierarchy;
use App\Models\CategoryProducts;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebCategoryHierarchyService
{
    public function __construct()
    {

    }
    /*
     * parent_id = null 取出大分類
     */
    public function web_Category_Hierarchy_Bylevel($parent_id = null)
    {
        return CategoryHierarchy::where('parent_id', $parent_id)->get();
    }
    public function add_Category_Hierarchy($in)
    {
        $now = Carbon::now();
        $agent_id = Auth::user()->agent_id;
        $user_id = Auth::user()->id;
        try {
            DB::beginTransaction();
            $insert['parent_id'] = $in['parent_id']; //父ID
            $insert['category_level'] = $in['category_level']; // 階級
            $insert['category_name'] = $in['category_name'];
            $insert['agent_id'] = $agent_id;
            $insert['sort'] = $this->getSort($in);
            $insert['created_by'] = $user_id;
            $insert['updated_by'] = $user_id;
            $insert['created_at'] = $now;
            $insert['updated_at'] = $now;
            CategoryHierarchy::insert($insert);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e);
            return false;
        }
        return CategoryHierarchy::where('parent_id', $in['parent_id'])->get();
    }
    public function edit_Category_Hierarchy($in)
    {
        $now = Carbon::now();
        $agent_id = Auth::user()->agent_id;
        $user_id = Auth::user()->id;
        try {
            DB::beginTransaction();
            $insert['category_name'] = $in['category_name'];
            $insert['updated_by'] = $user_id;
            $insert['updated_at'] = $now;
            CategoryHierarchy::where('id', $in['id'])->update($insert);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e);
            return false;
        }
        return CategoryHierarchy::where('parent_id', $in['parent_id'])->get();
    }
    public function del_Category_Hierarchy($in)
    {
        $resut = [];
        $CategoryProductsCount = CategoryProducts::where('web_category_hierarchy_id', $in['id'])->get()->count();
        $CategoryHierarchyCount = CategoryHierarchy::where('parent_id', $in['id'])->get()->count();
        $resut['Msg_Hierarchy'] = '';
        $resut['Msg_Products'] = '';
        $resut['Msg'] = '';

        $resut['status'] = true;
        if ($CategoryHierarchyCount !== 0) {
            $resut['Msg_Hierarchy'] = '請將該分類底下的分類清空才能執行該操作';
            $resut['status'] = false;
        }
        if ($CategoryProductsCount !== 0) {
            $resut['Msg_Products'] = '請先將分類階層有使用到該分類的品項刪除,才能執行該操作';
            $resut['status'] = false;
        }
        if ($resut['status']) {
            CategoryHierarchy::where('id', $in['id'])->delete();
            $resut['status'] = true;
            $resut['Msg'] = '刪除成功';
        }
        return $resut;
    }

    public function getSort($in)
    {

        $query = CategoryHierarchy::where('parent_id', $in['parent_id'])
            ->where('category_level', $in['category_level'])
            ->orderBy('sort', 'desc')
            ->first();
        $sort = 0;
        if ($query !== null) {
            $sort = $query->sort += 1;
        }
        return $sort;
    }
}
