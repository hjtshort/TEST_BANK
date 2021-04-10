<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TransactionFail extends Model
{

    protected $table = 'transaction_fails';
    protected $fillable = ['content', 'amount', 'type', 'date', 'file_import_id'];
    protected $guarded = ['id'];
    public $timestamps = false;
}
