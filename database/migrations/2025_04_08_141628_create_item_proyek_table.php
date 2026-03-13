<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_proyeks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyek_id'); // foreign key >> proyek
            $table->timestamps();

            $table->foreign('proyek_id')->references('id')->on('proyeks')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_proyeks');
    }
};