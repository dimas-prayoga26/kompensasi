<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $keyType = 'string';

    protected $guarded = [''];

    public function kelasSemesterMahasiswas()
    {
        return $this->hasMany(KelasSemesterMahasiswa::class, 'kelas_id');
    }
}
