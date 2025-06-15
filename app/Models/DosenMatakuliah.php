<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DosenMatakuliah extends Model
{
    protected $keyType = 'string';

    protected $guarded = [''];

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function matakuliah()
    {
        return $this->belongsTo(Matakuliah::class, 'matakuliah_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function kompensasis()
    {
        return $this->hasMany(Kompensasi::class, 'dosen_matakuliah_id');
    }

    public function semesters()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

}
