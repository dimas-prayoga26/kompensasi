<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailMahasiswa extends Model
{
    protected $keyType = 'string';

    protected $guarded = [''];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

}
