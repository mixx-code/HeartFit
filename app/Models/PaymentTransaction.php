<?php

// app/Models/PaymentTransaction.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'midtrans_order_id',
        'transaction_id',
        'attempt',
        'payment_type',
        'transaction_status',
        'fraud_status',
        'gross_amount',
        'extra',
        'signature_key',
        'raw_notification',
        'expired_at',
        'settled_at',
        'failed_reason',
    ];

    protected $casts = [
        'extra' => 'array',
        'raw_notification' => 'array',
        'expired_at' => 'datetime',
        'settled_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
