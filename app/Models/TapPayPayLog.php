<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TapPayPayLog extends Model
{
    use HasFactory;
    protected $table = 'tappay_response_log';
    protected $guarded = [];
}
