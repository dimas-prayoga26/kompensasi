<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TugasKompensasi extends Model
{
    protected $keyType = 'string';

    protected $guarded = [''];

    protected $table = 'penawaran_kompensasis';

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function penawaranUsers()
    {
        return $this->hasMany(PenawaranKompensasiUser::class, 'penawaran_kompensasi_id');
    }
}
