<?php


namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\MemberNotes;
use App\Models\MemberCollections;
use App\Models\ProductPhotos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Batch;

class APIWebService
{

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

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
            if ($input['is_default'] == 1) {//設為預設時，將其他同note_type設為0
                $webData = [];
                $webData['is_default'] = 0;
                MemberNotes::where('member_id', $member_id)->where('note_type', '=', $input['note_type'])->update($webData);
            }
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
        $s3 = config('filesystems.disks.s3.url');
        $collection = [];
        $member_id = Auth::guard('api')->user()->member_id;
        $collects = DB::table('member_collections')->select('products.id', 'products.product_name', 'products.selling_price', 'products.list_price')
            ->Join('products', 'member_collections.product_id', '=', 'products.id')
            ->where('member_collections.member_id', '=', $member_id)
            ->where('member_collections.status', '=', 0)->get();
        foreach ($collects as $collect) {
            $photo = ProductPhotos::select('photo_name')->where('product_id', '=', $collect->id)->orderBy('sort', 'ASC')->first()->toArray();
            $discount = ($collect->list_price == 0 ? 0 : ceil(($collect->selling_price / $collect->list_price) * 100));
            //echo $discount;
            $collection[] = array('product_id' => $collect->id, 'product_name' => $collect->product_name, 'selling_price' => intval($collect->selling_price), 'product_discount' => intval($discount), 'product_photo' => ($photo['photo_name'] ? $s3 . $photo['photo_name'] : null));
        }

        return json_encode($collection);
    }


    /**
     * 新增/刪除會員收藏商品
     * @param
     * @return string
     */
    public function setMemberCollections($input)
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $now = Carbon::now();
        $data = MemberCollections::where('product_id', $input['product_id'])->where('member_id', $member_id)->get()->toArray();
        if (count($data) > 0) {
            $act = 'upd';
        } else {
            $act = 'add';
        }
        DB::beginTransaction();
        try {
            $webData = [];
            $webData['member_id'] = $member_id;
            $webData['product_id'] = $input['product_id'];
            $webData['status'] = $input['status'];
            $webData['created_by'] = $member_id;
            $webData['updated_by'] = -1;
            $webData['created_at'] = $now;
            $webData['updated_at'] = $now;
            if ($act == 'add') {
                if ($input['status'] == '-1') {
                    return '203';
                }
                $new_id = MemberCollections::insertGetId($webData);
            } else if ($act == 'upd') {
                MemberCollections::where('product_id', $input['product_id'])->where('member_id', $member_id)->update($webData);
                $new_id = $input['product_id'];
            }

            DB::commit();
            if ($new_id > 0) {
                $result = 'success';
            } else {
                $result = 'fail';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = 'fail';
        }

        return $result;
    }

    /**
     * 批次刪除會員收藏商品
     * @param
     * @return string
     */
    public function deleteMemberCollections($input)
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $now = Carbon::now();
        DB::beginTransaction();
        try {
            $webData = [];
            foreach ($input['product_id'] as $key => $value) {
                $data = MemberCollections::where('product_id', $value)->where('member_id', $member_id)->get()->toArray();
                $webData[$key] = [
                    'id' => $data[0]['id'],
                    'member_id' => $member_id,
                    'product_id' => $value,
                    'status' => -1,
                    'created_by' => $member_id,
                    'updated_by' => -1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            $collectionInstance = new MemberCollections();
            $upd = Batch::update($collectionInstance, $webData, 'id');
            DB::commit();
            if ($upd > 0) {
                $result = 'success';
            } else {
                $result = 'fail';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = 'fail';
        }

        return $result;
    }
}
