<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proyeks', function (Blueprint $table) {
            $table->id();
            $table->string('no_otomatis')->unique(); // Kode auto increment (jika perlu auto, logic-nya bisa di controller)
            $table->string('kode_bom');
            $table->string('nama_bom');
            $table->text('keterangan')->nullable();
            $table->enum('status_bom', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proyeks');
    }
};
