<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PasswordResetToken extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'email';
    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';

    protected $fillable = [
        'email', 'token', 'created_at', 'expired_at', 'used_at', 'is_used'
    ];

    public $timestamps = false;

    protected $table = 'password_reset_tokens';

}
