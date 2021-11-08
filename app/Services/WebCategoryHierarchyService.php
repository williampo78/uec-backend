<?php

namespace App\Services;

use App\Models\CategoryHierarchy;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        $insert['parent_id'] = $in['parent_id'];//父ID
        $insert['category_level'] = $in['category_level']; // 階級
        $insert['category_name'] = $in['category_name'] ; 
        $insert['agent_id'] = $agent_id ;
        $insert['sort'] = $this->getSort($in) ; 
        $insert['created_by'] = $user_id ; 
        $insert['updated_by'] = $user_id ; 
        $insert['created_at'] = $now ;
        $insert['updated_at'] = $now ; 
        $query = CategoryHierarchy::insert($insert);
        return $query ;
        // CategoryHierarchy::insert($insert);
    }
    public function getSort($in){
        
        $query = CategoryHierarchy::where('parent_id', $in['parent_id'])
        ->where('category_level' , $in['category_level'])
        ->orderBy('sort', 'desc')
        ->first();
        $query->sort += 1  ;
        $resut = $query->sort ;  
        return  $resut ;
    }
}
