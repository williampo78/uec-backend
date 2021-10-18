<?php

namespace App\Services;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HierarchyService
{
    public function __construct()
    {
    }

    public function getHierarchyCode($hierarchy_code)
    {
        $user_id = Auth::user()->id;
        $agent_id = Auth::user()->agent_id;
        //目前只有這筆範例 id 先用4
        $user_id = 4;

        $result = DB::select("with recursive rloop as
                (
                    select id, parent_id, user_id,
                           cast(user_id as char) as full_user_id,
                           parent_id as prev_id,
                           1 as dp
                     from approval_hierarchy
                          where hierarchy_code = '".$hierarchy_code."'
                    and agent_id = ".$agent_id."
                              and is_first_level = 1
                              and user_id = ".$user_id."
                          union all
                          select r.id, r.parent_id, r.user_id,
                           concat(cast(b.user_id as char), '>', cast(r.full_user_id as char)) as full_user_id,
                           b.parent_id as prev_id,
                           r.dp + 1 as dp
                    from rloop r
                    inner join approval_hierarchy b on b.id=r.prev_id and b.hierarchy_code = 'QUOTATION'
                    and b.agent_id = ".$agent_id."

                ), rResult as
                (
                    select id, parent_id, user_id, full_user_id
                    from
                    (
                        select *, row_number() over(partition by id order by dp desc) as g
                          from rloop
                    ) as a
                    where g = 1
                )
                select user_id, full_user_id
                  from rResult;
                ");

        $hierarchy = [];
        if ($result) {
            $hierarchy = explode('>', $result[0]->full_user_id);
            array_pop($hierarchy);
            $hierarchy = array_reverse($hierarchy);
        }

        return $hierarchy;
    }

    public function getNextApproval($hierarchy_code){
        $reviewer = Auth::user()->id;
//        $reviewer = 1;
        $hierarchy = $this->getHierarchyCode($hierarchy_code);

        $next_approver = false;
        //帶出下一個簽核者
        foreach ($hierarchy as $k => $v){
            if ($v==$reviewer && isset($hierarchy[$k+1])){
                $next_approver= $hierarchy[$k+1];
                break;
            }
        }

        return $next_approver;
    }
}
