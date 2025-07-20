<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenawaranKompensasiUser extends Model
{
    protected $keyType = 'string';

    protected $guarded = [''];

    protected $table = 'penawaran_kompensasi_users';

    public function tugasKompensasi()
    {
        return $this->belongsTo(TugasKompensasi::class, 'penawaran_kompensasi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
