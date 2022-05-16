<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $guarded = [];

    /**
     * 建立與使用者的關聯
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id')->withTimestamps();
    }

    /**
     * 建立與子權限的關聯
     */
    public function permissionDetails()
    {
        return $this->belongsToMany(PermissionDetail::class, 'role_permission_details', 'role_id', 'permission_detail_id')->withTimestamps()->withPivot('auth_query', 'auth_create', 'auth_update', 'auth_delete', 'auth_void', 'auth_export');
    }
}
