<?php

namespace App\Http\Controllers\api;

use App\Models\WebContents;
use App\Services\UniversalService;
use Illuminate\Http\Request;
use App\Services\WebContentsService;
use App\Services\APIService;
use App\Services\APIIndexService;
use Mail;
use Validator;

class IndexController extends Controller
{

    private $webContentsService;
    private $apiService;
    private $apiIndexService;

    public function __construct(WebContentsService $webContentsService, UniversalService $universalService, APIService $apiService, APIIndexService $apiIndexService)
    {
        $this->webContentsService = $webContentsService;
        $this->universalService = $universalService;
        $this->apiService = $apiService;
        $this->apiIndexService = $apiIndexService;
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
        $messages = [
            'contact_name.required' => '姓名不能為空',
            'content_email.required' => 'E-mail不能為空',
            'content_mobile.required' => '手機不能為空',
            'content_text.required' => '問題/意見不能為空',
        ];

        $v = Validator::make($request->all(), [
            'contact_name' => 'required',
            'content_email' => 'required',
            'content_mobile' => 'required',
            'content_text' => 'required',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => '資料錯誤', 'result' => $v->errors()]);
        }

        //通知客服
        $data = array();
        $data['name'] =  $request['contact_name'];
        $data['email'] = $request['content_email'];
        $data['mobile']= $request['content_mobile'];
        $data['tel'] =   $request['content_tel'];
        $data['content_text'] = $request['content_text'];

        Mail::send('mail.contact', $data, function ($message ) use($data) {
            $message->to(array("rowena@u-ark.com"))->subject(config('uec.mailPrefix').' 與我們聯繫');
        });
        return response()->json(['status' => true, 'error_code' => null, 'error_msg' => null, 'result' => '信件發送成功']);

    }

    public function getAdSlots(Request $request)
    {
        $result = $this->apiIndexService->getIndex($request['ad']);
        return response()->json(['status' => true, 'error_code' => null, 'error_msg' => null, 'result' => $result]);
    }

    public function getQA()
    {
        $level1 = $this->universalService->getFooterCategory('QA_CATEGORY');
        $data = [];
        $items = [];
        foreach ($level1 as $code => $name) {
            $input['code'] = $code;
            $level2 = $this->webContentsService->getFooter($input, 'QA');
            foreach ($level2 as $k => $v) {
                $items[$k]['sort'] = $v['sort'];
                $items[$k]['question'] = $v['content_name'];
                $items[$k]['answer'] = $v['content_text'];
            }

            $data[] = array(
                "code" => $code,
                "name" => $name,
                "list" => $items
            );
        }
        return response()->json(['status' => true, 'error_code' => null, 'error_msg' => null, 'result' => $data]);
    }

}
