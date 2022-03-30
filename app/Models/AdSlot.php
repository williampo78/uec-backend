<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdSlot extends Model
{
    use HasFactory;

    protected $table = 'ad_slots';
    protected $guarded = [];

    public function adSlotContents()
    {
        return $this->hasMany(AdSlotContent::class, 'slot_id');
    }
}
