<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matakuliah extends Model
{
    protected $keyType = 'string';

    protected $guarded = [''];

     public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function matakuliahSemesters()
    {
        return $this->hasOne(MatakuliahSemester::class);
    }

}
