<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuMakanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'menu_makanans';

    protected $fillable = [
        'nama_menu',
        'batch',
        'serve_days',
        'spec_menu',
        'created_by',
        'updated_by',   // tambahkan ini
        'deleted_by',   // tambahkan ini juga
    ];

    protected $casts = [
        'serve_days' => 'array',
        'spec_menu'  => 'array',
    ];

    // Relasi user tracking
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
