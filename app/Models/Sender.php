<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sender extends Model
{
    use HasFactory;

    // Các trường mà bạn muốn cho phép gán hàng loạt
    protected $fillable = [
        'name',
        'contact',
        'address',
    ];

}