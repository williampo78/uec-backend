<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAuditLog extends Model
{
    use HasFactory;
    protected $table = 'product_audit_log';
    protected $guarded = []; 
}
