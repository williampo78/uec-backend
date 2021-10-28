<?php


namespace App\Services;

use App\Models\Lookup_values_v;
use App\Models\WebContents;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WebContentsService
{
    /*
     * $apply_to => 'QA_CATEGORY' or 'FOOTER_CATEGORY"'
     */
    public function getCategory($apply_to)
    {
        $lookup = Lookup_values_v::where('type_code', '=', $apply_to)->where('active', '=', '1')->orderBy('sort', 'ASC')->get();
        return $lookup;
    }

    /*
     * $apply_to => 'QA' or 'FOOTER'
     */
    public function getFooter($data, $apply_to)
    {
        $webcontents = WebContents::where('apply_to', '=', $apply_to);
        if (isset($data['code'])) {
            $webcontents->where('parent_code', '=', $data['code']);
        }

        if (isset($data['content_name'])) {
            $webcontents->where('content_name', 'like', '%' . $data['content_name'] . '%');
        }

        if (isset($data['active'])) {
            $webcontents->where('active', $data['active']);
        }
        $webcontents = $webcontents->orderBy('parent_code', 'asc')->orderBy('sort', 'asc')->get();
        return $webcontents;
    }

    public function addWebContent($inputdata, $act)
    {
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        DB::beginTransaction();
        try {
            $webData = [];
            $webData['agent_id'] = Auth::user()->agent_id;
            $webData['apply_to'] = $inputdata['apply_to'];
            $webData['parent_code'] = $inputdata['parent_code'];
            $webData['active'] = $inputdata['active'];
            $webData['content_name'] = $inputdata['content_name'];
            $webData['sort'] = $inputdata['sort'];
            $webData['content_target'] = isset($inputdata['content_target'])?$inputdata['content_target']:null;
            $webData['content_url'] = isset($inputdata['content_url'])?$inputdata['content_url']:null;
            $webData['content_text'] = $inputdata['content_text'];
            $webData['created_by'] = $user_id;
            $webData['created_at'] = $now;
            $webData['updated_by'] = $user_id;
            $webData['updated_at'] = $now;
            if ($act == 'add') {
                $new_id = WebContents::insertGetId($webData);
            } else if ($act =='upd') {
                WebContents::where('id' , $inputdata['id'])->update($webData);
                $new_id = $inputdata['id'];
            }
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
}
