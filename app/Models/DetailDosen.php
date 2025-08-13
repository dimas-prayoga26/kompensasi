<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailDosen extends Model
{
    protected $keyType = 'string';

    protected $guarded = [''];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jabatanFungsional()
    {
        return $this->belongsTo(JabatanFungsional::class, 'jabatan_fungsional_id');
    }

    // Relasi ke Bidang Keahlian
    public function bidangKeahlian()
    {
        return $this->belongsTo(BidangKeahlian::class, 'bidang_keahlian_id');
    }
}
