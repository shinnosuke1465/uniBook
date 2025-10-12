<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'textbooks';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
                $table->string('id', 36)->primary()->comment('教科書ID');
                $table->string('name', 255)->comment('教科書名');
                $table->text('description')->nullable()->comment('説明');
                $table->unsignedInteger('price')->comment('価格');
                $table->enum('condition_type', [
                    'new',           // 新品、未使用
                    'near_new',      // 未使用に近い
                    'no_damage',     // 目立った傷や汚れなし
                    'slight_damage', // やや傷や汚れあり
                    'damage',        // 傷や汚れあり
                    'poor_condition' // 全体的に状態が悪い
                ])->comment('状態');
                $table->string('university_id')->comment('大学ID');
                $table->string('faculty_id', 36)->comment('学部ID');

                $table->timestamps();
                $table->softDeletes();

                // 外部キー制約
                $table->foreign('university_id')
                    ->references('id')
                    ->on('universities');
                $table->foreign('faculty_id')
                    ->references('id')
                    ->on('faculties');

                // インデックス
                $table->index('university_id', 'idx_textbooks_university_id');
                $table->index('faculty_id', 'idx_textbooks_faculty_id');
                $table->index(['university_id', 'faculty_id'], 'idx_textbooks_university_faculty');
                $table->index('price', 'idx_textbooks_price');
                $table->index('created_at', 'idx_textbooks_created_at');
                $table->index('condition_type', 'idx_textbooks_condition_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
