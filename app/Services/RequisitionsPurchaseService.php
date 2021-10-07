<?php

namespace App\Services;



use Illuminate\Support\Facades\Auth;

class RequisitionsPurchaseService
{
    public function __construct()
    {
    }

    public function getRequisitionsPurchase()
    {
//        $sql->query("select `requisitions_purchase`.* , user.name as user_name , supplier.name as supplier_name , department.name as department_name , warehouse.name as warehouse_name
//                                from `requisitions_purchase`
//                                left join user on `requisitions_purchase`.user_id = user.id
//                                left join supplier on `requisitions_purchase`.supplier_id = supplier.id
//                                left join department on `requisitions_purchase`.department_id = department.id
//                                left join warehouse on `requisitions_purchase`.warehouse_id = warehouse.id
//                                where `requisitions_purchase`.agent_id = '".$_SESSION['agent_id']."'
//                                and trade_date >= '".$select_start_date."'
//                                and trade_date <= '".$select_end_date."'
//                                and department_id like '".$department."'
//                                ".$where_type."
//                                order by `requisitions_purchase`.trade_date desc , `requisitions_purchase`.created_at desc
//                                ");
//        return Supplier::where('agent_id' , $agent_id)->where('active',1);
    }
}
