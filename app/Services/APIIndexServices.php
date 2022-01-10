<?php


namespace App\Services;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;
use App\Services\APIProductServices;

class APIIndexServices
{

    private $apiProductService;

    public function __construct(APIProductServices $apiProductService)
    {
        $this->apiProductService = $apiProductService;
    }

    public function getIndex($params = null)
    {
        $s3 = config('filesystems.disks.s3.url');
        $products = $this->apiProductService->getProducts();
        $categoryProducts = $this->apiProductService->getWebCategoryProducts();
        $strSQL = "select ad1.`slot_code`, ad1.`slot_desc`, ad1.`slot_type`, ad1.`is_mobile_applicable`, ad1.`is_desktop_applicable`
                , ad2.`slot_color_code`, ad2.`slot_icon_name`, ad2.`slot_title`, ad2.`product_assigned_type`
                , ad3.*
                from `ad_slots` ad1
                inner join `ad_slot_contents` ad2 on ad2.`slot_id`=ad1.`id`
                inner join `ad_slot_content_details` ad3 on ad3.`ad_slot_content_id`=ad2.`id`
                where current_timestamp() between ad2.`start_at` and ad2.`end_at` ";
        if ($params == 1) {
            $strSQL .= " and ad1.`applicable_page` !='HOME'";
        } else {
            $strSQL .= " and ad1.`applicable_page` ='HOME'";
        }
        $strSQL .= " order by ad1.`slot_code`, ad3.`sort`";
        $ads = DB::select($strSQL);
        $data = [];
        $img_H080A = [];
        $img_H080B = [];
        $prd_H080A = [];
        $prd_H080B = [];
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
                    'slot_color_code' => $ad_slot->slot_color_code,
                    'slot_icon_name' => ($ad_slot->slot_icon_name ? $s3 . $ad_slot->slot_icon_name : null),
                    'slot_title' => $ad_slot->slot_title,
                    'img_path' => ($ad_slot->image_name ? $s3 . $ad_slot->image_name : null),
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
                if ($ad_slot->product_assigned_type == 'C') {
                    if (isset($categoryProducts[$ad_slot->web_category_hierarchy_id])) {
                        $product_info = [];
                        foreach ($categoryProducts[$ad_slot->web_category_hierarchy_id] as $product) {
                            $product_info[] = array(
                                'prod_id' => $product->id,
                                'prod_no' => $product->product_no,
                                'prod_name' => $product->product_name,
                                'prod_unit' => $product->uom,
                                'prod_price' => $product->selling_price,
                                'prod_photo_path' => ($product->displayPhoto ? $s3 . $product->displayPhoto : null),
                            );
                        }

                        $data[$ad_slot->slot_code][] = array(
                            'slot_color_code' => $ad_slot->slot_color_code,
                            'slot_icon_name' => ($ad_slot->slot_icon_name ? $s3 . $ad_slot->slot_icon_name : null),
                            'slot_title' => $ad_slot->slot_title,
                            'mobile_applicable' => $ad_slot->is_mobile_applicable,
                            'desktop_applicable' => $ad_slot->is_desktop_applicable,
                            'products' => $product_info
                        );
                    }
                } else if ($ad_slot->product_assigned_type == 'P') {
                    $product_info[$ad_slot->slot_code][] = array(
                        'prod_id' => $ad_slot->product_id,
                        'prod_no' => $products[$ad_slot->product_id]->product_no,
                        'prod_name' => $products[$ad_slot->product_id]->product_name,
                        'prod_unit' => $products[$ad_slot->product_id]->uom,
                        'prod_price' => $products[$ad_slot->product_id]->selling_price,
                        'prod_photo_path' => ($products[$ad_slot->product_id]->displayPhoto ? $s3 . $products[$ad_slot->product_id]->displayPhoto : null),
                    );

                    $data[$ad_slot->slot_code] = array(
                        'slot_color_code' => $ad_slot->slot_color_code,
                        'slot_icon_name' => ($ad_slot->slot_icon_name ? $s3 . $ad_slot->slot_icon_name : null),
                        'slot_title' => $ad_slot->slot_title,
                        'mobile_applicable' => $ad_slot->is_mobile_applicable,
                        'desktop_applicable' => $ad_slot->is_desktop_applicable,
                        'products' => $product_info[$ad_slot->slot_code]
                    );
                }
            } elseif ($ad_slot->slot_type == 'IS') {
                /*
                $image_info = [];
                $product_info = [];
                if ($ad_slot->data_type == 'PRD' && isset($products[$ad_slot->product_id])) {
                    $product_info[$ad_slot->slot_code][] = array(
                        'prod_id' => $products[$ad_slot->product_id]->id,
                        'prod_no' => $products[$ad_slot->product_id]->product_no,
                        'prod_name' => $products[$ad_slot->product_id]->product_name,
                        'prod_unit' => $products[$ad_slot->product_id]->uom,
                        'prod_price' => $products[$ad_slot->product_id]->selling_price,
                        'prod_photo_path' => ($products[$ad_slot->product_id]->displayPhoto ? $s3 . $products[$ad_slot->product_id]->displayPhoto : null),
                        'mobile_applicable' => $ad_slot->is_mobile_applicable,
                        'desktop_applicable' => $ad_slot->is_desktop_applicable
                    );
                }
                if ($ad_slot->data_type == 'IMG') {

                    $image_info[$ad_slot->slot_code][] = array(
                        'img_path' => ($ad_slot->image_name ? $s3 . $ad_slot->image_name : null),
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

                $data[$ad_slot->slot_code] = array(
                    'images' => (isset($product_info[$ad_slot->slot_code]) ? $product_info[$ad_slot->slot_code] : []),
                    'products' => (isset($product_info[$ad_slot->slot_code]) ? $product_info[$ad_slot->slot_code] : [])
                );
                */

                if ($ad_slot->data_type == 'PRD' && isset($products[$ad_slot->product_id])) {
                    if ($ad_slot->slot_code == 'H080A') {
                        $prd_H080A[] = array(
                            'prod_id' => $products[$ad_slot->product_id]->id,
                            'prod_no' => $products[$ad_slot->product_id]->product_no,
                            'prod_name' => $products[$ad_slot->product_id]->product_name,
                            'prod_unit' => $products[$ad_slot->product_id]->uom,
                            'prod_price' => $products[$ad_slot->product_id]->selling_price,
                            'prod_photo_path' => ($products[$ad_slot->product_id]->displayPhoto ? $s3 . $products[$ad_slot->product_id]->displayPhoto : null),
                            'mobile_applicable' => $ad_slot->is_mobile_applicable,
                            'desktop_applicable' => $ad_slot->is_desktop_applicable
                        );
                    }
                    if ($ad_slot->slot_code == 'H080B') {
                        $prd_H080B[] = array(
                            'prod_id' => $products[$ad_slot->product_id]->id,
                            'prod_no' => $products[$ad_slot->product_id]->product_no,
                            'prod_name' => $products[$ad_slot->product_id]->product_name,
                            'prod_unit' => $products[$ad_slot->product_id]->uom,
                            'prod_price' => $products[$ad_slot->product_id]->selling_price,
                            'prod_photo_path' => ($products[$ad_slot->product_id]->displayPhoto ? $s3 . $products[$ad_slot->product_id]->displayPhoto : null),
                            'mobile_applicable' => $ad_slot->is_mobile_applicable,
                            'desktop_applicable' => $ad_slot->is_desktop_applicable
                        );
                    }
                }

                if ($ad_slot->data_type == 'IMG') {
                    if ($ad_slot->slot_code == 'H080A') {
                        $img_H080A[] = array(
                            'img_path' => ($ad_slot->image_name ? $s3 . $ad_slot->image_name : null),
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
                    if ($ad_slot->slot_code == 'H080B') {
                        $img_H080B[] = array(
                            'img_path' => ($ad_slot->image_name ? $s3 . $ad_slot->image_name : null),
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
        }

        if (!$img_H080A && !$prd_H080A) {
            unset($data['H080A']);
        } else {
            $data['H080A'][] = array(
                'images' => $img_H080A,
                'products' => $prd_H080A
            );
        }
        if (!$img_H080B && !$prd_H080B) {
            unset($data['H080B']);
        } else {
            $data['H080B'][] = array(
                'images' => $img_H080B,
                'products' => $prd_H080B
            );
        }
        return $data;
    }


}
