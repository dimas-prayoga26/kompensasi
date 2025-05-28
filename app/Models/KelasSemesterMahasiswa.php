<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelasSemesterMahasiswa extends Model
{
    protected $keyType = 'string';

    protected $table = 'kelas_semester_mahasiswa';
    
    protected $guarded = [''];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
