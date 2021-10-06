<?php

namespace App\Services;



use App\Models\AgentConfig;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Services\AgentConfigService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ItemService
{
    private $agentConfigService;
    private $agent_id;
    public function __construct(AgentConfigService $agentConfigService)
    {
        $this->agentConfigService = $agentConfigService;
        $this->agent_id = Auth::user()->agent_id;
    }

    //  自動重新計算庫存數量
    public function updateItem()
    {
        $agnet_config = $this->agentConfigService->getAgentConfig();
        // 驗收後才入庫 true/false
        $config_info = json_decode($agnet_config['value'], true);

        if($config_info['acceptance'])
            $filter_order = " and stock_log.type in ('sales','sales_return','purchase_acceptance','purchase_return','adjust','transfer','process','requisition') ";
        else
            $filter_order = " and stock_log.type in ('sales','sales_return','purchase','purchase_return','adjust','transfer','process','requisition') ";

        $cmd = "Update `item`
          inner join
          (
            select item_id, new_item_number, sum(`item_qty`) as sum_item_qty
            from
            (
              select `stock_log`.`item_id`, item.number as new_item_number, `stock_log`.`item_qty`
              from `stock_log`
              left join item  on stock_log.item_id = item.id
              where `stock_log`.agent_id = '".$this->agent_id."'
              and stock_log.trade_date <= '".date("Y-m-t")."'
              and stock_log.active != 0
              ".$filter_order."

              UNION ALL

              select item_id, item_number as new_item_number, adjust_qty as item_qty
              from cost_adjust
              left join cost_adjust_detail on cost_adjust.id = cost_adjust_detail.cost_adjust_id
              left join item  on cost_adjust_detail.item_id = item.id
              where `cost_adjust`.agent_id = '".$this->agent_id."'
              and trade_date <= '".date("Y-m-t")."'
              and cost_adjust.active != 0
            ) as table1
            group by table1.item_id
            order by table1.new_item_number asc
          ) as stock_info on `item`.id = stock_info.item_id
          set `stock_qty` = stock_info.sum_item_qty
          ";

        try {
            DB::select($cmd);
        }catch (\Exception $e){
            Log::info($e);
        }

        return true;
    }

    public function getItem($category){
        if($category != "%" and $category != "" )
            $where_type = " and item.active = '".$category."'";
        else if($category == "%"){
            $where_type = "";
        }else{
            $where_type = " and item.active = '1'";
        }

        $where_type = '';
        $cmd = "select item.* ,  category.name as category_name , supplier.name as supplier_name, log_table.last_price as last_price
                      from item
                      left join category on item.category_id = category.id
                      left join supplier on item.supplier_id = supplier.id
                      left join
                      (
                        select item_id, item_number, item_price as last_price
                        from stock_log
                        left join
                        (
                          select max(stock_log.id) as max_id
                          from stock_log
                          where type = 'purchase'
                          and item_price != 0
                          and agent_id = '".$this->agent_id."'
                          group by stock_log.item_id
                        ) as table_log on table_log.max_id = stock_log.id
                        where table_log.max_id is not null
                      ) as log_table on log_table.item_id = item.id
                      where item.agent_id = '".$this->agent_id."'
                      and category.id like  '".$category."'
                      ".$where_type."
                      order by item.number , item.brand , item.name , item.spec
                      ";

        try {
            $result = DB::select($cmd);
        }catch (\Exception $e){
            Log::info($e);
        }

        return $result;
    }

    public function insertData($data){
        $data['agent_id'] = $this->agent_id;
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['live_times'] = $data['live_times'] * 365;
        Item::insert($data);

        try {
            $result = Item::insert($data);
        }catch (\Exception $e){
            Log::info($e);
        }

        return $result;
    }
}
