<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstallmentInterestRate extends Model
{
    protected $guarded = [];

    /**
     * @return BelongsTo
     * @Author: Eric
     * @DateTime: 2022/8/4 上午 11:37
     */
    public function bank():BelongsTo
    {
        return $this->belongsTo(Bank::class, 'issuing_bank_no', 'bank_no');
    }

    /**
     * @return BelongsTo
     * @Author: Eric
     * @DateTime: 2022/8/9 上午 11:54
     */
    public function updatedBy():BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
