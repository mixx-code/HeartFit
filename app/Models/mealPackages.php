<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealPackages extends Model
{
    use HasFactory;

    protected $table = 'meal_packages';

    protected $fillable = [
        'batch',
        'jenis_paket',
        'porsi_paket',
        'detail_paket',
        'package_type_id',
    ];

    public function packageType()
    {
        return $this->belongsTo(PackageType::class, 'package_type_id');
    }
}
