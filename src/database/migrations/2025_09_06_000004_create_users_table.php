<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'users';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->string('id', 36)->primary()->comment('ユーザーID');
            $table->string('name', 255)->comment('ユーザー名');
            $table->string('password', 255)->comment('パスワード');
            $table->string('mail_address', 255)->unique()->comment('メールアドレス');
            $table->string('post_code', 255)->comment('郵便番号');
            $table->string('address', 255)->comment('住所');
            $table->string('image_id', 36)->nullable()->comment('画像ID');
            $table->string('university_id', 36)->comment('大学ID');
            $table->string('faculty_id', 36)->comment('学部ID');


            $table->timestamps();
            $table->softDeletes();

            $table->foreign('university_id')
                ->references('id')
                ->on('universities');

            $table->foreign('faculty_id')
                ->references('id')
                ->on('faculties');
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
