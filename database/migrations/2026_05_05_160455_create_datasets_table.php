<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->integer('id_dataset')->autoIncrement();
            $table->unsignedInteger('admin_id');
            $table->json('features');
            $table->string('label', 50);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('admin_id')->references('id_admin')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datasets');
    }
};
