<?php

namespace App\Services;

use App\Models\CategoryHierarchy;

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
    public function add_Category_Hierarchy($in){
        $in ; 
    }
}
