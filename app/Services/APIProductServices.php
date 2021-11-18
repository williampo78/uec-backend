<?php


namespace App\Services;

use App\Services\WebCategoryHierarchyService;
use Illuminate\Support\Facades\DB;

class APIProductServices
{

    private $apiWebCategory;

    public function __construct(WebCategoryHierarchyService $apiWebCategory)
    {
        $this->apiWebCategory = $apiWebCategory;
    }

    public function getCategory()
    {
        //分類總覽階層
        $config_levels = config('uec.web_category_hierarchy_levels');

        $now = date("Y-m-d H:i");
        //根據階層顯示層級資料
        if ($config_levels == '3') {
            $strSQL = "select cate2.`id` L1ID , cate2.`category_name` L1_NAME, cate1.`id` L2ID , cate1.`category_name` L2_NAME, cate.* from `web_category_products` cate_prod
                    inner join `web_category_hierarchy` cate on  cate.`id` =cate_prod.`web_category_hierarchy_id`  and cate.`category_level`=3
                    inner join `products_v` prod on prod.`id` =cate_prod.`product_id`
                    inner join  `web_category_hierarchy` cate1 on cate1.`id`=cate.`parent_id`
                    inner join  `web_category_hierarchy` cate2 on cate2.`id`=cate1.`parent_id`
                    where cate.`active`=1
                    and date_format(prod.`start_launched_at`,'%Y-%m-%d %H:%i') <='" . $now . "' and date_format(prod.`end_launched_at`,'%Y-%m-%d') >='" . $now . "'
                    group by cate.`id`
                    order by cate2.`sort`, cate1.`sort`, cate.`sort`";
        } elseif ($config_levels == '2') {
            $strSQL = "select cate1.`id` L1ID , cate1.`category_name` L1_NAME, cate.* from `web_category_products` cate_prod
                    inner join `web_category_hierarchy` cate on  cate.`id` =cate_prod.`web_category_hierarchy_id` and cate.`category_level`=2
                    inner join `products_v` prod on prod.`id` =cate_prod.`product_id`
                    inner join  `web_category_hierarchy` cate1 on cate1.`id`=cate.`parent_id`
                    where cate.`active`=1
                    and date_format(prod.`start_launched_at`,'%Y-%m-%d %H:%i') <='" . $now . "' and date_format(prod.`end_launched_at`,'%Y-%m-%d') >='" . $now . "'
                    group by cate.`id`
                    order by cate1.`sort`, cate.`sort`";
        }
        $categorys = DB::select($strSQL);
        foreach ($categorys as $category) {

            $L1_data[$category->L1ID]["id"] = $category->L1ID;
            $L1_data[$category->L1ID]["name"] = $category->L1_NAME;

            if ($config_levels == '3') {
                $L2_data[$category->L1ID][$category->L2ID]["id"] = $category->L2ID;
                $L2_data[$category->L1ID][$category->L2ID]["name"] = $category->L2_NAME;

                $L3_data[$category->L1ID][$category->L2ID][$category->id]['id'] = $category->id;
                $L3_data[$category->L1ID][$category->L2ID][$category->id]['name'] = $category->category_name;
                $L3_data[$category->L1ID][$category->L2ID][$category->id]['type'] = $category->content_type;
                $L3_data[$category->L1ID][$category->L2ID][$category->id]['meta_title'] = $category->meta_title;
                $L3_data[$category->L1ID][$category->L2ID][$category->id]['meta_description'] = $category->meta_description;
                $L3_data[$category->L1ID][$category->L2ID][$category->id]['meta_keywords'] = $category->meta_keywords;

            } else if ($config_levels == '2') {

                $L2_data[$category->L1ID][$category->id]['id'] = $category->id;
                $L2_data[$category->L1ID][$category->id]['name'] = $category->category_name;
                $L2_data[$category->L1ID][$category->id]['type'] = $category->content_type;
                $L2_data[$category->L1ID][$category->id]['meta_title'] = $category->meta_title;
                $L2_data[$category->L1ID][$category->id]['meta_description'] = $category->meta_description;
                $L2_data[$category->L1ID][$category->id]['meta_keywords'] = $category->meta_keywords;
            }

        }
        $data = [];
        foreach ($L1_data as $key1 => $value1) {
            $data2 = [];
            $data[$key1]["id"] = $value1["id"];
            $data[$key1]["name"] = $value1["name"];
            foreach ($L2_data[$key1] as $key2 => $value2) {
                $data2[$key2]["id"] = $value2["id"];
                $data2[$key2]["name"] = $value2["name"];
                if ($config_levels == 3) {
                    $data3 = [];
                    foreach ($L3_data[$key1][$key2] as $key3 => $value3) {
                        $data3[$key3]["id"] = $value3["id"];
                        $data3[$key3]["name"] = $value3["name"];
                        $data3[$key3]["type"] = $value3["type"];
                        $data3[$key3]["meta_title"] = $value3["meta_title"];
                        $data3[$key3]["meta_description"] = $value3["meta_description"];
                        $data3[$key3]["meta_keywords"] = $value3["meta_keywords"];
                        $data2[$key2]["cateInfo"] = $data3;
                    }
                } elseif ($config_levels == 2) {
                    $data2[$key2]["type"] = $value2["type"];
                    $data2[$key2]["meta_title"] = $value2["meta_title"];
                    $data2[$key2]["meta_description"] = $value2["meta_description"];
                    $data2[$key2]["meta_keywords"] = $value2["meta_keywords"];
                }
            }
            $data[$key1]["cateInfo"] = $data2;
        }

        return $data;
    }


}
