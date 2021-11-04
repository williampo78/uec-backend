<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryHierarchy extends Model
{
    use HasFactory;
    protected $table = 'web_category_hierarchy';
    public $timestamps = true;
}
