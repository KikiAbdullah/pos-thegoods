<?php

namespace App\Models;

use App\Models\Traits\CreatedByTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kasir extends Model
{
    use SoftDeletes;
    use CreatedByTrait;

    protected $table     = 'kasirs';
    protected $fillable = [
        'tanggal',
        'open',
        'close',
        'saldo_awal',
        'total_transaksi',
        'hasil_akhir',
        'status',
        'created_by'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'kasir_id');
    }


    public function setTanggalAttribute($val)
    {
        $val = str_replace('/', '-', $val);
        $val = date("Ymd", strtotime($val));
        $this->attributes['tanggal'] = $val;
    }


    public function setSaldoAwalAttribute($val)
    {
        $val = str_replace(',', '', $val);
        $this->attributes['saldo_awal'] = $val;
    }

    public function setTotalTransaksiAttribute($val)
    {
        $val = str_replace(',', '', $val);
        $this->attributes['total_transaksi'] = $val;
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
            case 'closed':
                $title      = 'Closed';
                $badge      = 'success';
                break;
        }

        return '<center><span class="badge bg-' . $badge . '">' . strtoupper($title) . '</span></center>';
    }


    public function getTanggalFormattedAttribute()
    {
        return formatDate('Y-m-d', 'd-m-Y', $this->tanggal);
    }
}
