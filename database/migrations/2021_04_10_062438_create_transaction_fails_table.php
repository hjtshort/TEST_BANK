<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionFailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_fails', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->string('content')->nullable();
            $table->string('amount')->nullable();
            $table->string('type')->nullable();
            $table->foreignId('file_import_id')
                ->constrained('file_imports')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_fails');
    }
}
