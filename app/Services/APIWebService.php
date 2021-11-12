<?php


namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\MemberNotes;

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

}
