<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CategoryHierarchy;
class WebCategoryHierarchyService
{
    public function __construct()
    {

    }
    public function web_Category_Hierarchy_Bylevel($parent_id=null){

      return CategoryHierarchy::where('parent_id' , $parent_id)->get();
    }
}
