<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatakuliahSemester extends Model
{
    protected $keyType = 'string';

    protected $guarded = [''];

    public function matakuliah()
    {
        return $this->belongsTo(Matakuliah::class);
    }
}
