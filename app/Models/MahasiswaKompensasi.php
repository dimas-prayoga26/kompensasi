<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MahasiswaKompensasi extends Model
{
    protected $keyType = 'string';

    protected $guarded = [''];

    protected $table = 'penawaran_kompensasi_users';

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /**
     * Relasi ke penawaran kompensasi
     */
    public function kompensasi()
    {
        return $this->belongsTo(TugasKompensasi::class, 'penawaran_kompensasi_id');
    }
    
}
