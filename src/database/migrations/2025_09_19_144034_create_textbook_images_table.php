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
        Schema::create('textbook_images', function (Blueprint $table) {
            $table->bigIncrements('id', true)->comment('ID');
            $table->string('image_id', 36)->comment('画像ID');
            $table->string('textbook_id', 36)->comment('教科書ID');
            $table->timestamps();

            // 外部キー制約
            $table->foreign('image_id')
                ->references('id')
                ->on('images');
            $table->foreign('textbook_id')
                ->references('id')
                ->on('textbooks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('textbook_images');
    }
};
