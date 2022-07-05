<?php

namespace App\Services;

use App\Models\CategoryHierarchy;
use App\Models\CategoryProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ImageUpload ;
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
        return CategoryHierarchy::where('parent_id', $parent_id)->orderBy('sort', 'ASC')->get();
    }
    public function add_Category_Hierarchy($in,$file)
    {
        $now = Carbon::now();
        $agent_id = Auth::user()->agent_id;
        $user_id = Auth::user()->id;
        DB::beginTransaction();

        try {
            if($in['parent_id'] == 'null'){
                $in['parent_id'] = null ;
            }
            $insert['parent_id'] = $in['parent_id']; //父ID
            $insert['category_level'] = $in['category_level']; // 階級
            $insert['category_name'] = $in['category_name'];
            $insert['content_type'] = $in['content_type'];
            $insert['agent_id'] = $agent_id;
            $insert['sort'] = $this->getSort($in);
            $insert['created_by'] = $user_id;
            $insert['updated_by'] = $user_id;
            $insert['created_at'] = $now;
            $insert['updated_at'] = $now;
            if($in['category_level'] == '1'){
                $insert['category_short_name'] = $in['category_short_name'];
                $insert['gross_margin_threshold'] = $in['gross_margin_threshold'];
            }
            $CategoryHierarchy = CategoryHierarchy::create($insert);
            if(!empty($file)){
                $uploadPath = 'category_icon/'.$CategoryHierarchy->id;
                $uploadFileResult = ImageUpload::uploadImage($file['image'], $uploadPath, 'category_icon');
                CategoryHierarchy::where('id',$CategoryHierarchy->id)->update(['icon_name'=>$uploadFileResult['image']]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e);
            return false;
        }
        $result = new CategoryHierarchy ;
        if(!is_null($in['parent_id'])){
           $result = $result->where('parent_id',$in['parent_id']);
        }
        return $result->get();
    }
    public function edit_Category_Hierarchy($in,$file)
    {
        $now = Carbon::now();
        $agent_id = Auth::user()->agent_id;
        $user_id = Auth::user()->id;
        try {
            DB::beginTransaction();
            if($in['parent_id'] == 'null'){
                $in['parent_id'] = null ;
            }
            $insert['category_name'] = $in['category_name'];
            $insert['content_type'] = $in['content_type'];
            if($in['category_level'] == '1'){
                $insert['category_short_name'] = $in['category_short_name'];
                $insert['gross_margin_threshold'] = $in['gross_margin_threshold'];
            }
            if(!empty($file)){
                $uploadPath = 'category_icon/'.$in['id'];
                $uploadFileResult = ImageUpload::uploadImage($file['image'], $uploadPath, 'category_icon');
                CategoryHierarchy::where('id',$in['id'])->update(['icon_name'=>$uploadFileResult['image']]);
            }
            $insert['updated_by'] = $user_id;
            $insert['updated_at'] = $now;
            CategoryHierarchy::where('id', $in['id'])->update($insert);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e);
            return false;
        }
        return CategoryHierarchy::where('parent_id', $in['parent_id'])->orderBy('sort', 'ASC')->get();
    }
    public function del_Category_Hierarchy($in)
    {
        $resut = [];
        $CategoryProductsCount = CategoryProduct::where('web_category_hierarchy_id', $in['id'])->get()->count();
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
    public function sort_Category_Hierarchy($in)
    {
        $sort = json_decode($in['JsonData'], true);
        foreach ($sort as $key => $val) {
            CategoryHierarchy::where('id', $val['id'])->update(['sort' => $key]);
        }
        return true;
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
    //取得開關分類判斷輸出內容
    public function getCategoryHierarchyContents($input = [])
    {
        $confi_levels = config('uec.web_category_hierarchy_levels');
        $keyword = '';
        $where = '';
        if (isset($input['id']) && $input['id'] !== '') {
            $where .= "AND id = " . $input['id'];
        }
        if (isset($input['active']) && $input['active'] !== '') {
            $where .= " AND active = " . $input['active'];
        }

        if(isset($input['exclude_content_type']) && $input['exclude_content_type']){
            $where .= " AND content_type <> " . $input['exclude_content_type'] ;
        }
        if ($confi_levels == 2) {
            if (isset($input['keyword']) && $input['keyword'] !== '') {
                $keyword .= "WHERE concat(level_one.category_name, '>', level_two.category_name )  LIKE '%" . $input['keyword'] . "%' ";
            }
        } else {
            if (isset($input['keyword']) && $input['keyword'] !== '') {
                $keyword .= "WHERE concat(level_one.category_name, '>', level_two.category_name , ' > ' ,level_three.category_name)  LIKE '%" . $input['keyword'] . "%' ";
            }
        }

        if ($confi_levels == 2) {
            $query = "SELECT level_two.id as id, level_two.meta_title , CONCAT( level_one.category_name, ' > ', level_two.category_name ) as name, level_two.active,
            level_two.content_type , level_two.promotion_campaign_id,'' as meta_title ,level_two.meta_description ,level_two.content_type , level_two.meta_keywords
            FROM (SELECT * FROM web_category_hierarchy WHERE category_level = 1 ) level_one
            JOIN ( SELECT * FROM web_category_hierarchy WHERE category_level = 2 " . $where . ") level_two ON level_two.parent_id = level_one.id
            " . $keyword . " ORDER BY level_one.category_name, level_two.category_name";
        } else {
            $query = "SELECT level_three.id as id, CONCAT( level_one.category_name, ' > ', level_two.category_name , ' > ' ,level_three.category_name) as name, level_three.active,
            level_three.content_type  ,level_three.meta_title ,level_three.meta_description,level_three.content_type ,level_three.meta_keywords,level_three.promotion_campaign_id
            FROM ( SELECT id , category_name FROM web_category_hierarchy WHERE category_level = 1 ) level_one
            JOIN ( SELECT * FROM web_category_hierarchy WHERE category_level = 2 ) level_two ON level_two.parent_id = level_one.id
            JOIN ( SELECT * FROM web_category_hierarchy WHERE category_level = 3 " . $where . ") level_three ON level_three.parent_id = level_two.id
            " . $keyword . " ORDER BY level_one.category_name, level_two.category_name , level_three.category_name";
        }
        return DB::select($query);
    }
    public function categoryProductsHierarchyId($id)
    {
        $result = CategoryProduct::where('web_category_hierarchy_id', $id)
            ->join('products_v', 'products_v.id', '=', 'web_category_products.product_id')
            ->select('web_category_products.id as web_category_products_id', 'web_category_products.product_id as product_id', 'products_v.*')
            ->get();
        return $result;
    }
    public function categoryProductsId($id)
    {
        $confi_levels = config('uec.web_category_hierarchy_levels');
        $result = CategoryProduct::where('web_category_products.product_id', $id)
            ->leftJoin('web_category_hierarchy', 'web_category_hierarchy.id', '=', 'web_category_products.web_category_hierarchy_id')
            ->select('web_category_products.web_category_hierarchy_id', 'web_category_hierarchy.category_name', 'web_category_products.sort','web_category_hierarchy.active')
            ->where('web_category_hierarchy.category_level',$confi_levels)
            ->orderBy('web_category_products.sort', 'ASC')
            ->get();
        return $result;
    }
    public function get_products_v($in)
    {
        $agent_id = Auth::user()->agent_id;
        $query = DB::table('products_v')
            ->select(DB::raw('products_v.*'), DB::raw('supplier.name as supplier_name'))
            ->leftJoin('supplier', 'products_v.supplier_id', '=', 'supplier.id')
            ->where('products_v.agent_id', $agent_id);
        if (isset($in['create_start_date']) && isset($in['create_end_date'])) {
            if ($in['create_start_date'] !== '' && $in['create_end_date'] !== '') {
                $query->where('products_v.created_date', '>=', $in['create_start_date'])
                    ->where('products_v.created_date', '<=', $in['create_end_date']);
            };
        };
        if (isset($in['filter_product_id']) && $in['filter_product_id'] !== '') {
            $query->whereNotIn('products_v.id', $in['filter_product_id']);
        };
        if (isset($in['product_no']) && $in['product_no'] !== '') {
            $query->where('product_no', $in['product_no']);
        };
        if (isset($in['select_start_date']) && isset($in['select_end_date'])) {
            if ($in['select_start_date'] !== '' && $in['select_end_date'] !== '') {
                $query->where('start_launched_at', '>=', $in['select_start_date'])
                    ->where('end_launched_at', '<=', $in['select_start_date']);
            };
        }
        if (isset($in['supplier_id']) && $in['supplier_id'] !== '') {
            $query->where('products_v.supplier_id', $in['supplier_id']);
        }
        if (isset($in['product_name']) && $in['product_name'] !== '') {
            $query->where('products_v.product_name', $in['product_name']);
        }
        if (isset($in['selling_price_max']) && $in['selling_price_max'] !== '') {
            $query->where('products_v.selling_price', '>=', $in['selling_price_max']);
        }
        if (isset($in['selling_price_min']) && $in['selling_price_min'] !== '') {
            $query->where('products_v.selling_price', '<=', $in['selling_price_min']);
        }
        return $query->get();
    }
    public function edit_category_hierarchy_content($in, $id)
    {
        $result = false;
        $category_products_list = json_decode($in['category_products_list_json'], true);
        $user_id = Auth::user()->id;
        $now = Carbon::now();

        DB::beginTransaction();
        try {
            CategoryHierarchy::where('id', $id)->update([
                'active' => $in['active'],
                'meta_title' => $in['meta_title'],
                'meta_description' => $in['meta_description'],
                'meta_keywords' => $in['meta_keyword'],
                'content_type' => $in['content_type'],
                'promotion_campaign_id' => $in['content_type'] == "M" ? $in['promotion_campaign_id'] : null,
            ]);
            foreach ($category_products_list as $key => $val) {
                if (!isset($val['web_category_products_id']) || $val['web_category_products_id'] == '') {
                    DB::table('web_category_products')->insert([
                        'web_category_hierarchy_id' => $id,
                        'product_id' => $val['product_id'],
                        'sort' => 0,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = false;
        }
        return $result;
    }

    public function del_category_hierarchy_content($id)
    {
        return DB::table('web_category_products')->where('id', $id)->delete();
    }

    public function DelCategoryInProduct($in)
    {
        return DB::table('web_category_products')->where('web_category_hierarchy_id', $in['category_id'])->where('product_id', $in['product_id'])->delete();
    }
    public function DelRelatedProducts($id)
    {
        return DB::table('related_products')->where('id', $id)->delete();
    }
    public function getRomotionalCampaigns($in)
    {
        $now = Carbon::now();
        $promotionalCampaigns = DB::table('promotional_campaigns')->where('level_code', 'CART_P');

        if (!empty($in['promotional_campaigns_time_type'])) {
            // if ($in['promotional_campaigns_time_type'] == 'all') {
            // }
            if ($in['promotional_campaigns_time_type'] == 'not_expired') {
                $promotionalCampaigns->where('end_at', '>=', $now);
            }
        }

        if (!empty($in['promotional_campaigns_key_word'])) {
            $promotionalCampaigns->Where('campaign_name', 'like', '%' . $in['promotional_campaigns_key_word'] . '%')
            ->orWhere('campaign_brief', 'like', '%' . $in['promotional_campaigns_key_word'] . '%');
        }

        if (!empty($in['id'])) {
            $promotionalCampaigns->where('id', $in['id']);
        };
        return $promotionalCampaigns->get();
    }
    public  function del_icon_photo($id){
        $CategoryHierarchy = CategoryHierarchy::where('id',$id)->first();
        if($CategoryHierarchy->icon_name){
            ImageUpload::DelPhoto($CategoryHierarchy->icon_name);
            $CategoryHierarchy->icon_name = null ;
            $CategoryHierarchy->save();
        }
        return true ;
    }

}
