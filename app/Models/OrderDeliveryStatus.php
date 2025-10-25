<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDeliveryStatus extends Model
{
    protected $fillable = [
        'meal_package_id',
        'menu_makanan_id',
        'batch',
        'delivery_date',
        'status_siang',
        'status_malam',
        'confirmed_by',
        'confirmed_at',
        'note',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'confirmed_at'  => 'datetime',
        'spec_menu'     => 'array',
        'serve_days'    => 'array',
    ];

    public function mealPackage()
    {
        // penting: spesifikkan FK karena nama model "MealPackages" (plural)
        return $this->belongsTo(MealPackages::class, 'meal_package_id');
    }

    public function menuMakanan()
    {
        // default FK sudah cocok: menu_makanan_id
        return $this->belongsTo(MenuMakanan::class, 'menu_makanan_id');
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function getStatusSummaryAttribute()
    {
        return "Siang: {$this->status_siang}, Malam: {$this->status_malam}";
    }
}
