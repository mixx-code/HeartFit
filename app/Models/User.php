<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Kolom yang boleh diisi mass-assignment.
     */
    protected $fillable = [
        'name',
        'role',
        'email',
        'password',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting kolom otomatis.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'deleted_at'        => 'datetime',
        ];
    }
    public function detail()
    {
        return $this->hasOne(\App\Models\UserDetail::class);
    }
}
