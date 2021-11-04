<?php

namespace App\Http\Controllers\api;

use App\Models\WebContents;
use App\Services\UniversalService;
use Illuminate\Http\Request;
use App\Services\WebContentsService;
use App\Services\APIService;

class IndexController extends Controller
{

    private $webContentsService;
    private $apiService;

    public function __construct(WebContentsService $webContentsService, UniversalService $universalService, APIService $apiService)
    {
        $this->webContentsService = $webContentsService;
        $this->universalService = $universalService;
        $this->apiService = $apiService;
    }

    public function index()
    {
        $level1 = $this->universalService->getFooterCategory('FOOTER_CATEGORY');
        $data = [];
        $items = [];
        foreach ($level1 as $code => $name) {
            $input['code'] = $code;
            $level2 = $this->webContentsService->getFooter($input, 'FOOTER');
            foreach ($level2 as $k => $v) {
                $items[$v['parent_code']][$k]['content_id'] = $v['id'];
                $items[$v['parent_code']][$k]['content_name'] = $v['content_name'];
                $items[$v['parent_code']][$k]['content_target'] = $v['content_target'];
                $items[$v['parent_code']][$k]['content_url'] = $v['content_url'];
            }

            $data[] = array(
                "field_id" => $code, //ABOUT_US
                "field_value" => $name, //關於我們
                "field_items" => $items[$v['parent_code']]
            );
        }
        return response()->json(['status' => true, 'error_code' => null, 'error_msg' => null, 'result' => $data]);
    }

    public function getContent($id)
    {
        $status = false;
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $content = WebContents::where('id', '=', $id)->where('content_target', '=', 'H')->get()->toArray();
        if (sizeof($content) > 0) {
            $data[] = array(
                "content_id" => $content[0]['id'], //9
                "content_name" => $content[0]['content_name'], //會員服務條款
                "content_text" => $content[0]['content_text']//html內容
            );
            $status= true;
        } else {
            $data = [];
            $status = false;
            $err = '201';
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $data]);
    }

    public function postContact(Request $request)
    {

    }

}
