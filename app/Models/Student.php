<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nim',
        'full_name',
        'points_balance',
    ];

    protected $casts = [
        'points_balance' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function shuPointTransactions()
    {
        return $this->hasMany(ShuPointTransaction::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
