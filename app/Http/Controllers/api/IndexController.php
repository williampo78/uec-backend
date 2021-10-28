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
                $items[$v['parent_code']][$k]['content_name'] = $v['content_name'];
                $items[$v['parent_code']][$k]['content_target'] = $v['content_target'];
                $items[$v['parent_code']][$k]['content_url'] = $v['content_url'];
            }

            $data[] = array(
                "fieldId" => $code, //ABOUT_US
                "fieldValue" => $name, //關於我們
                "fieldItems" => $items[$v['parent_code']]
            );
        }
        return response()->json(['status' => true, 'reuslt' => $data]);
    }
}
