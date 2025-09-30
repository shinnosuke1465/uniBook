<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\DealMessage;

use App\Exceptions\DuplicateKeyException;
use App\Models\DealMessage as DealMessageDB;
use App\Platform\Domains\DealMessage\DealMessage;
use App\Platform\Domains\DealMessage\DealMessageId;
use App\Platform\Domains\DealMessage\DealMessageRepositoryInterface;

readonly class DealMessageRepository implements DealMessageRepositoryInterface
{
    /**
     * @throws DuplicateKeyException
     */
    public function insert(DealMessage $dealMessage): void
    {
        if ($this->hasDuplicate($dealMessage->id)) {
            throw new DuplicateKeyException('メッセージが重複しています。');
        }

        DealMessageDB::create([
            'id' => $dealMessage->id->value,
            'user_id' => $dealMessage->sender->userId->value,
            'deal_room_id' => $dealMessage->dealRoomId->value,
            'message' => $dealMessage->message->value,
        ]);
    }

    private function hasDuplicate(DealMessageId $dealMessageId): bool
    {
        return DealMessageDB::find($dealMessageId->value) !== null;
    }
}