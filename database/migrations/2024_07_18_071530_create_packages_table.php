<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('photo_session')->nullable()->comment('sesi foto');
            $table->integer('jumlah_orang')->nullable();
            $table->decimal('harga', 30, 2)->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=active,0=non');
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
        Schema::dropIfExists('packages');
    }
}
