<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\DealMessage;

use App\Platform\Domains\DealMessage\DealMessage;
use App\Platform\Domains\DealMessage\DealMessageId;
use App\Platform\Domains\DealMessage\Sender;
use App\Platform\Domains\DealRoom\DealRoomId;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\User\UserId;

class TestDealMessageFactory
{
    public static function create(
        ?DealMessageId $id = null,
        ?Sender $sender = null,
        ?DealRoomId $dealRoomId = null,
        ?Text $message = null,
    ): DealMessage {
        return new DealMessage(
            id: $id ?? new DealMessageId(),
            sender: $sender ?? new Sender(new UserId()),
            dealRoomId: $dealRoomId ?? new DealRoomId(),
            message: $message ?? new Text('テストメッセージ'),
        );
    }
}