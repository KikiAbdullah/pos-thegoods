<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('no', 20)->nullable();
            $table->date('tanggal')->nullable();
            $table->string('text')->nullable();
            $table->string('customer_whatsapp', 50)->nullable();
            $table->string('customer_name', 50)->nullable();
            $table->string('status', 20)->nullable();
            $table->string('created_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
