<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function transactionUsers()
    {
        return $this->belongsToMany(User::class, 'transaction_user')
            ->withPivot('amount_owned', 'status');
    }
}
