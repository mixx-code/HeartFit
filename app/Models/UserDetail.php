<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    /** @use HasFactory<\Database\Factories\UserDetailFactory> */
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'mr',
        'nik',
        'alamat',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'bb_tb',
        'foto_ktp_base64',
        'hp',
        'usia',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'usia' => 'integer',
        'foto_ktp_base64' => 'encrypted'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
