<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdSlots extends Model
{
    use HasFactory;
    
    protected $table = 'ad_slots';
    protected $guarded = [];
}
