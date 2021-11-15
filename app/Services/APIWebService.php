<?php


namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\MemberNotes;
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
        $member_notes = MemberNotes::select('id','note_type', 'name', 'mobile', 'telephone', 'telephone_ext', 'email', 'zip_code', 'city_id', 'district_id', 'address', 'cvs_type', 'cvs_store_no', 'is_default')
            ->where('member_id', '=', $member_id)->get();
        return  $member_notes;
    }


    /**
     * 更新會員收件人
     * @param
     * @return string
     */
    public function updateMemberNotes($input, $id)
    {
        $member_id = Auth::guard('api')->user()->member_id;
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
            $webData['cvs_type'] = isset($input['cvs_type'])?$input['cvs_type']:null;
            $webData['cvs_store_no'] = isset($input['cvs_store_no'])?$input['cvs_store_no']:null;
            $webData['is_default'] = $input['is_default'];
            $webData['updated_by'] = $member_id;

            MemberNotes::where('id' , $id)->where('member_id' , $member_id)->update($webData);
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

}
