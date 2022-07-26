<?php

namespace App\Services;

use ImageUpload;
use Carbon\Carbon;
use App\Models\CategoryProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\WebCategoryHierarchy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WebCategoryHierarchyService
{
    private const CATEGORY_UPLOAD_PATH_PREFIX = 'category_icon/';

    public function del_Category_Hierarchy($in)
    {
        $resut = [];
        $CategoryProductsCount = CategoryProduct::where('web_category_hierarchy_id', $in['id'])->get()->count();
        $CategoryHierarchyCount = WebCategoryHierarchy::where('parent_id', $in['id'])->get()->count();
        $resut['Msg_Hierarchy'] = '';
        $resut['Msg_Products'] = '';
        $resut['Msg'] = '';

        $resut['status'] = true;
        if ($CategoryHierarchyCount !== 0) {
            $resut['Msg_Hierarchy'] = '請將該分類底下的分類清空才能執行該操作';
            $resut['status'] = false;
        }
        if ($CategoryProductsCount !== 0) {
            $resut['Msg_Products'] = '請先將分類階層有使用到該分類的品項刪除,才能執行該操作';
            $resut['status'] = false;
        }
        if ($resut['status']) {
            WebCategoryHierarchy::where('id', $in['id'])->delete();
            $resut['status'] = true;
            $resut['Msg'] = '刪除成功';
        }

        return $resut;
    }

    public function sort_Category_Hierarchy($in)
    {
        $sort = json_decode($in['JsonData'], true);
        foreach ($sort as $key => $val) {
            WebCategoryHierarchy::where('id', $val['id'])->update(['sort' => $key]);
        }

        return true;
    }

    public function getSort($in)
    {

        $query = WebCategoryHierarchy::where('parent_id', $in['parent_id'])
            ->where('category_level', $in['category_level'])
            ->orderBy('sort', 'desc')
            ->first();
        $sort = 0;
        if ($query !== null) {
            $sort = $query->sort += 1;
        }

        return $sort;
    }

    //取得開關分類判斷輸出內容
    public function getCategoryHierarchyContents($input = [])
    {
        $confi_levels = config('uec.web_category_hierarchy_levels');
        $keyword = '';
        $where = '';
        if (isset($input['id']) && $input['id'] !== '') {
            $where .= "AND id = " . $input['id'];
        }
        if (isset($input['active']) && $input['active'] !== '') {
            $where .= " AND active = " . $input['active'];
        }

        if (isset($input['exclude_content_type']) && $input['exclude_content_type']) {
            $where .= " AND content_type <> " . $input['exclude_content_type'];
        }
        if ($confi_levels == 2) {
            if (isset($input['keyword']) && $input['keyword'] !== '') {
                $keyword .= "WHERE concat(level_one.category_name, '>', level_two.category_name )  LIKE '%" . $input['keyword'] . "%' ";
            }
        } else {
            if (isset($input['keyword']) && $input['keyword'] !== '') {
                $keyword .= "WHERE concat(level_one.category_name, '>', level_two.category_name , ' > ' ,level_three.category_name)  LIKE '%" . $input['keyword'] . "%' ";
            }
        }

        if ($confi_levels == 2) {
            $query = "SELECT level_one.id as level_one_id ,level_two.id as id, level_two.meta_title , CONCAT( level_one.category_name, ' > ', level_two.category_name ) as name, level_two.active,
            level_two.content_type , level_two.promotion_campaign_id,'' as meta_title ,level_two.meta_description ,level_two.content_type , level_two.meta_keywords
            FROM (SELECT * FROM web_category_hierarchy WHERE category_level = 1 ) level_one
            JOIN ( SELECT * FROM web_category_hierarchy WHERE category_level = 2 " . $where . ") level_two ON level_two.parent_id = level_one.id
            " . $keyword . " ORDER BY level_one.category_name, level_two.category_name";
        } else {
            $query = "SELECT level_one.id as level_one_id, level_three.id as id, CONCAT( level_one.category_name, ' > ', level_two.category_name , ' > ' ,level_three.category_name) as name, level_three.active,
            level_three.content_type  ,level_three.meta_title ,level_three.meta_description,level_three.content_type ,level_three.meta_keywords,level_three.promotion_campaign_id
            FROM ( SELECT id , category_name FROM web_category_hierarchy WHERE category_level = 1 ) level_one
            JOIN ( SELECT * FROM web_category_hierarchy WHERE category_level = 2 ) level_two ON level_two.parent_id = level_one.id
            JOIN ( SELECT * FROM web_category_hierarchy WHERE category_level = 3 " . $where . ") level_three ON level_three.parent_id = level_two.id
            " . $keyword . " ORDER BY level_one.category_name, level_two.category_name , level_three.category_name";
        }

        return DB::select($query);
    }

    public function categoryProductsHierarchyId($id)
    {
        $result = CategoryProduct::where('web_category_hierarchy_id', $id)
            ->join('products_v', 'products_v.id', '=', 'web_category_products.product_id')
            ->select('web_category_products.id as web_category_products_id', 'web_category_products.product_id as product_id', 'products_v.*')
            ->get();

        return $result;
    }

    public function categoryProductsId($id)
    {
        $confi_levels = config('uec.web_category_hierarchy_levels');
        $result = CategoryProduct::where('web_category_products.product_id', $id)
            ->leftJoin('web_category_hierarchy', 'web_category_hierarchy.id', '=', 'web_category_products.web_category_hierarchy_id')
            ->select('web_category_products.web_category_hierarchy_id', 'web_category_hierarchy.category_name', 'web_category_products.sort', 'web_category_hierarchy.active')
            ->where('web_category_hierarchy.category_level', $confi_levels)
            ->orderBy('web_category_products.sort', 'ASC')
            ->get();

        return $result;
    }

    public function get_products_v($in)
    {
        $agent_id = Auth::user()->agent_id;
        $query = DB::table('products_v')
            ->select(DB::raw('products_v.*'), DB::raw('supplier.name as supplier_name'))
            ->leftJoin('supplier', 'products_v.supplier_id', '=', 'supplier.id')
            ->where('products_v.agent_id', $agent_id);
        if (isset($in['create_start_date']) && isset($in['create_end_date'])) {
            if ($in['create_start_date'] !== '' && $in['create_end_date'] !== '') {
                $query->where('products_v.created_date', '>=', $in['create_start_date'])
                    ->where('products_v.created_date', '<=', $in['create_end_date']);
            };
        };
        if (isset($in['filter_product_id']) && $in['filter_product_id'] !== '') {
            $query->whereNotIn('products_v.id', $in['filter_product_id']);
        };
        if (isset($in['product_no']) && $in['product_no'] !== '') {
            $query->where('product_no', $in['product_no']);
        };
        if (isset($in['select_start_date']) && isset($in['select_end_date'])) {
            if ($in['select_start_date'] !== '' && $in['select_end_date'] !== '') {
                $query->where('start_launched_at', '>=', $in['select_start_date'])
                    ->where('end_launched_at', '<=', $in['select_start_date']);
            };
        }
        if (isset($in['supplier_id']) && $in['supplier_id'] !== '') {
            $query->where('products_v.supplier_id', $in['supplier_id']);
        }
        if (isset($in['product_name']) && $in['product_name'] !== '') {
            $query->where('products_v.product_name', $in['product_name']);
        }
        if (isset($in['selling_price_max']) && $in['selling_price_max'] !== '') {
            $query->where('products_v.selling_price', '>=', $in['selling_price_max']);
        }
        if (isset($in['selling_price_min']) && $in['selling_price_min'] !== '') {
            $query->where('products_v.selling_price', '<=', $in['selling_price_min']);
        }

        return $query->get();
    }

    public function edit_category_hierarchy_content($in, $id)
    {
        $result = false;
        $category_products_list = json_decode($in['category_products_list_json'], true);
        $user_id = Auth::user()->id;
        $now = Carbon::now();

        DB::beginTransaction();
        try {
            WebCategoryHierarchy::where('id', $id)->update([
                'active' => $in['active'],
                'meta_title' => $in['meta_title'],
                'meta_description' => $in['meta_description'],
                'meta_keywords' => $in['meta_keyword'],
                'content_type' => $in['content_type'],
                'promotion_campaign_id' => $in['content_type'] == "M" ? $in['promotion_campaign_id'] : null,
            ]);
            foreach ($category_products_list as $key => $val) {
                if (!isset($val['web_category_products_id']) || $val['web_category_products_id'] == '') {
                    DB::table('web_category_products')->insert([
                        'web_category_hierarchy_id' => $id,
                        'product_id' => $val['product_id'],
                        'sort' => 0,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = false;
        }

        return $result;
    }

    public function del_category_hierarchy_content($id)
    {
        return DB::table('web_category_products')->where('id', $id)->delete();
    }

    public function DelCategoryInProduct($in)
    {
        return DB::table('web_category_products')->where('web_category_hierarchy_id', $in['category_id'])->where('product_id', $in['product_id'])->delete();
    }

    public function DelRelatedProducts($id)
    {
        return DB::table('related_products')->where('id', $id)->delete();
    }

    public function getRomotionalCampaigns($in)
    {
        $now = Carbon::now();
        $promotionalCampaigns = DB::table('promotional_campaigns')->where('level_code', 'CART_P');

        if (!empty($in['promotional_campaigns_time_type'])) {
            // if ($in['promotional_campaigns_time_type'] == 'all') {
            // }
            if ($in['promotional_campaigns_time_type'] == 'not_expired') {
                $promotionalCampaigns->where('end_at', '>=', $now);
            }
        }

        if (!empty($in['promotional_campaigns_key_word'])) {
            $promotionalCampaigns->Where('campaign_name', 'like', '%' . $in['promotional_campaigns_key_word'] . '%')
                ->orWhere('campaign_brief', 'like', '%' . $in['promotional_campaigns_key_word'] . '%');
        }

        if (!empty($in['id'])) {
            $promotionalCampaigns->where('id', $in['id']);
        };

        return $promotionalCampaigns->get();
    }

    public function check_icon_name($id)
    {
        $CategoryHierarchy = WebCategoryHierarchy::where('id', $id)->first();
        if ($CategoryHierarchy->icon_name) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 取得同一層分類
     *
     * @param integer|null $parentId
     * @return Collection
     */
    public function getSiblingCategories(?int $parentId = null): Collection
    {
        return WebCategoryHierarchy::where('parent_id', $parentId)
            ->defaultOrder()
            ->get();
    }

    /**
     * 新增分類
     *
     * @param array $data
     * @return array
     */
    public function createCategory(array $data): array
    {
        $user = auth()->user();
        $isSuccess = false;

        DB::beginTransaction();
        try {
            // 根節點
            if ($data['category_level'] < 2) {
                $createdCategory = WebCategoryHierarchy::create([
                    'agent_id' => $user->agent_id,
                    'category_name' => $data['category_name'],
                    'category_level' => $data['category_level'],
                    'gross_margin_threshold' => $data['gross_margin_threshold'],
                    'category_short_name' => $data['category_short_name'],
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);

                if (!empty($data['icon_name'])) {
                    $uploadPath = self::CATEGORY_UPLOAD_PATH_PREFIX . $createdCategory->id;
                    $uploadFileResult = ImageUpload::uploadImage($data['icon_name'], $uploadPath, 'category_icon');
                    $createdCategory->update([
                        'icon_name' => $uploadFileResult['image'],
                    ]);
                }
            } else {
                $parentCategory = WebCategoryHierarchy::findOrFail($data['parent_id']);
                $createdCategory = $parentCategory->children()->create([
                    'agent_id' => $user->agent_id,
                    'category_name' => $data['category_name'],
                    'category_level' => $data['category_level'],
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }

            DB::commit();
            $isSuccess = true;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }

        return [
            'is_success' => $isSuccess,
            'category' => $createdCategory ?? null,
        ];
    }

    /**
     * 更新分類
     *
     * @param integer $id
     * @param array $data
     * @return array
     */
    public function updateCategory(int $id, array $data): array
    {
        $user = auth()->user();
        $isSuccess = false;

        DB::beginTransaction();
        try {
            $category = WebCategoryHierarchy::findOrFail($id);

            $categoryData = [
                'updated_by' => $user->id,
            ];

            if (!empty($data['category_name'])) {
                $categoryData['category_name'] = $data['category_name'];
            }

            if ($category->category_level < 2) {
                $categoryData['gross_margin_threshold'] = $data['gross_margin_threshold'];

                if (!empty($data['category_short_name'])) {
                    $categoryData['category_short_name'] = $data['category_short_name'];
                }

                $category->update($categoryData);

                if (!empty($data['icon_name'])) {
                    // 移除舊圖片
                    if (
                        !empty($category->icon_name)
                        && Storage::disk('s3')->exists($category->icon_name)
                    ) {
                        Storage::disk('s3')->delete($category->icon_name);
                    }

                    $uploadPath = self::CATEGORY_UPLOAD_PATH_PREFIX . $category->id;
                    $uploadFileResult = ImageUpload::uploadImage($data['icon_name'], $uploadPath, 'category_icon');
                    $category->update([
                        'icon_name' => $uploadFileResult['image'],
                    ]);
                } elseif ($data['isIconDeleted'] == 'true') {
                    // 移除舊圖片
                    if (
                        !empty($category->icon_name)
                        && Storage::disk('s3')->exists($category->icon_name)
                    ) {
                        Storage::disk('s3')->delete($category->icon_name);
                    }

                    $category->update([
                        'icon_name' => null,
                    ]);
                }
            } else {
                $category->update($categoryData);
            }

            DB::commit();
            $isSuccess = true;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }

        return [
            'is_success' => $isSuccess,
            'category' => $category ?? null,
        ];
    }

    /**
     * 取得祖先和自己的名稱
     *
     * @param integer $id
     * @return string
     */
    public function getAncestorsAndSelfName(int $id): string
    {
        $categories = WebCategoryHierarchy::defaultOrder()->ancestorsAndSelf($id);

        return $categories->count() ? implode(' > ', $categories->pluck('category_name')->toArray()) : '';
    }
}
