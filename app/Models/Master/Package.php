<?php

namespace App\Models\Master;

use App\Models\Traits\CreatedByTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;
    use CreatedByTrait;

    protected $table     = 'packages';
    protected $fillable = [
        'name',
        'description',
        'photo_session',
        'jumlah_orang',
        'harga',
        'status', //1=active 0=nonactive
        'created_by',
    ];
}
