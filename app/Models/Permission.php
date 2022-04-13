<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permission';

    /**
     * 建立與子權限的關聯
     */
    public function permissionDetails()
    {
        return $this->hasMany(PermissionDetail::class, 'permission_id');
    }
}
