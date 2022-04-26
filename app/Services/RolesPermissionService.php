<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\PermissionDetail;
use App\Models\Role;
use App\Models\RolePermissionDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolesPermissionService
{
    public function getRoles($data = [])
    {
        $agent_id = Auth::user()->agent_id;
        $roles = Role::where('agent_id', $agent_id);

        if (isset($data['active'])) {
            $roles->where('active', $data['active']);
        }

        if (isset($data['role_name'])) {
            $roles->where('role_name', 'like', '%' . $data['role_name'] . '%');
        }

        $roles = $roles->orderBy('role_name', 'asc')->get();

        return $roles;
    }

    public function getPermission()
    {
        $permission_array = [];
        $permission = Permission::where('type', '=', 'menu')->orderBy('sort', 'asc')->get()->toArray();

        foreach ($permission as $data) {
            $permission_array[$data['id']]['id'] = $data['id'];
            $permission_array[$data['id']]['icon'] = $data['icon'];
            $permission_array[$data['id']]['name'] = $data['name'];
        }

        return $permission_array;
    }

    public function getPermissionDetail()
    {
        $permission_detail_array = [];
        $permission = PermissionDetail::orderBy('sort', 'asc')->orderBy('id', 'asc')->get()->toArray();

        foreach ($permission as $data) {
            $permission_detail_array[$data['permission_id']]['id'][] = $data['id'];
            $permission_detail_array[$data['permission_id']]['icon'][] = $data['icon'];
            $permission_detail_array[$data['permission_id']]['code'][] = $data['code'];
            $permission_detail_array[$data['permission_id']]['name'][] = $data['name'];
        }

        return $permission_detail_array;
    }

    public function addRole($inputdata, $act)
    {
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        $auth = ['query', 'create', 'update', 'delete', 'void', 'export'];

        DB::beginTransaction();
        try {
            $roleData = [];
            $roleData['agent_id'] = Auth::user()->agent_id;
            $roleData['role_name'] = $inputdata['role_name'];
            $roleData['active'] = $inputdata['active'];
            $roleData['is_for_supplier'] = $inputdata['is_for_supplier'];
            $roleData['created_by'] = $user_id;
            $roleData['created_at'] = $now;
            $roleData['updated_by'] = $user_id;
            $roleData['updated_at'] = $now;

            if ($act == 'add') {
                $role_id = Role::insertGetId($roleData);
            } else if ($act == 'upd') {
                Role::where('id', $inputdata['id'])->update($roleData);
                $role_id = $inputdata['id'];
            }

            $detailData = [];

            //不管新增或編輯先把原有的權限都刪除
            RolePermissionDetail::where('role_id', '=', $role_id)->delete();
            if (!empty($inputdata['auth_index'])) {
                foreach ($inputdata['auth_index'] as $k1 => $v1) { //有勾選才會寫入細項權限
                    $detailData['role_id'] = $role_id;
                    $detailData['permission_detail_id'] = $v1;

                    foreach ($auth as $k => $v) {
                        $detailData['auth_' . $v] = isset($inputdata['auth_' . $v . '_' . $v1]) ? $inputdata['auth_' . $v . '_' . $v1] : 0;
                    }
                    $detailData['created_by'] = $user_id;
                    $detailData['created_at'] = $now;
                    $detailData['updated_by'] = -1;
                    $detailData['updated_at'] = $now;

                    RolePermissionDetail::insert($detailData);
                }
            }

            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = false;
        }

        return $result;
    }

    public function getRolePermission($id)
    {
        $auth = ['auth_query', 'auth_create', 'auth_update', 'auth_delete', 'auth_void', 'auth_export'];
        $permission_detail_array = [];
        $permission = RolePermissionDetail::where('role_id', '=', $id)->get()->toArray();

        foreach ($permission as $data) {
            foreach ($auth as $k => $v) {
                $permission_detail_array[$data['permission_detail_id']][$v] = $data[$v];
            }
        }

        return $permission_detail_array;
    }

    public function showRole($id)
    {
        return Role::where('id', $id)->get()->first();
    }
}
