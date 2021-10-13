<?php

namespace App\Services;

use App\Models\Users;
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

        $roles = Users::select('users.user_name' , 'user_roles.role_id', 'roles.role_name', 'role_permission_details.permission_detail_id',
                                'role_permission_details.permission_detail_id' , 'permission_detail.code' , 'permission_detail.name',
                                'auth_query' , 'auth_create' , 'auth_update', 'auth_delete', 'auth_void', 'auth_export')
                    ->where('users.id' , $user_id)
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

    /**
     *  'update':操作者有「修改」權限，才開放﹝簽核﹞鈕供點擊 , 'void':作廢 , 'export':匯出檔案
     */
    public function getOtherRoles(){
        $code = explode('.' , \Request::route()->getName())[0];
        $roles = Session::get('roles');

        //預設0 , DB未建置資料皆判斷為無權限
        $data = [
            'auth_query' => 1,
            'auth_create' => 1,
            'auth_update' => 1,
            'auth_delete' => 1,
            'auth_void' => 1,
            'auth_export' => 1,
        ];

        if(isset($roles[$code])) {
            $data = [
                'auth_query' => $roles[$code]['auth_query'],
                'auth_create' => $roles[$code]['auth_create'],
                'auth_update' => $roles[$code]['auth_update'],
                'auth_delete' => $roles[$code]['auth_delete'],
                'auth_void' => $roles[$code]['auth_void'],
                'auth_export' => $roles[$code]['auth_export'],
            ];
        }

        return $data;
    }
}
