<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';
    protected $guarded = [];

    /**
     * 建立與角色的關聯
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')->withTimestamps();
    }

    /**
     * 建立與更新者的關聯
     */
    public function updatedByUser()
    {
        return $this->belongsTo(self::class, 'updated_by');
    }

    /**
     * 建立與供應商的關聯
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
