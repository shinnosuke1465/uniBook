<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const TABLE_NAME = 'likes';
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->string('id', 36)->comment('いいねID');
            $table->string('user_id', 36)->comment('ユーザーID');
            $table->string('textbook_id', 36)->comment('教科書ID');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('textbook_id')->references('id')->on('textbooks')->onDelete('cascade');

            // 同じユーザーが同じ教科書に複数回いいねできないようにユニーク制約を追加
            $table->unique(['user_id', 'textbook_id'], 'unique_user_textbook_like');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
