<?php

declare(strict_types=1);

namespace App\Platform\Domains\DealMessage;

use App\Exceptions\DomainException;
use App\Platform\Domains\DealRoom\DealRoom;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\User\UserId;

/**
 * DealMessageのドメインサービス
 *
 * 責務:
 * - DealMessageの生成時にビジネスルールを適用
 * - DealRoomへの参加チェック
 */
readonly class DealMessageDomainService
{
    /**
     * メッセージを作成（DealRoomへの参加チェック付き）
     *
     * @param DealRoom $dealRoom
     * @param UserId $senderId
     * @param Text $message
     * @return DealMessage
     * @throws DomainException ユーザーがDealRoomに参加していない場合
     */
    public function createMessage(
        DealRoom $dealRoom,
        UserId $senderId,
        Text $message
    ): DealMessage {
        // DealRoomに参加していないとメッセージ送信不可
        if (!$dealRoom->hasUser($senderId)) {
            throw new DomainException(
                'この取引ルームにメッセージを送信する権限がありません。'
            );
        }

        // メッセージ作成
        return DealMessage::create(
            sender: new Sender($senderId),
            dealRoomId: $dealRoom->id,
            message: $message
        );
    }
}
