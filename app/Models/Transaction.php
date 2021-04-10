<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasTimestamps;

    protected $table = 'transactions';
    protected $fillable = ['content', 'amount', 'type', 'date'];
    protected $guarded = ['id'];
}
