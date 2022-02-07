<?php


namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\APIProductServices;
use App\Services\APIWebService;
use App\Models\Lookup_values_v;

class APIIndexServices
{

    private $apiProductService;
    private $apiWebService;

    public function __construct(APIProductServices $apiProductService, APIWebService $apiWebService)
    {
        $this->apiProductService = $apiProductService;
        $this->apiWebService = $apiWebService;
    }

    public function getIndex($params = null)
    {
        $now = Carbon::now();
        $s3 = config('filesystems.disks.s3.url');
        $products = $this->apiProductService->getProducts();
        $categoryProducts = $this->apiProductService->getWebCategoryProducts();
        $strSQL = "select ad1.`slot_code`, ad1.`slot_desc`, ad1.`slot_type`, ad1.`is_mobile_applicable`, ad1.`is_desktop_applicable`
                , ad2.`slot_color_code`, ad2.`slot_icon_name`, ad2.`slot_title`, ad2.`product_assigned_type`
                , ad3.*
                from `ad_slots` ad1
                inner join `ad_slot_contents` ad2 on ad2.`slot_id`=ad1.`id`
                inner join `ad_slot_content_details` ad3 on ad3.`ad_slot_content_id`=ad2.`id`
                where current_timestamp() between ad2.`start_at` and ad2.`end_at` and ad1.active = 1 and ad2.active = 1 ";
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
        $promotion = $this->apiProductService->getPromotion('product_card');
        foreach ($promotion as $k => $v) {
            $promotion_txt = '';
            foreach ($v as $label) {
                if ($promotion_txt != $label->promotional_label) {
                    $promotional[$k][] = $label->promotional_label;
                    $promotion_txt = $label->promotional_label;
                }
            }
        }
        $login = Auth::guard('api')->check();
        $is_collection = [];
        if ($login) {
            $member_id = Auth::guard('api')->user()->member_id;
            if ($member_id > 0) {
                $response = $this->apiWebService->getMemberCollections();
                $is_collection = json_decode($response, true);
            }
        }
        foreach ($ads as $ad_slot) {
            if ($ad_slot->slot_type == 'T') {
                $data[$ad_slot->slot_code][] = array(
                    'name' => $ad_slot->texts,
                    'url' => $ad_slot->target_url,
                    'target_blank' => $ad_slot->is_target_blank,
                    'target_campaign' => $ad_slot->target_campaign_id,
                    'target_cate_hierarchy' => $ad_slot->target_cate_hierarchy_id,
                    'img_action' => $ad_slot->image_action,
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
                if (!isset($products[$ad_slot->product_id])) continue;
                if ($now >= $products[$ad_slot->product_id]->promotion_start_at && $now <= $products[$ad_slot->product_id]->promotion_end_at) {
                    $promotion_desc = $products[$ad_slot->product_id]->promotion_desc;
                } else {
                    $promotion_desc = null;
                }
                if ($ad_slot->product_assigned_type == 'C') {
                    if (isset($categoryProducts[$ad_slot->web_category_hierarchy_id])) {
                        $product_info = [];
                        foreach ($categoryProducts[$ad_slot->web_category_hierarchy_id] as $product) {
                            if ($now >= $product->promotion_start_at && $now <= $product->promotion_end_at) {
                                $promotion_desc = $product->promotion_desc;
                            } else {
                                $promotion_desc = null;
                            }
                            if (isset($is_collection)) {
                                $collection = false;
                                foreach ($is_collection as $k => $v) {
                                    if ($v['product_id'] == $product->id) {
                                        $collection = true;
                                    }
                                }
                            }
                            $product_info[] = array(
                                'product_id' => $product->id,
                                'product_no' => $product->product_no,
                                'product_name' => $product->product_name,
                                'product_unit' => $product->uom,
                                'list_price' => $product->list_price,
                                'selling_price' => $product->selling_price,
                                'product_photo' => ($product->displayPhoto ? $s3 . $product->displayPhoto : null),
                                'promotion_desc' => $promotion_desc,
                                'promotion_label' => (isset($promotional[$product->id]) ? $promotional[$product->id] : null),
                                "collection" => $collection,
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

                    if ($now >= $products[$ad_slot->product_id]->promotion_start_at && $now <= $products[$ad_slot->product_id]->promotion_end_at) {
                        $promotion_desc = $products[$ad_slot->product_id]->promotion_desc;
                    } else {
                        $promotion_desc = null;
                    }

                    if (isset($is_collection)) {
                        $collection = false;
                        foreach ($is_collection as $k => $v) {
                            if ($v['product_id'] == $ad_slot->product_id) {
                                $collection = true;
                            }
                        }
                    }

                    $product_info[$ad_slot->slot_code][] = array(
                        'product_id' => $ad_slot->product_id,
                        'product_no' => $products[$ad_slot->product_id]->product_no,
                        'product_name' => $products[$ad_slot->product_id]->product_name,
                        'product_unit' => $products[$ad_slot->product_id]->uom,
                        'list_price' => $products[$ad_slot->product_id]->list_price,
                        'selling_price' => $products[$ad_slot->product_id]->selling_price,
                        'product_photo' => ($products[$ad_slot->product_id]->displayPhoto ? $s3 . $products[$ad_slot->product_id]->displayPhoto : null),
                        'promotion_desc' => $promotion_desc,
                        'promotion_label' => (isset($promotional[$ad_slot->product_id]) ? $promotional[$ad_slot->product_id] : null),
                        "collection" => $collection,
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
                if (!isset($products[$ad_slot->product_id])) continue;
                if ($ad_slot->data_type == 'PRD' && isset($products[$ad_slot->product_id])) {
                    if ($now >= $products[$ad_slot->product_id]->promotion_start_at && $now <= $products[$ad_slot->product_id]->promotion_end_at) {
                        $promotion_desc = $products[$ad_slot->product_id]->promotion_desc;
                    } else {
                        $promotion_desc = null;
                    }
                    if (isset($is_collection)) {
                        $collection = false;
                        foreach ($is_collection as $k => $v) {
                            if ($v['product_id'] == $ad_slot->product_id) {
                                $collection = true;
                            }
                        }
                    }
                    if ($ad_slot->slot_code == 'H080A') {
                        $prd_H080A[] = array(
                            'product_id' => $products[$ad_slot->product_id]->id,
                            'product_no' => $products[$ad_slot->product_id]->product_no,
                            'product_name' => $products[$ad_slot->product_id]->product_name,
                            'product_unit' => $products[$ad_slot->product_id]->uom,
                            'list_price' => $products[$ad_slot->product_id]->list_price,
                            'selling_price' => $products[$ad_slot->product_id]->selling_price,
                            'product_photo' => ($products[$ad_slot->product_id]->displayPhoto ? $s3 . $products[$ad_slot->product_id]->displayPhoto : null),
                            'mobile_applicable' => $ad_slot->is_mobile_applicable,
                            'desktop_applicable' => $ad_slot->is_desktop_applicable,
                            'promotion_desc' => $promotion_desc,
                            'promotion_label' => (isset($promotional[$ad_slot->product_id]) ? $promotional[$ad_slot->product_id] : null),
                            "collection" => $collection,
                        );
                    }
                    if ($ad_slot->slot_code == 'H080B') {
                        $prd_H080B[] = array(
                            'product_id' => $products[$ad_slot->product_id]->id,
                            'product_no' => $products[$ad_slot->product_id]->product_no,
                            'product_name' => $products[$ad_slot->product_id]->product_name,
                            'product_unit' => $products[$ad_slot->product_id]->uom,
                            'list_price' => $products[$ad_slot->product_id]->list_price,
                            'selling_price' => $products[$ad_slot->product_id]->selling_price,
                            'product_photo' => ($products[$ad_slot->product_id]->displayPhoto ? $s3 . $products[$ad_slot->product_id]->displayPhoto : null),
                            'mobile_applicable' => $ad_slot->is_mobile_applicable,
                            'desktop_applicable' => $ad_slot->is_desktop_applicable,
                            'promotion_desc' => $promotion_desc,
                            'promotion_label' => (isset($promotional[$ad_slot->product_id]) ? $promotional[$ad_slot->product_id] : null),
                            "collection" => $collection,
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
            $data['H080A'] = array(
                'images' => $img_H080A,
                'products' => $prd_H080A
            );
        }
        if (!$img_H080B && !$prd_H080B) {
            unset($data['H080B']);
        } else {
            $data['H080B'] = array(
                'images' => $img_H080B,
                'products' => $prd_H080B
            );
        }
        return $data;
    }

    public function getUTM($params = null)
    {
        $result = Lookup_values_v::select('udf_01 as url')
            ->where('type_code', 'UTM_PAGE')
            ->where('code', $params)
            ->where('active', 1)->get();
        return $result;
    }


}
