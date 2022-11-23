<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';
    protected $guarded = [];

    /**
     * 狀態名稱
     *
     * @return string|null
     */
    public function getActiveNameAttribute(): ?string
    {
        return config('uec.options.actives.type1')[$this->active] ?? null;
    }

    /**
     * 與使用者的關聯
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'supplier_id');
    }

    /**
     * 與供應商類別的關聯
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplierType(): BelongsTo
    {
        return $this->belongsTo(SupplierType::class, 'supplier_type_id');
    }

    /**
     * 與付款方式設定檔的關聯
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(LookupValuesV::class, 'payment_term', 'code')->where('type_code', 'PAYMENT_TERMS');
    }

    /**
     * 與聯絡人的關聯
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'table_id')->where('table_name', 'Supplier');
    }

    /**
     * 與供應商合約的關聯
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function supplierContract(): HasOne
    {
        return $this->hasOne(SupplierContract::class, 'supplier_id');
    }

    /**
     * 與商品的關聯
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }

    /**
     * @return HasMany
     * @DateTime: 2022/11/22 下午 05:28
     */
    public function supplierStockTypes(): HasMany
    {
        return $this->hasMany(SupplierStockType::class, 'supplier_id');
    }
}
