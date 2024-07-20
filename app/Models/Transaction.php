<?php

namespace App\Models;

use App\Models\Traits\CreatedByTrait;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    use CreatedByTrait;

    protected $table     = 'transactions';

    protected $fillable = [
        'no',
        'tanggal',
        'text',
        'customer_whatsapp',
        'customer_name',
        'status',
        'created_by',
    ];

    public function packages()
    {
        return $this->hasMany(TransactionPackage::class, 'transaction_id');
    }

    public function addons()
    {
        return $this->hasMany(TransactionAddon::class, 'transaction_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function setTanggalAttribute($val)
    {
        $val = str_replace('/', '-', $val);
        $val = date("Ymd", strtotime($val));
        $this->attributes['tanggal'] = $val;
    }

    public function getStatusFormattedAttribute()
    {
        $title      = '';
        $badge      = '';

        switch ($this->status) {
            case 'open':
                $title      = 'Open';
                $badge      = 'info';
                break;
            case 'ordered':
                $title      = 'Ordered';
                $badge      = 'primary';
                break;
            case 'payment':
                $title      = 'Payment';
                $badge      = 'warning';
                break;
            case 'verify':
                $title      = 'Verified';
                $badge      = 'success';
                break;
        }

        return '<center><span class="badge bg-' . $badge . '">' . $title . '</span></center>';
    }

    public function getTanggalFormattedAttribute()
    {
        return formatDate('Y-m-d', 'd-m-Y', $this->tanggal);
    }
}
