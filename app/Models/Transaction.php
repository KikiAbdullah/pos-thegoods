<?php

namespace App\Models;

use App\Models\Master\TipePembayaran;
use App\Models\Traits\CreatedByTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    use CreatedByTrait;

    protected $table     = 'transactions';

    protected $fillable = [
        'kasir_id',
        'tipe_pembayaran_id',
        'no',
        'tanggal',
        'text',
        'customer_whatsapp',
        'customer_name',
        'customer_email',

        'amount_paid',

        'status',
        'text_rejected',
        'created_by',

        'rejected_at',
        'rejected_by',

        'ordered_at',
        'ordered_by',

        'payment_at',
        'payment_by',

        'verify_at',
        'verify_by',
    ];

    protected $append = [
        'total',
        'service_time'
    ];

    public function packages()
    {
        return $this->hasMany(TransactionPackage::class, 'transaction_id');
    }

    public function addons()
    {
        return $this->hasMany(TransactionAddon::class, 'transaction_id');
    }

    public function kasir()
    {
        return $this->belongsTo(Kasir::class, 'kasir_id', 'id');
    }

    public function tipePembayaran()
    {
        return $this->belongsTo(TipePembayaran::class, 'tipe_pembayaran_id', 'id');
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
            case 'rejected':
                $title      = 'Rejected';
                $badge      = 'danger';
                break;
        }

        return '<center><span class="badge bg-' . $badge . '">' . strtoupper($title) . '</span></center>';
    }

    public function getTotalAttribute()
    {
        $total = 0;
        if ($this->packages->isNotEmpty()) {
            $total +=  $this->packages->sum('harga');
        }

        if ($this->addons->isNotEmpty()) {
            $total +=  $this->addons->sum('total');
        }

        return $total;
    }

    public function getTanggalFormattedAttribute()
    {
        return formatDate('Y-m-d', 'd-m-Y', $this->tanggal);
    }

    public function setAmountPaidAttribute($val)
    {
        $val = str_replace(',', '', $val);
        $this->attributes['amount_paid'] = $val;
    }

    public function getServiceTimeAttribute()
    {
        if (!empty($this->ordered_at) && !empty($this->verify_at)) {
            // Buat objek Carbon dari waktu mulai dan waktu akhir
            $start = Carbon::createFromFormat('Y-m-d H:i:s', $this->ordered_at);
            $end = Carbon::createFromFormat('Y-m-d H:i:s', $this->verify_at);

            // Hitung selisih menit
            // Hitung selisih detik
            $differenceInSeconds = $end->diffInSeconds($start);

            if ($differenceInSeconds < 60) {
                return $differenceInSeconds . ' Detik';
            } elseif ($differenceInSeconds < 3600) {
                return $end->diffInMinutes($start) . ' Menit';
            } else {
                return $end->diffInHours($start) . ' Jam';
            }
        } else {
            return '-';
        }
    }
}
