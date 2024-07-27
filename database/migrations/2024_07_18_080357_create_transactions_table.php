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
            $table->bigInteger('kasir_id')->nullable();
            $table->bigInteger('tipe_pembayaran_id')->nullable();
            $table->string('no', 20)->nullable();
            $table->date('tanggal')->nullable();
            $table->string('text')->nullable();
            $table->string('text_rejected')->nullable();
            $table->string('customer_whatsapp', 50)->nullable();
            $table->string('customer_name', 50)->nullable();
            $table->string('customer_email', 50)->nullable();

            $table->decimal('amount_paid', 30, 2)->nullable();
            
            $table->string('status', 20)->nullable();
            $table->tinyInteger('created_by')->nullable();

            $table->dateTime('rejected_at')->nullable();
            $table->tinyInteger('rejected_by')->nullable();

            $table->dateTime('ordered_at')->nullable();
            $table->tinyInteger('ordered_by')->nullable();

            $table->dateTime('payment_at')->nullable();
            $table->tinyInteger('payment_by')->nullable();

            $table->dateTime('verify_at')->nullable();
            $table->tinyInteger('verify_by')->nullable();

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
