<?php

// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'package_key',
        'package_label',
        'package_category',
        'package_price',
        'amount_total',
        'start_date',
        'end_date',
        'days',
        'payment_method', // niat user
        'status',
        'paid_at',
        'meta',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'paid_at'    => 'datetime',
        'meta'       => 'array',
    ];

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}
