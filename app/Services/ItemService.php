<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Item_photo ;
use App\Services\AgentConfigService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemService
{
    private $agentConfigService;

    public function __construct(AgentConfigService $agentConfigService)
    {
        $this->agentConfigService = $agentConfigService;
        // $this->agent_id = Auth::user()->agent_id;
    }

    //  自動重新計算庫存數量
    public function updateItem()
    {
        $agnet_config = $this->agentConfigService->getAgentConfig();
        // 驗收後才入庫 true/false
        $config_info = json_decode($agnet_config['value'], true);

        if ($config_info['acceptance']) {
            $filter_order = " and stock_log.type in ('sales','sales_return','purchase_acceptance','purchase_return','adjust','transfer','process','requisition') ";
        } else {
            $filter_order = " and stock_log.type in ('sales','sales_return','purchase','purchase_return','adjust','transfer','process','requisition') ";
        }

        $cmd = "Update `item`
          inner join
          (
            select item_id, new_item_number, sum(`item_qty`) as sum_item_qty
            from
            (
              select `stock_log`.`item_id`, item.number as new_item_number, `stock_log`.`item_qty`
              from `stock_log`
              left join item  on stock_log.item_id = item.id
              where `stock_log`.agent_id = '" . $this->agent_id . "'
              and stock_log.trade_date <= '" . date("Y-m-t") . "'
              and stock_log.active != 0
              " . $filter_order . "

              UNION ALL

              select item_id, item_number as new_item_number, adjust_qty as item_qty
              from cost_adjust
              left join cost_adjust_detail on cost_adjust.id = cost_adjust_detail.cost_adjust_id
              left join item  on cost_adjust_detail.item_id = item.id
              where `cost_adjust`.agent_id = '" . $this->agent_id . "'
              and trade_date <= '" . date("Y-m-t") . "'
              and cost_adjust.active != 0
            ) as table1
            group by table1.item_id
            order by table1.new_item_number asc
          ) as stock_info on `item`.id = stock_info.item_id
          set `stock_qty` = stock_info.sum_item_qty
          ";

        try {
            DB::select($cmd);
        } catch (\Exception $e) {
            Log::info($e);
        }

        return true;
    }
    public function getItem($category = 1, $id = null)
    {
        $agent_id = Auth::user()->agent_id;
        if ($category != "%" and $category != "") {
            $where_type = " and item.active = '" . $category . "'";
        } else if ($category == "%") {
            $where_type = "";
        } else {
            $where_type = " and item.active = '1'";
        }

        $where_type = '';

        $log_table = DB::table('stock_log')->select('item_id', 'item_number', DB::raw('item_price as last_price'))
            ->leftJoin(DB::raw("(select max(stock_log.id) as max_id
                          from stock_log
                          where type = 'purchase'
                          and item_price != 0
                          and agent_id = '" . $agent_id . "'
                          group by stock_log.item_id
                        ) as table_log"),
                function ($join) {
                    $join->on('table_log.max_id', '=', 'stock_log.id');
                })
            ->whereNotNull('table_log.max_id');

        $rs = Item::select(DB::raw('item.*'))
            ->leftJoin('category', 'item.category_id', '=', 'category.id')
            ->leftJoin('supplier', 'item.supplier_id', '=', 'supplier.id')
            ->leftJoinSub($log_table, 'log_table', function ($join) {
                $join->on('log_table.item_id', '=', 'item.id');
            })
            ->where('item.agent_id', $agent_id)
            ->where('category.id', 'like', $category . $where_type)
            ->orderBy('item.number')
            ->orderBy('item.brand')
            ->orderBy('item.name')
            ->orderBy('item.spec');

        if (!is_null($id)) {
            $rs->where('item.id', $id);
        }
        $rs->get();
        return $rs;
    }
    public function edit($data)
    {

    }
    public function insertData($data)
    {
        $result = [];
        $data['agent_id'] = Auth::user()->agent_id;
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['live_times'] = $data['live_times'] * 365; //有效期限
        $data['sell_price2'] = 0; // 銷售價格2 ?
        $data['ratio'] = 0; //比率 ?
        $data['photo_name'] = '0'; //照片名稱
        $data['test_remark'] = '?'; //測試備註?
        try {
            $insert = Item::create($data);
            $result['status'] = true;
            $result['id'] = $insert->id;
        } catch (\Exception $e) {
            Log::info($e);
        }
        return $result;
    }
    public function update($data, $id)
    {
        try {
            $result = Item::where('id', $id)->update($data);
        } catch (\Exception $e) {
            Log::info($e);
        }

        return $result;
    }
    public function Get_Item_photo($id){
        try {

            $result = Item_photo::where('item_id', $id)->orderBy('sort', 'asc')->get()->toArray();
        } catch (\Exception $e) {
            Log::info($e);
        }
        return $result;
    }
    /**
     *  $itemId : 新建或刪除時 會需要使用 item 對應到  table item_photo item_id
     *  $requestFile : 從控制器請求的檔案丟到服務執行
     *  $method  : 有創建跟修改的方式 創建則有值的話 直接寫入 如果是修改要另外判斷 $readDelId and $oldFiles
     *  $inputData :  oldFiles  , readDelId
     *  $inputData['oldFiles'] : 如果有新上傳的圖片要蓋掉原先的圖片
     *  $inputData['readDelId'] : 放入要刪除的照片
     */
    public function uploadImage($itemId = null, $requestFile = [], $method, $inputData)
    {
        $destinationPath = public_path('/images/item'); //圖片
        $destination_YM = '/'.date('Y') .'/' .date('m') ; //年月
        $destinationPath .= $destination_YM ; // 合併
       
        //file-1 是主要圖片
        //file-2 是以下是次要商品圖片
        foreach($requestFile as $key => $file){
            if($method == 'create'){
                if($key == 'file-1'){
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move($destinationPath, $fileName);
                    $item = Item::find($itemId);
                    $item->photo_name = $destination_YM . '/' .$fileName ;
                    $item->save();
                }else{
                    $replace_example = explode("-",$key);
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move($destinationPath, $fileName);
                    $data['item_id'] = $itemId ;
                    $data['agent_id'] = Auth::user()->agent_id; ;
                    $data['photo_name'] = $destination_YM . '/' .$fileName ;
                    $data['sort'] = $replace_example[1] ;
                    Item_photo::create($data);
                }
            }            
        }
        return true ;
  
    }
}
