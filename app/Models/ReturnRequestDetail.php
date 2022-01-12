<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequestDetail extends Model
{
    use HasFactory;

    protected $table = 'return_request_details';
    protected $guarded = [];
}
