<?php

declare(strict_types=1);

namespace App\Platform\Domains\DealMessage;

use App\Platform\Domains\DealRoom\DealRoomId;
use App\Platform\Domains\Shared\Text\Text;

readonly class DealMessage
{
    public function __construct(
        public DealMessageId $id,
        public Sender $sender,
        public DealRoomId $dealRoomId,
        public Text $message,
    ) {
    }

    public static function create(
        Sender $sender,
        DealRoomId $dealRoomId,
        Text $message,
    ): self {
        return new self(
            new DealMessageId(),
            $sender,
            $dealRoomId,
            $message,
        );
    }
}