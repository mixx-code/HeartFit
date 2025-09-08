<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'package_key',
        'package_label',
        'package_category',
        'package_price',
        'start_date',
        'end_date',
        'days',
        'payment_method',
        'meta',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'meta'       => 'array', // simpan data tambahan jika perlu
    ];
}
