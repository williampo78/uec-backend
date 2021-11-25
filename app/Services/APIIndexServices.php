<?php


namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\APIProductServices;

class APIIndexServices
{

    private $apiProductService;

    public function __construct(APIProductServices $apiProductService)
    {
        $this->apiProductService = $apiProductService;
    }

    public function getIndex()
    {
        $products = $this->apiProductService->getProducts();
        $categoryProducts = $this->apiProductService->getWebCategoryProducts();
        $strSQL = "select ad1.`slot_code`, ad1.`slot_desc`, ad1.`slot_type`, ad1.`is_mobile_applicable`, ad1.`is_desktop_applicable`
                , ad2.`slot_color_code`, ad2.`slot_icon_name`, ad2.`slot_title`, ad2.`product_assigned_type`
                , ad3.*
                from `ad_slots` ad1
                inner join `ad_slot_contents` ad2 on ad2.`slot_id`=ad1.`id`
                inner join `ad_slot_content_details` ad3 on ad3.`ad_slot_content_id`=ad2.`id`
                where current_timestamp() between ad2.`start_at` and ad2.`end_at` and ad1.`applicable_page`='HOME'
                order by ad1.`slot_code`, ad3.`sort`";

        $ads = DB::select($strSQL);
        $data = [];
        $img_H080A = [];
        $img_H080B = [];
        $prd_H080A = [];
        $prd_H080B = [];
        $prod = [];
        foreach ($ads as $ad_slot) {
            if ($ad_slot->slot_type == 'T') {
                 $data[$ad_slot->slot_code][] = array(
                     'name' => $ad_slot->texts,
                     'url' => $ad_slot->target_url,
                     'target_blank' => $ad_slot->is_target_blank,
                     'mobile_applicable' => $ad_slot->is_mobile_applicable,
                     'desktop_applicable' => $ad_slot->is_desktop_applicable
                 );
            } elseif ($ad_slot->slot_type == 'I') {
                $data[$ad_slot->slot_code][] = array(
                    'img_path' => $ad_slot->image_name,
                    'img_alt' => $ad_slot->image_alt,
                    'img_title' => $ad_slot->image_title,
                    'img_abstract' => $ad_slot->image_abstract,
                    'img_action' => $ad_slot->image_action,
                    'url' => $ad_slot->target_url,
                    'target_blank' => $ad_slot->is_target_blank,
                    'target_campaign' => $ad_slot->target_campaign_id,
                    'target_cate_hierarchy' => $ad_slot->target_cate_hierarchy_id,
                    'mobile_applicable' => $ad_slot->is_mobile_applicable,
                    'desktop_applicable' => $ad_slot->is_desktop_applicable
                );
            } elseif ($ad_slot->slot_type == 'S') {
                if (isset($categoryProducts[$ad_slot->web_category_hierarchy_id])) {
                    $product_info = [];
                    foreach ($categoryProducts[$ad_slot->web_category_hierarchy_id] as $product) {
                        $product_info[] = array(
                            'prod_id' => $product->product_no,
                            'prod_name' => $product->product_name,
                            'prod_unit' => $product->uom,
                            'prod_price' => $product->selling_price,
                            'prod_photo_path' => $product->displayPhoto,
                        );
                    }

                    $data[$ad_slot->slot_code][] = array(
                        'slot_color_code' => $ad_slot->slot_color_code,
                        'slot_icon_name' => $ad_slot->slot_icon_name,
                        'slot_title' => $ad_slot->slot_title,
                        'mobile_applicable' => $ad_slot->is_mobile_applicable,
                        'desktop_applicable' => $ad_slot->is_desktop_applicable,
                        'products'=>$product_info
                    );
                }
            } elseif ($ad_slot->slot_type == '4') {
                if (isset($products[$ad_slot->product_id])) {
                    $data[$ad_slot->slot_code][] = array(
                        'prod_id' => $products[$ad_slot->product_id]->product_no,
                        'prod_name' => $products[$ad_slot->product_id]->product_name,
                        'prod_unit' => $products[$ad_slot->product_id]->uom,
                        'prod_price' => $products[$ad_slot->product_id]->selling_price,
                        'prod_photo_path' => $products[$ad_slot->product_id]->displayPhoto,
                        'mobile_applicable' => $ad_slot->is_mobile_applicable,
                        'desktop_applicable' => $ad_slot->is_desktop_applicable
                    );
                }
            } elseif ($ad_slot->slot_type == '5') {
                if ($ad_slot->data_type=='PRD' && isset($products[$ad_slot->product_id])){
                    $prd_H080A[] =  array(
                        'prod_id' => $products[$ad_slot->product_id]->product_no,
                        'prod_name' => $products[$ad_slot->product_id]->product_name,
                        'prod_unit' => $products[$ad_slot->product_id]->uom,
                        'prod_price' => $products[$ad_slot->product_id]->selling_price,
                        'prod_photo_path' => $products[$ad_slot->product_id]->displayPhoto,
                        'mobile_applicable' => $ad_slot->is_mobile_applicable,
                        'desktop_applicable' => $ad_slot->is_desktop_applicable
                    );
                }
                if ($ad_slot->data_type=='IMG'){
                    $img_H080A[] = array(
                        'img_path' => $ad_slot->image_name,
                        'img_alt' => $ad_slot->image_alt,
                        'img_title' => $ad_slot->image_title,
                        'img_abstract' => $ad_slot->image_abstract,
                        'img_action' => $ad_slot->image_action,
                        'url' => $ad_slot->target_url,
                        'target_blank' => $ad_slot->is_target_blank,
                        'target_campaign' => $ad_slot->target_campaign_id,
                        'target_cate_hierarchy' => $ad_slot->target_cate_hierarchy_id,
                        'mobile_applicable' => $ad_slot->is_mobile_applicable,
                        'desktop_applicable' => $ad_slot->is_desktop_applicable
                    );
                }
            } elseif ($ad_slot->slot_type == 'IS') {
                if ($ad_slot->data_type=='PRD' && isset($products[$ad_slot->product_id])){
                    $prd_H080B[] =  array(
                        'prod_id' => $products[$ad_slot->product_id]->product_no,
                        'prod_name' => $products[$ad_slot->product_id]->product_name,
                        'prod_unit' => $products[$ad_slot->product_id]->uom,
                        'prod_price' => $products[$ad_slot->product_id]->selling_price,
                        'prod_photo_path' => $products[$ad_slot->product_id]->displayPhoto,
                        'mobile_applicable' => $ad_slot->is_mobile_applicable,
                        'desktop_applicable' => $ad_slot->is_desktop_applicable
                    );
                }
                if ($ad_slot->data_type=='IMG'){
                    $img_H080B[] = array(
                        'img_path' => $ad_slot->image_name,
                        'img_alt' => $ad_slot->image_alt,
                        'img_title' => $ad_slot->image_title,
                        'img_abstract' => $ad_slot->image_abstract,
                        'img_action' => $ad_slot->image_action,
                        'url' => $ad_slot->target_url,
                        'target_blank' => $ad_slot->is_target_blank,
                        'target_campaign' => $ad_slot->target_campaign_id,
                        'target_cate_hierarchy' => $ad_slot->target_cate_hierarchy_id,
                        'mobile_applicable' => $ad_slot->is_mobile_applicable,
                        'desktop_applicable' => $ad_slot->is_desktop_applicable
                    );
                }
            }
        }

        $data['H080A'][] = array(
            'images'=>$img_H080A,
            'products'=>$prd_H080A
        );
        $data['H080B'][] = array(
            'images'=>$img_H080B,
            'products'=>$prd_H080B
        );
        return $data;
    }


}