<?php

namespace App\Services;

use App\Models\CategoryHierarchy;
use App\Models\CategoryProducts;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\Input;

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
        return CategoryHierarchy::where('parent_id', $in['parent_id'])->orderBy('sort', 'ASC')->get();
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
    public function category_hierarchy_content($input = array())
    {
        $confi_levels = config('uec.web_category_hierarchy_levels') ; 
        $whereID = '';
        $where = '' ; 

        if(isset($input['id']) && $input['id'] !== ''){
            $whereID .= "AND id = " . $input['id'] ; 
        }
        if(isset($input['active']) && $input['active'] !== ''){
            $whereID .= " AND active = " .$input['active'] ; 
        }
        if(isset($input['keyword']) && $input['keyword'] !== ''){
            $where .= "WHERE concat(level_one.category_name, '>', level_two.category_name , ' > ' ,level_three.category_name)  LIKE '%".$input['keyword']."%' " ;
        }
    
        if($confi_levels == 2){
            $query = "SELECT level_two.id as id, CONCAT( level_one.category_name, ' > ', level_two.category_name ) as name, level_two.active, 
            level_two.content_type ,level_two.level_three ,level_two.meta_description ,level_two.content_type , level_two.meta_keywords
            FROM (SELECT * WHERE category_level = 1 ) level_one
            JOIN ( SELECT * FROM web_category_hierarchy WHERE category_ level = 2 ".$whereID.") level_two ON level_two.parent_id = level_one.id
            ".$where." ORDER BY level_one.category_name, level_two.category_name";
        }else{
            $query = "SELECT level_three.id as id, CONCAT( level_one.category_name, ' > ', level_two.category_name , ' > ' ,level_three.category_name) as name, level_three.active, 
            level_three.content_type  ,level_three.meta_title ,level_three.meta_description,level_three.content_type ,level_three.meta_keywords
            FROM ( SELECT id , category_name FROM web_category_hierarchy WHERE category_level = 1 ) level_one
            JOIN ( SELECT * FROM web_category_hierarchy WHERE category_level = 2 ) level_two ON level_two.parent_id = level_one.id
            JOIN ( SELECT * FROM web_category_hierarchy WHERE category_level = 3 ".$whereID.") level_three ON level_three.parent_id = level_two.id
            ".$where." ORDER BY level_one.category_name, level_two.category_name , level_three.category_name";
        }
        
        
        return DB::select($query);
    }
    public function category_products($id){

        $resut =  CategoryProducts::where('web_category_hierarchy_id' , $id)
        ->select('web_category_products.id as id' , 'web_category_products.product_id as product_id' , 'products_v.*')
        ->join('products_v', 'products_v.id', '=', 'web_category_products.product_id')
        ->get();
        return $resut ; 
    }
    public function get_products_v(){
        $resut = DB::table('products_v')->select(DB::raw('products_v.*'),DB::raw('supplier.name as supplier_name'))->leftJoin('supplier', 'products_v.supplier_id', '=', 'supplier.id')->get()->toArray() ; 
        return $resut ;
    }   

}
