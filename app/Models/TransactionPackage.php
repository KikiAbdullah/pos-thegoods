<?php

namespace App\Models;

use App\Models\Master\Package;
use App\Models\Traits\CreatedByTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionPackage extends Model
{
    use SoftDeletes;
    use CreatedByTrait;

    protected $table     = 'transaction_packages';
    protected $fillable = [
        'transaction_id',
        'package_id',
        'package_name',
        'harga',
        'url',
        'created_by',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }
}
