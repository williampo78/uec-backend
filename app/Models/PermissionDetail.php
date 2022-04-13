<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionDetail extends Model
{
    use HasFactory;

    protected $table = 'permission_detail';

    /**
     * 建立與權限的關聯
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    /**
     * 建立與角色的關聯
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission_details', 'permission_detail_id', 'role_id')->withTimestamps()->withPivot('auth_query', 'auth_create', 'auth_update', 'auth_delete', 'auth_void', 'auth_export');
    }
}
