<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * 可批量赋值的属性
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'age',
        'dob',
        'email',
        'creation_date',
    ];

    /**
     * 应该转换的属性
     */
    protected $casts = [
        'dob' => 'date',
        'creation_date' => 'datetime',
    ];
}
