<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationDetails extends Model
{
    use HasFactory;
    protected $table = 'quotation_details';
    public $timestamps = true;
}
