<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const TABLE_NAME = 'comments';
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->string('id', 36)->comment('コメントID');
            $table->text('text')->comment('コメント内容');
            $table->string('user_id', 36)->comment('ユーザーID');
            $table->string('textbook_id', 36)->comment('教科書ID');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('textbook_id')->references('id')->on('textbooks')->onDelete('cascade');

            // インデックス
            $table->index('textbook_id', 'idx_comments_textbook_id');
            $table->index('user_id', 'idx_comments_user_id');
            $table->index('created_at', 'idx_comments_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};

