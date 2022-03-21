<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'supplier_id');
    }
}
