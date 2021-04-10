<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileImport extends Model
{
    use HasTimestamps, HasFactory;

    protected $table = 'file_imports';
    protected $fillable = ['name', 'path', 'import_by', 'state'];
    protected $guarded = ['id'];

    public function transactionFailed(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransactionFail::class, 'file_import_id');
    }
}
