<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'deal_messages';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->string('id', 36)->primary()->comment('取引メッセージID');
            $table->string('user_id', 36)->comment('ユーザーID');
            $table->string('deal_room_id', 36)->comment('取引ルームID');
            $table->text('message')->comment('メッセージ内容');
            $table->timestamps();

            // 外部キー制約
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('deal_room_id')
                ->references('id')
                ->on('deal_rooms');

            // インデックス
            $table->index('deal_room_id', 'idx_deal_messages_room_id');
            $table->index('user_id', 'idx_deal_messages_user_id');
            $table->index(['deal_room_id', 'created_at'], 'idx_deal_messages_room_time');
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
