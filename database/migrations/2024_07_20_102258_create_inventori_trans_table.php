<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventori_trans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('tipe');
            $table->bigInteger('transaction_id')->unsigned();
            $table->string('trans_no')->nullable();
            $table->string('customer_name')->nullable();
            $table->decimal('total')->nullable();
            $table->text('keterangan')->nullable();
            $table->tinyInteger('created_by')->nullable();
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
        Schema::dropIfExists('inventori_trans');
    }
}
