<?php

namespace App\Services;

use App\Models\User;
use App\Models\User_permission;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;

class PermissionService
{
    public function __construct()
    {
    }

    //取得使用者權限
    public function GetUserPermission($user_id = 1)
    {
        $permission = User_permission::where('user_id', $user_id)->get()->toArray();
        return $permission;
    }

    public static function GetUserMenu()
    {
        $user_id = Auth::user()->id;
        $menus = Users::select("permission.id as mainID", "permission.name as mainMenu", "permission.icon as mainIcon"
            , "permission_detail.id as subID", "permission_detail.name as subMenu", "permission_detail.icon", "permission_detail.code")
            ->where('users.id', 1)//$user_id
            ->where('users.agent_id', 1)
            ->join('user_roles' , 'user_roles.user_id' , '=' , 'users.id')
            ->join('roles' , 'roles.id' , '=' , 'user_roles.role_id')
            ->join('role_permission_details' , 'role_permission_details.role_id' , '=' , 'roles.id')
            ->join('permission_detail' , 'permission_detail.id' , '=' , 'role_permission_details.permission_detail_id')
            ->join('permission' , 'permission.id' , '=' , 'permission_detail.permission_id')
            ->orderBy('permission.id', 'asc')
            ->orderBy('permission_detail.sort','asc')
            ->get()->toArray();
        foreach ($menus as $menu) {
            $data['mainMenu'][$menu['mainID']] = $menu;
            $data['subMenu'][$menu['mainID']][$menu['subID']] = $menu;
        }
        return $data;
    }
}
