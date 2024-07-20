<?php

namespace App\Models;

use App\Models\Traits\CreatedByTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoriTrans extends Model
{
    use SoftDeletes;
    use CreatedByTrait;

    protected $table     = 'inventori_trans';
    protected $fillable = [
        'tanggal',
        'tipe',
        'transaction_id',
        'trans_no',
        'customer_name',
        'total',
        'created_by',
    ];

    // relation
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
