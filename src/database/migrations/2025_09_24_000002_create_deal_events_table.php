<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'deal_events';
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->string('id', 36)->primary()->comment('取引イベントID');
            $table->string('user_id', 36)->comment('ユーザーID');
            $table->string('deal_id', 36)->comment('取引ID');
            $table->enum('actor_type', [
                'seller',   // 出品者
                'buyer'     // 購入者
            ])->comment('アクター種別');
            $table->enum('event_type', [
                'Listing',    // 出品
                'Purchase',  // 購入
                'ReportDelivery',   // 発送報告
                'ReportReceipt',  // 受取報告
                'Cancel'   // 取引キャンセル
            ])->comment('イベント種別');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('deal_id')->references('id')->on('deals');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};

