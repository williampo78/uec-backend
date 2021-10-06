<?php

namespace App\Services;

use App\Models\User;
use App\Models\User_permission;

class PermissionService
{
    public function __construct()
    {
    }
    //取得使用者權限
    public function GetUserPermission($user_id = 1)
    {
        $permission = User_permission::where('user_id', $user_id)->get()->toArray();
        return $permission ;
    }
}
