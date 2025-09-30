<?php

declare(strict_types=1);

namespace App\Platform\Domains\DealMessage;

use App\Platform\Domains\DealRoom\DealRoomId;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\User\UserId;

readonly class DealMessage
{
    public function __construct(
        public DealMessageId $id,
        public UserId $userId,
        public DealRoomId $dealRoomId,
        public Text $message,
    ) {
    }

    public static function create(
        UserId $userId,
        DealRoomId $dealRoomId,
        Text $message,
    ): self {
        return new self(
            new DealMessageId(),
            $userId,
            $dealRoomId,
            $message,
        );
    }
}