<?php

namespace App\Services;

use App\Models\Roles;
use App\Models\RolePermissionDetails;
use App\Models\PermissionDetail;
use App\Models\Permission;

use Illuminate\Support\Facades\Auth;

class RolesPermissionService
{

    public function getRoles($data)
    {
        $agent_id = Auth::user()->agent_id;
        $roles = Roles::where('agent_id', $agent_id);

        if (isset($data['active'])) {
            $roles->where('active', $data['active']);
        }

        if (isset($data['role_name'])) {
            $roles->where('role_name', 'like', '%' . $data['role_name'] . '%');
        }

        $roles = $roles->orderBy('role_name' , 'ASC')->get();

        return $roles;
    }

    public function getPermission()
    {
        $permission_array = [];
        $permission = Permission::where('type','=','menu')->orderBy('sort', 'asc')->get()->toArray();
        foreach ($permission as $data){
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
        foreach ($permission as $data){
            $permission_detail_array[$data['permission_id']]['id'][] = $data['id'];
            $permission_detail_array[$data['permission_id']]['icon'][] = $data['icon'];
            $permission_detail_array[$data['permission_id']]['code'][] = $data['code'];
            $permission_detail_array[$data['permission_id']]['name'][] = $data['name'];
        }
        return $permission_detail_array;
    }

}
