<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MealPackages extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'meal_packages';

    protected $fillable = [
        'batch',
        'nama_meal_package',
        'jenis_paket',
        'porsi_paket',
        'total_hari',
        'detail_paket',
        'price',
        'package_type_id',
    ];

    public function packageType()
    {
        return $this->belongsTo(PackageType::class, 'package_type_id');
    }
}
