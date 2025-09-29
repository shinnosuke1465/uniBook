<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'deal_room_users';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->string('user_id', 36)->comment('ユーザーID');
            $table->string('deal_room_id', 36)->comment('取引ルームID');
            $table->timestamps();

            // 外部キー制約
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('deal_room_id')
                ->references('id')
                ->on('deal_rooms');

            // ユニーク制約（同じユーザーが同じ取引ルームに複数回参加することを防ぐ）
            $table->unique(['user_id', 'deal_room_id']);
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