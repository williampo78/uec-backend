<?php


namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\MemberNotes;
use App\Models\MemberCollections;
use App\Models\ProductPhotos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class APIWebService
{

    /**
     * 取得會員收件人
     * @param
     * @return string
     */
    public function getMemberNotes()
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $member_notes = MemberNotes::select('id', 'note_type', 'name', 'mobile', 'telephone', 'telephone_ext', 'email', 'zip_code', 'city_id', 'city_name', 'district_id', 'district_name', 'address', 'cvs_type', 'cvs_store_no', 'is_default')
            ->where('member_id', '=', $member_id)->get();
        return $member_notes;
    }


    /**
     * 更新會員收件人
     * @param
     * @return string
     */
    public function updateMemberNotes($input, $id)
    {
        $member_id = Auth::guard('api')->user()->member_id;

        $data = MemberNotes::where('id', $id)->where('member_id', $member_id)->get()->toArray();
        if (count($data) == 0) {
            return false;
        }
        DB::beginTransaction();
        try {
            $webData = [];
            $webData['note_type'] = $input['note_type'];
            $webData['email'] = $input['email'];
            $webData['name'] = $input['name'];
            $webData['mobile'] = $input['mobile'];
            $webData['telephone'] = $input['telephone'];
            $webData['telephone_ext'] = $input['telephone_ext'];
            $webData['zip_code'] = $input['zip_code'];
            $webData['city_name'] = $input['city_name'];
            $webData['city_id'] = $input['city_id'];
            $webData['district_name'] = $input['district_name'];
            $webData['district_id'] = $input['district_id'];
            $webData['address'] = $input['address'];
            $webData['cvs_type'] = isset($input['cvs_type']) ? $input['cvs_type'] : null;
            $webData['cvs_store_no'] = isset($input['cvs_store_no']) ? $input['cvs_store_no'] : null;
            $webData['is_default'] = $input['is_default'];
            $webData['updated_by'] = $member_id;

            MemberNotes::where('id', $id)->where('member_id', $member_id)->update($webData);
            DB::commit();
            if ($id > 0) {
                $result = true;
            } else {
                $result = false;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = false;
        }

        return $result;
    }


    /**
     * 刪除會員收件人
     * @param
     * @return string
     */
    public function deleteMemberNotes($id)
    {
        $member_id = Auth::guard('api')->user()->member_id;

        $data = MemberNotes::where('id', $id)->where('member_id', $member_id)->get()->toArray();
        if (count($data) == 0) {
            return false;
        }
        DB::beginTransaction();
        try {
            MemberNotes::where('id', $id)->where('member_id', $member_id)->delete();
            DB::commit();
            if ($id > 0) {
                $result = true;
            } else {
                $result = false;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = false;
        }

        return $result;
    }


    /**
     * 新增會員收件人
     * @param
     * @return string
     */
    public function createMemberNotes($input)
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $now = Carbon::now();
        DB::beginTransaction();
        try {
            $webData = [];
            $webData['member_id'] = $member_id;
            $webData['note_type'] = $input['note_type'];
            $webData['email'] = $input['email'];
            $webData['name'] = $input['name'];
            $webData['mobile'] = $input['mobile'];
            $webData['telephone'] = $input['telephone'];
            $webData['telephone_ext'] = $input['telephone_ext'];
            $webData['zip_code'] = $input['zip_code'];
            $webData['city_name'] = $input['city_name'];
            $webData['city_id'] = $input['city_id'];
            $webData['district_name'] = $input['district_name'];
            $webData['district_id'] = $input['district_id'];
            $webData['address'] = $input['address'];
            $webData['cvs_type'] = isset($input['cvs_type']) ? $input['cvs_type'] : null;
            $webData['cvs_store_no'] = isset($input['cvs_store_no']) ? $input['cvs_store_no'] : null;
            $webData['is_default'] = $input['is_default'];
            $webData['created_by'] = $member_id;
            $webData['updated_by'] = -1;
            $webData['created_at'] = $now;
            $webData['updated_at'] = $now;
            $new_id = MemberNotes::insertGetId($webData);
            DB::commit();
            if ($new_id > 0) {
                $result = true;
            } else {
                $result = false;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = false;
        }

        return $result;
    }


    /**
     * 取得會員收藏商品
     * @param
     * @return string
     */
    public function getMemberCollections()
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $collects = DB::table('member_collections')->select('products.product_name', 'products.selling_price','products.list_price')
            ->Join('products','member_collections.product_id','=','products.id')
            ->leftJoin(DB::raw("(select main(sort) as sort, photo_name,product_id
                          from products
                          group by sort
                        ) as photo"),
                function ($join) {
                    $join->on('photo.product_id', '=', 'products.id');
                })
            ->where('member_collections.member_id', '=', $member_id)->get();
        return $collects;
    }

}
