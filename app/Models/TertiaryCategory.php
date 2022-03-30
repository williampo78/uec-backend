<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TertiaryCategory extends Model
{
    use HasFactory;

    protected $table = 'tertiary_categories';
    protected $guarded = [];
}
