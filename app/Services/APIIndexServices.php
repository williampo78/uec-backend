<?php


namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Psr7\Request;
use App\Models\LookupValuesV;
use App\Services\APIWebService;
use Illuminate\Support\Facades\DB;
use App\Services\APIProductServices;
use Illuminate\Support\Facades\Auth;

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
        $gtm = $this->apiProductService->getProductItemForGTM($products);
        $categoryProducts = $this->apiProductService->getWebCategoryProducts('', '', '', '', '', '', '');
        $rel_category = [];
        foreach ($categoryProducts as $key => $category) {
            foreach ($category as $kk => $vv) {
                $rel_category[$vv->id] = $vv;
            }
        }

        $ads = DB::table("ad_slots as ad1")
            ->join("ad_slot_contents as ad2", "ad2.slot_id", "=", "ad1.id")
            ->join("ad_slot_content_details as ad3", "ad3.ad_slot_content_id", "ad2.id")
            ->leftJoin("promotional_campaigns as event", "event.id", "=", "ad3.target_campaign_id")
            ->select("ad1.slot_code", "ad1.slot_desc", "ad1.slot_type", "ad1.is_mobile_applicable", "ad1.is_desktop_applicable",
                "ad2.slot_color_code", "ad2.slot_icon_name", "ad2.slot_title", "ad2.product_assigned_type", "ad2.slot_title_color", "ad2.see_more_action", "ad2.see_more_url", "ad2.see_more_cate_hierarchy_id", "ad2.see_more_target_blank"
                , "ad3.*", "event.url_code")
            ->where("ad2.start_at", "<=", $now)
            ->where("ad2.end_at", ">=", $now)
            ->where("ad1.active", "=", 1)
            ->where("ad2.active", "=", 1);
        if ($params) {
            $ads = $ads->where("ad1.applicable_page", "<>", "HOME");
            $ads = $ads->where("ad1.slot_code", "=", $params);
        } else {
            $ads = $ads->where("ad1.applicable_page", "=", "HOME");
        }
        $ads = $ads->orderBy('ad3.sort', 'asc')->get();

        $data = [];
        $img_H080A = [];
        $img_H080B = [];
        $prd_H080A = [];
        $prd_H080B = [];
        $H080A_seemore = [];
        $H080B_seemore = [];
        $product_info = [];
        $promotion = $this->apiProductService->getPromotion('product_card');
        $promotion_threshold = $this->apiProductService->getPromotionThreshold();
        foreach ($promotion as $k => $v) {
            $promotion_txt = '';
            foreach ($v as $label) {
                if ($label->promotional_label == '') continue;
                if ($label->campaign_type == 'CART_P03' || $label->campaign_type == 'CART_P04') { //檢查多門檻的商品是否為正常上架
                    if (isset($promotion_threshold[$k])) {
                        if ($promotion_threshold[$k]) {
                            if ($promotion_txt != $label->promotional_label) {
                                $promotional[$k][] = $label->promotional_label;
                                $promotion_txt = $label->promotional_label;
                            }
                        }
                    }
                } elseif ($label->campaign_type == 'PRD05') { //單品
                    $campaign_gift = $this->apiProductService->getCampaignGiftByID($label->id);
                    if ($campaign_gift['result']) {
                        if ($promotion_txt != $label->promotional_label) {
                            $promotional[$k][] = $label->promotional_label;
                            $promotion_txt = $label->promotional_label;
                        }
                    }
                } else {
                    if ($promotion_txt != $label->promotional_label) {
                        $promotional[$k][] = $label->promotional_label;
                        $promotion_txt = $label->promotional_label;
                    }
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
                    'campaign_url_code' => $ad_slot->url_code,
                    'target_cate_hierarchy' => $ad_slot->target_cate_hierarchy_id,
                    'img_action' => $ad_slot->image_action,
                    'mobile_applicable' => $ad_slot->is_mobile_applicable,
                    'desktop_applicable' => $ad_slot->is_desktop_applicable
                );
            } elseif ($ad_slot->slot_type == 'I') {
                $data[$ad_slot->slot_code][] = array(
                    'slot_color_code' => $ad_slot->slot_color_code,
                    'slot_title_color' => $ad_slot->slot_title_color,
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
                    'campaign_url_code' => $ad_slot->url_code,
                    'target_cate_hierarchy' => $ad_slot->target_cate_hierarchy_id,
                    'see_more_action' => $ad_slot->see_more_action,
                    'see_more_url' => $ad_slot->see_more_url,
                    'see_more_cate_hierarchy_id' => $ad_slot->see_more_cate_hierarchy_id,
                    'see_more_target_blank' => $ad_slot->see_more_target_blank,
                    'mobile_applicable' => $ad_slot->is_mobile_applicable,
                    'desktop_applicable' => $ad_slot->is_desktop_applicable
                );
            } elseif ($ad_slot->slot_type == 'S') {
                if ($ad_slot->product_assigned_type == 'C') {
                    if (isset($categoryProducts[$ad_slot->web_category_hierarchy_id])) {
                        //$product_info = [];
                        foreach ($categoryProducts[$ad_slot->web_category_hierarchy_id] as $product) {
                            $test[$ad_slot->web_category_hierarchy_id][] = $product->id;
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

                            $product_info[$ad_slot->slot_code][$product->id] = array(
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
                                "selling_channel" => $product->selling_channel,
                                "start_selling" => $product->start_selling_at,
                                "gtm" => (isset($gtm[$product->id]) ? $gtm[$product->id] : "")
                            );
                        }

                        $product_info_return[$ad_slot->slot_code] = []; //重新調整結構for前端使用1
                        foreach ($product_info[$ad_slot->slot_code] as $product) {
                            $product_info_return[$ad_slot->slot_code][] = $product;
                        }

                        $data[$ad_slot->slot_code] = array(
                            'slot_color_code' => $ad_slot->slot_color_code,
                            'slot_title_color' => $ad_slot->slot_title_color,
                            'slot_icon_name' => ($ad_slot->slot_icon_name ? $s3 . $ad_slot->slot_icon_name : null),
                            'slot_title' => $ad_slot->slot_title,
                            'see_more_action' => $ad_slot->see_more_action,
                            'see_more_url' => $ad_slot->see_more_url,
                            'see_more_cate_hierarchy_id' => $ad_slot->see_more_cate_hierarchy_id,
                            'see_more_target_blank' => $ad_slot->see_more_target_blank,
                            'mobile_applicable' => $ad_slot->is_mobile_applicable,
                            'desktop_applicable' => $ad_slot->is_desktop_applicable,
                            'products' => $product_info_return[$ad_slot->slot_code]
                        );
                    }
                } else if ($ad_slot->product_assigned_type == 'P') {
                    if (isset($products[$ad_slot->product_id])) {
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
                        if (isset($rel_category[$ad_slot->product_id]->web_category_hierarchy_id) > 0) {
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
                                "selling_channel" => $products[$ad_slot->product_id]->selling_channel,
                                "start_selling" => $products[$ad_slot->product_id]->start_selling_at,
                                "gtm" => (isset($gtm[$ad_slot->product_id]) ? $gtm[$ad_slot->product_id] : "")
                            );
                        }
                        if (isset($product_info[$ad_slot->slot_code])) {
                            $data[$ad_slot->slot_code] = array(
                                'slot_color_code' => $ad_slot->slot_color_code,
                                'slot_title_color' => $ad_slot->slot_title_color,
                                'slot_icon_name' => ($ad_slot->slot_icon_name ? $s3 . $ad_slot->slot_icon_name : null),
                                'slot_title' => $ad_slot->slot_title,
                                'see_more_action' => $ad_slot->see_more_action,
                                'see_more_url' => $ad_slot->see_more_url,
                                'see_more_cate_hierarchy_id' => $ad_slot->see_more_cate_hierarchy_id,
                                'see_more_target_blank' => $ad_slot->see_more_target_blank,
                                'mobile_applicable' => $ad_slot->is_mobile_applicable,
                                'desktop_applicable' => $ad_slot->is_desktop_applicable,
                                'products' => $product_info[$ad_slot->slot_code]
                            );
                        }
                    }
                }
            } elseif ($ad_slot->slot_type == 'IS') {
                if ($ad_slot->data_type == 'PRD') {
                    if ($ad_slot->product_assigned_type == 'C') {
                        if (isset($categoryProducts[$ad_slot->web_category_hierarchy_id])) {
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
                                if ($ad_slot->slot_code == 'H080A') {
                                    $prd_H080A[] = array(
                                        'product_id' => $product->id,
                                        'product_no' => $product->product_no,
                                        'product_name' => $product->product_name,
                                        'product_unit' => $product->uom,
                                        'list_price' => $product->list_price,
                                        'selling_price' => $product->selling_price,
                                        'product_photo' => ($product->displayPhoto ? $s3 . $product->displayPhoto : null),
                                        'mobile_applicable' => $ad_slot->is_mobile_applicable,
                                        'desktop_applicable' => $ad_slot->is_desktop_applicable,
                                        'promotion_desc' => $promotion_desc,
                                        'promotion_label' => (isset($promotional[$product->id]) ? $promotional[$product->id] : null),
                                        "collection" => $collection,
                                        "selling_channel" => $product->selling_channel,
                                        "start_selling" => $product->start_selling_at,
                                        "gtm" => (isset($gtm[$product->id]) ? $gtm[$product->id] : "")
                                    );
                                }
                                if ($ad_slot->slot_code == 'H080B') {
                                    $prd_H080B[] = array(
                                        'product_id' => $product->id,
                                        'product_no' => $product->product_no,
                                        'product_name' => $product->product_name,
                                        'product_unit' => $product->uom,
                                        'list_price' => $product->list_price,
                                        'selling_price' => $product->selling_price,
                                        'product_photo' => ($product->displayPhoto ? $s3 . $product->displayPhoto : null),
                                        'mobile_applicable' => $ad_slot->is_mobile_applicable,
                                        'desktop_applicable' => $ad_slot->is_desktop_applicable,
                                        'promotion_desc' => $promotion_desc,
                                        'promotion_label' => (isset($promotional[$product->id]) ? $promotional[$product->id] : null),
                                        "collection" => $collection,
                                        "selling_channel" => $product->selling_channel,
                                        "start_selling" => $product->start_selling_at,
                                        "gtm" => (isset($gtm[$product->id]) ? $gtm[$product->id] : "")
                                    );
                                }
                            }
                        }
                    } else if ($ad_slot->product_assigned_type == 'P') {
                        if (isset($products[$ad_slot->product_id])) {
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
                                if (isset($rel_category[$ad_slot->product_id]->web_category_hierarchy_id)) {
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
                                        "selling_channel" => $products[$ad_slot->product_id]->selling_channel,
                                        "start_selling" => $products[$ad_slot->product_id]->start_selling_at,
                                        "gtm" => (isset($gtm[$ad_slot->product_id]) ? $gtm[$ad_slot->product_id] : "")
                                    );
                                }
                                $H080A_seemore['see_more_action'] = $ad_slot->see_more_action;
                                $H080A_seemore['see_more_url'] = $ad_slot->see_more_url;
                                $H080A_seemore['see_more_cate_hierarchy_id'] = $ad_slot->see_more_cate_hierarchy_id;
                                $H080A_seemore['see_more_target_blank'] = $ad_slot->see_more_target_blank;
                            }
                            if ($ad_slot->slot_code == 'H080B') {
                                if (isset($rel_category[$ad_slot->product_id]->web_category_hierarchy_id)) {
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
                                        "selling_channel" => $products[$ad_slot->product_id]->selling_channel,
                                        "start_selling" => $products[$ad_slot->product_id]->start_selling_at,
                                        "gtm" => (isset($gtm[$ad_slot->product_id]) ? $gtm[$ad_slot->product_id] : "")
                                    );
                                }
                                $H080B_seemore['see_more_action'] = $ad_slot->see_more_action;
                                $H080B_seemore['see_more_url'] = $ad_slot->see_more_url;
                                $H080B_seemore['see_more_cate_hierarchy_id'] = $ad_slot->see_more_cate_hierarchy_id;
                                $H080B_seemore['see_more_target_blank'] = $ad_slot->see_more_target_blank;
                            }
                        }
                    }
                    if ($ad_slot->slot_code == 'H080A') {
                        $H080A_seemore['see_more_action'] = $ad_slot->see_more_action;
                        $H080A_seemore['see_more_url'] = $ad_slot->see_more_url;
                        $H080A_seemore['see_more_cate_hierarchy_id'] = $ad_slot->see_more_cate_hierarchy_id;
                        $H080A_seemore['see_more_target_blank'] = $ad_slot->see_more_target_blank;
                    }
                    if ($ad_slot->slot_code == 'H080B') {
                        $H080B_seemore['see_more_action'] = $ad_slot->see_more_action;
                        $H080B_seemore['see_more_url'] = $ad_slot->see_more_url;
                        $H080B_seemore['see_more_cate_hierarchy_id'] = $ad_slot->see_more_cate_hierarchy_id;
                        $H080B_seemore['see_more_target_blank'] = $ad_slot->see_more_target_blank;
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
                            'campaign_url_code' => $ad_slot->url_code,
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
                            'campaign_url_code' => $ad_slot->url_code,
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
                'see_more_action' => $H080A_seemore['see_more_action'] ?? null,
                'see_more_url' => $H080A_seemore['see_more_url'] ?? null,
                'see_more_cate_hierarchy_id' => $H080A_seemore['see_more_cate_hierarchy_id'] ?? null,
                'see_more_target_blank' => $H080A_seemore['see_more_target_blank'] ?? null,
                'images' => $img_H080A,
                'products' => $prd_H080A
            );
        }
        if (!$img_H080B && !$prd_H080B) {
            unset($data['H080B']);
        } else {
            $data['H080B'] = array(
                'see_more_action' => $H080B_seemore['see_more_action'] ?? null,
                'see_more_url' => $H080B_seemore['see_more_url'] ?? null,
                'see_more_cate_hierarchy_id' => $H080B_seemore['see_more_cate_hierarchy_id'] ?? null,
                'see_more_target_blank' => $H080B_seemore['see_more_target_blank'] ?? null,
                'images' => $img_H080B,
                'products' => $prd_H080B
            );
        }
        return $data;
    }

    public function getUTM($params = null)
    {
        $result = LookupValuesV::select('udf_01 as url')
            ->where('type_code', 'UTM_PAGE')
            ->where('code', $params)
            ->where('active', 1)->get();
        return $result;
    }


}
