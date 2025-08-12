<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileBuktiKompensasi extends Model
{
     protected $keyType = 'string';

    protected $guarded = [''];

    protected $table = 'file_bukti_penawaran_kompensasis';

    public function tugasKompensasi()
    {
        return $this->belongsTo(TugasKompensasi::class, 'penawaran_kompensasi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
