<?php

namespace App\Services;

// use Illuminate\Support\Facades\Log;
use App\Models\TertiaryCategory;
use Illuminate\Support\Facades\DB;

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
        $result = TertiaryCategory::select(
            DB::raw('tertiary_categories.id as id'),
            DB::raw('primary_category.name as primary_category'),
            DB::raw('category.name as category_name'),
            DB::raw('tertiary_categories.name as tertiary_categories_name'),

            DB::raw("CONCAT(primary_category.name,' > ' ,category.name , ' > ' ,tertiary_categories.name) AS name")

        )->join('category','category.id' ,'=' , 'tertiary_categories.category_id')
         ->join('primary_category' , 'primary_category.id' , '=' , 'category.primary_category_id')
         ->where('tertiary_categories.active','=','1')
         ->orderBy('primary_category.number')
         ->orderBy('category.number')
         ->orderBy('tertiary_categories.number')
         ->get()
        ;
        return $result ;
    }

}
