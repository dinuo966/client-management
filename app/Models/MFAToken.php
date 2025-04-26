<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MFAToken extends Model
{
    use HasFactory;

    /**
     * 可批量赋值的属性
     */
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
    ];

    /**
     * 应该转换的属性
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * 令牌所属的用户
     */
    public function user()
    {
        return $this -> belongsTo(User::class);
    }

    /**
     * 检查令牌是否已过期
     */
    public function isExpired()
    {
        return $this -> expires_at -> isPast();
    }
}
