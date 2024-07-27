<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKasirsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kasirs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->nullable();
            $table->dateTime('open')->nullable();
            $table->dateTime('close')->nullable();
            $table->decimal('saldo_awal', 10, 2);
            $table->decimal('total_transaksi', 10, 2)->nullable();
            $table->decimal('hasil_akhir', 10, 2)->nullable();
            $table->tinyInteger('created_by')->nullable();
            $table->string('status')->nullable()->comment('open/closed');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kasirs');
    }
}
