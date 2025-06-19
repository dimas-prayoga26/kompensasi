<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kompensasi extends Model
{
    protected $keyType = 'string';

    protected $guarded = [];

    public function dosenMatakuliah()
    {
        return $this->belongsTo(DosenMatakuliah::class, 'dosen_matakuliah_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
