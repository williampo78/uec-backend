<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RoleService
{
    public function __construct()
    {
    }

    public function putUserRolesSession()
    {
        $user_id = Auth::user()->id;

        $roles = User::select('users.user_name' , 'user_roles.role_id', 'roles.role_name', 'role_permission_details.permission_detail_id',
                                'role_permission_details.permission_detail_id' , 'permission_detail.code' , 'permission_detail.name',
                                'auth_query' , 'auth_create' , 'auth_update', 'auth_delete', 'auth_void', 'auth_export')
                    ->where('user.id' , $user_id)
                    ->leftJoin('users' , 'user.account' , '=' , 'users.user_account')
                    ->leftJoin('user_roles' , 'users.id' , '=' , 'user_roles.user_id')
                    ->leftJoin('roles' , 'roles.id' , '=' , 'user_roles.role_id')
                    ->leftJoin('role_permission_details' , 'role_permission_details.role_id' , '=' , 'user_roles.role_id')
                    ->leftJoin('permission_detail' , 'permission_detail.id' , '=' , 'role_permission_details.permission_detail_id')
                    ->get()->toArray();

        $role_data = [];
        foreach ($roles as $val){
            $role_data[$val['code']] = $val;
        }

        Session::put('roles' , $role_data);
    }

    public function getDisplayRoles(){

        $code = explode('.' , \Request::route()->getName())[0];
        $route = \Route::current();
        $roles = Session::get('roles');

        $auth = 0;
        //laravel route回傳格式不同 先判別回傳類型
        if (isset($route->paramters[$code])){
            if (isset($roles[$code])){
                switch($route->paramters[$code]){
                    case 'edit':
                    case 'update':
                        $auth = $roles[$code]['auth_update'];
                        break;
                    case 'destroy':
                        $auth = $roles[$code]['auth_delete'];
                        break;
                    case 'store':
                        $auth = $roles[$code]['auth_create'];
                        break;
                }
            }
        }else{
            //index,create 回傳無 paramters , 用action判別
            $act = explode('@' , $route->action['controller']);

            if (isset($roles[$code]) && ($code != 'admin' && $code != '' && $code != 'signOut')) {
                switch ($act[1]) {
                    case 'index':
                        $auth = $roles[$code]['auth_query'];
                        break;
                    case 'create':
                        $auth = $roles[$code]['auth_create'];
                        break;
                }
            }elseif($code=='admin' || $code == '' || $code =='signOut'){
                $auth = 1;
            }
        }

        return $auth;
    }

    public function getOtherRoles(){
//        case 'void':
//            $auth = $roles[$code]['auth_void'];
//            break;
//        case 'export':
//            $auth = $roles[$code]['auth_export'];
//            break;
    }
}
