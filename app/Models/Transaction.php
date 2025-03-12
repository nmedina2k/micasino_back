<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = ['pay_type', 'date', 'amount', 'currency', 'status', 'transaction_id'];
    protected $table = 'transactions';
}
