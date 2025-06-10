<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelasSemesterMahasiswa extends Model
{
    protected $keyType = 'string';

    protected $table = 'kelas_semester_mahasiswa';
    
    protected $guarded = [''];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // app/Models/KelasSemesterMahasiswa.php

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }


}
