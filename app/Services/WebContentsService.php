<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\WebContent;
use App\Models\LookupValuesV;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class WebContentsService
{
    /*
     * $apply_to => 'QA_CATEGORY' or 'FOOTER_CATEGORY"'
     */
    public function getCategory($apply_to)
    {
        $lookup = LookupValuesV::where('type_code', '=', $apply_to)->where('active', '=', '1')->orderBy('sort', 'ASC')->get();
        return $lookup;
    }

    /*
     * $apply_to => 'QA' or 'FOOTER'
     */
    public function getFooter($data, $apply_to)
    {
        $webcontents = WebContent::where('apply_to', '=', $apply_to);
        if (isset($data['code'])) {
            $webcontents->where('parent_code', '=', $data['code']);
        }

        if (isset($data['content_name'])) {
            $webcontents->where('content_name', 'like', '%' . $data['content_name'] . '%');
        }

        if (isset($data['active'])) {
            $webcontents->where('active', $data['active']);
        }

        if (isset($data['target'])) {
            $webcontents->where('content_target', $data['target']);
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
            $webData['content_target'] = isset($inputdata['content_target']) ? $inputdata['content_target'] : null;
            $webData['content_url'] = isset($inputdata['content_url']) ? $inputdata['content_url'] : null;
            $webData['content_text'] = $inputdata['content_text'];
            if ($inputdata['apply_to'] == 'FOOTER') {
                if ($webData['content_target'] == 'S' || $webData['content_target'] == 'B') {
                    $webData['content_text'] = null;
                } else if ($webData['content_target'] == 'H') {
                    $webData['content_url'] = null;
                } else {
                    $webData['content_url'] = null;
                    $webData['content_text'] = null;
                }
            }
            $webData['created_by'] = $user_id;
            $webData['created_at'] = $now;
            $webData['updated_by'] = $user_id;
            $webData['updated_at'] = $now;
            if ($act == 'add') {
                $new_id = WebContent::insertGetId($webData);
            } else if ($act == 'upd') {
                WebContent::where('id', $inputdata['id'])->update($webData);
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

    /**
     * 商城頁面內容設定
     *
     * @param array $datas
     * @return Collection
     */
    public function getWebContents(array $datas = []): Collection
    {
        $agent_id = Auth::user()->agent_id;

        $web_contents = WebContent::select(
            'web_contents.*',
            'lookup_values_v.description'
        )
            ->leftJoin('lookup_values_v', 'web_contents.parent_code', '=', 'lookup_values_v.code')
            ->where('web_contents.agent_id', $agent_id)
            ->where('lookup_values_v.agent_id', $agent_id);

        if (isset($datas['id'])) {
            $web_contents = $web_contents->where('web_contents.id', $datas['id']);
        }

        // 類別代碼
        if (isset($datas['type_code'])) {
            $web_contents = $web_contents->where('lookup_values_v.type_code', $datas['type_code']);
        }

        // 應用層面
        if (isset($datas['apply_to'])) {
            $web_contents = $web_contents->where('web_contents.apply_to', $datas['apply_to']);
        }

        $web_contents = $web_contents->orderBy('web_contents.parent_code', 'asc')
            ->orderBy('web_contents.sort', 'asc')
            ->get();

        return $web_contents;
    }
}
