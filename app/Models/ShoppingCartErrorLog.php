<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCartErrorLog extends Model
{
    use HasFactory;

    protected $table = 'shopping_cart_error_log';
    protected $guarded = [];

    // member 關聯會員
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
