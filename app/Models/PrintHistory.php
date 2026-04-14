<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_name', 'recipient_phone', 'recipient_address', 'printed_by', 'printed_at',
        'sender_name', 'sender_phone', 'sender_address', 'shipping_unit', 'cod', 'product_name', 'payer', 'quantity',
    ];

    protected $dates = [
        'printed_at',
    ];
}
