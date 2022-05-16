<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contact';
    protected $guarded = [];

    /**
     * 建立與供應商的關聯
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'table_id');
    }
}
