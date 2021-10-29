<?php

namespace App\Http\Controllers\api;

use App\Services\UniversalService;
use Illuminate\Http\Request;
use App\Services\WebContentsService;

class IndexController extends Controller
{

    private $webContentsService;

    public function __construct(WebContentsService $webContentsService, UniversalService $universalService)
    {
        $this->webContentsService = $webContentsService;
        $this->universalService = $universalService;
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
                $items[$v['parent_code']][$k]['content_text'] = $v['content_text'];
            }

            $data[] = array(
                "field_id" => $code, //ABOUT_US
                "field_value" => $name, //關於我們
                "field_items" => $items[$v['parent_code']]
            );
        }
        return response()->json(['status' => true, 'error_code' => null, 'error_msg' => null, 'reuslt' => $data]);
    }
}
