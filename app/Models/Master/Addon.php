<?php

namespace App\Models\Master;

use App\Models\Traits\CreatedByTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Addon extends Model
{
    use SoftDeletes;
    use CreatedByTrait;

    protected $table     = 'addons';
    protected $fillable = [
        'name',
        'description',
        'harga',
        'status', //1=active 0=nonactive
        'created_by',
    ];
}
