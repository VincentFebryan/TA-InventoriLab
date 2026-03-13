<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bill_of_materials', function (Blueprint $table) {
            // Mengubah nama kolom 'nama_material' menjadi 'nama_bom'
            $table->renameColumn('nama_material', 'nama_bom');

            // Menambahkan kolom baru 'keterangan' setelah kolom 'nama_bom'
            $table->text('keterangan')->nullable()->after('nama_bom');
        });
    }

    public function down(): void
    {
        Schema::table('bill_of_materials', function (Blueprint $table) {
            // Mengubah kembali kolom 'nama_bom' menjadi 'nama_material'
            $table->renameColumn('nama_bom', 'nama_material');

            // Menghapus kolom 'keterangan'
            $table->dropColumn('keterangan');
        });
    }
};


