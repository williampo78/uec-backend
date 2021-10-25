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
}
