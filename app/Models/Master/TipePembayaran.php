<?php

namespace App\Models\Master;

use App\Models\Traits\CreatedByTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipePembayaran extends Model
{
    use SoftDeletes;
    use CreatedByTrait;

    protected $table     = 'tipe_pembayarans';
    protected $fillable = [
        'name',
        'status', //1=active 0=nonactive
        'created_by',
    ];
}
