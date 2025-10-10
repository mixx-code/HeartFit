<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'package_key',
        'package_label',
        'package_category',
        'package_batch',
        'package_price',
        'amount_total',
        'start_date',
        'end_date',
        'days',
        'service_dates',
        'unique_menus',
        'unique_menu_count',
        'payment_method',
        'status',
        'paid_at',
        'meta',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'paid_at'    => 'datetime',
        'service_dates'     => 'array',
        'unique_menus'      => 'array',
        'unique_menu_count' => 'integer',
        'package_price'     => 'integer',
        'amount_total'      => 'integer',
        'meta'              => 'array',
    ];

    // Default attribute (opsional)
    protected $attributes = [
        'status' => 'pending',
    ];

    // Normalisasi: jaga agar harga selalu integer (rupiah)
    protected function packagePrice(): Attribute
    {
        return Attribute::make(
            set: fn($value) => is_null($value) ? null : (int) $value
        );
    }

    protected function amountTotal(): Attribute
    {
        return Attribute::make(
            set: fn($value) => is_null($value) ? null : (int) $value
        );
    }

    // Convenience accessor: periode "YYYY-MM-DD s/d YYYY-MM-DD"
    protected function period(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->start_date && $this->end_date
                ? $this->start_date->format('Y-m-d') . ' s/d ' . $this->end_date->format('Y-m-d')
                : null
        );
    }

    // Relasi
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


// app/Models/Order.php
// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class Order extends Model
// {
//     protected $fillable = [
//         'order_number',
//         'user_id',
//         'package_key',
//         'package_label',
//         'package_category',
//         'package_price',
//         'amount_total',
//         'start_date',
//         'end_date',
//         'days',
//         'payment_method', // niat user
//         'status',
//         'paid_at',
//         'meta',
//     ];

//     protected $casts = [
//         'start_date' => 'date',
//         'end_date'   => 'date',
//         'paid_at'    => 'datetime',
//         'meta'       => 'array',
//     ];

//     public function paymentTransactions()
//     {
//         return $this->hasMany(PaymentTransaction::class);
//     }
// }
