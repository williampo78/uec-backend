<?php

namespace App\Services;

// use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\TertiaryCategories;

class CategoriesSerivce
{
    /**
     * POS 分類 後台專用 不要跟前台分類搞混了
     * 
     * 
    */
    public function __construct()
    {

    }
    /**
     * 取得大分類 -> 中分類 -> 小分類
     * 
     */
    public function getPosCategories(){
        
        $result = TertiaryCategories::select(
            DB::raw('tertiary_categories.id as id'),
            DB::raw('primary_category.name as primary_category'),
            DB::raw('category.name as category_name'),
            DB::raw('tertiary_categories.name as tertiary_categories_name'),

            DB::raw("CONCAT(primary_category.name,' > ' ,category.name , ' > ' ,tertiary_categories.name) AS name")

        )->join('category','category.id' ,'=' , 'tertiary_categories.category_id') 
         ->join('primary_category' , 'primary_category.id' , '=' , 'category.id')
         ->get()
        ;
        return $result ;
    }

}
