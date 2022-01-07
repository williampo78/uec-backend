<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TmpTapPay extends Model
{
    use HasFactory;
    protected $table = 'tmp_tappay';
    protected $guarded = [];
}
