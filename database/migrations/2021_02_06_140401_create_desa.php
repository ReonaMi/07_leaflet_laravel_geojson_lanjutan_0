<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('desa', function (Blueprint $table) {
            $table->bigInteger('id_desa')->autoIncrement();
            $table->string('nama_desa');
            $table->bigInteger('id_kecamatan_ref');
            $table->longText('area');
            $table->timestamps();

            $table->foreign('id_kecamatan_ref')
                ->references('id_kecamatan')
                ->on('kecamatan')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('desa');
    }
}
