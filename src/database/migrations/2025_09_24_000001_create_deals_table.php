<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'deals';
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->string('id', 36)->primary()->comment('取引ID');
            $table->string('seller_id', 36)->comment('出品者ID');
            $table->string('buyer_id', 36)->nullable()->comment('購入者ID');
            $table->string('textbook_id', 36)->comment('教科書ID');
            $table->enum('deal_status', [
                'Listing',    // 出品中
                'Purchased',  // 購入済み
                'Shipping',   // 発送中
                'Completed',  // 取引完了
                'Cancelled'   // 取引キャンセル
            ])->comment('取引ステータス');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('seller_id')->references('id')->on('users');
            $table->foreign('buyer_id')->references('id')->on('users');
            $table->foreign('textbook_id')->references('id')->on('textbooks');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};

