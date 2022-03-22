<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberNote extends Model
{
    use HasFactory;

    protected $table = 'member_notes';
    protected $guarded = [];
}
