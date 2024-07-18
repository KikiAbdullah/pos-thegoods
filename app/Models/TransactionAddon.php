<?php

namespace App\Models;

use App\Models\Master\Addon;
use App\Models\Traits\CreatedByTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionAddon extends Model
{
    use SoftDeletes;
    use CreatedByTrait;

    protected $table     = 'transaction_addons';
    protected $fillable = [
        'transaction_id',
        'addon_id',
        'addon_name',
        'qty',
        'harga',
        'created_by',
    ];

    protected $append = [
        'total',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function addon()
    {
        return $this->belongsTo(Addon::class, 'addon_id', 'id');
    }

    public function getTotalAttribute()
    {
        return $this->harga *  $this->qty;
    }
}
