<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('barangs', function (Blueprint $table) {
            $table->unsignedBigInteger('gudang_id')->nullable()->after('jenis_barang_id');
            $table->foreign('gudang_id')->references('id')->on('gudangs')->onDelete('cascade');
        });
    }

    public function down() {
        
    }
};
