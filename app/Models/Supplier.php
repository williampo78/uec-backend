<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';
    protected $guarded = [];

    /**
     * 建立與使用者的關聯
     */
    public function users()
    {
        return $this->hasMany(User::class, 'supplier_id');
    }

    /**
     * 建立與商品的關聯
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }
}
