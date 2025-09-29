<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\DealRoom;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Exceptions\RepositoryException;
use App\Models\DealRoom as DealRoomDB;
use App\Models\DealRoomUser;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\DealRoom\DealRoom;
use App\Platform\Domains\DealRoom\DealRoomId;
use App\Platform\Domains\DealRoom\DealRoomRepositoryInterface;
use App\Platform\Domains\User\UserId;

readonly class DealRoomRepository implements DealRoomRepositoryInterface
{
    /**
     * @throws DuplicateKeyException
     */
    public function insert(DealRoom $dealRoom): void
    {
        if ($this->hasDuplicate($dealRoom->id)) {
            throw new DuplicateKeyException('取引ルームが重複しています。');
        }

        // DealRoomテーブルにレコード作成
        DealRoomDB::create([
            'id' => $dealRoom->id->value,
            'deal_id' => $dealRoom->dealId->value,
        ]);

        // 中間テーブル（deal_room_users）にユーザー関連付けを作成
        $dealRoomUserData = [];
        foreach ($dealRoom->userIds->toArray() as $userId) {
            $dealRoomUserData[] = [
                'deal_room_id' => $dealRoom->id->value,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DealRoomUser::insert($dealRoomUserData);
    }

    /**
     * @throws DomainException
     */
    public function findById(DealRoomId $dealRoomId): ?DealRoom
    {
        $dealRoomDB = DealRoomDB::query()
            ->with('users')
            ->where('id', $dealRoomId->value)
            ->first();

        if (!$dealRoomDB) {
            return null;
        }

        return DealRoomFactory::create($dealRoomDB);
    }

    /**
     * @throws DomainException
     * @return DealRoom[]
     */
    public function findByUserId(UserId $userId): array
    {
        $dealRoomDBs = DealRoomDB::query()
            ->with('users')
            ->whereHas('users', function ($query) use ($userId) {
                $query->where('users.id', $userId->value);
            })
            ->get();

        return $dealRoomDBs->map(
            fn ($dealRoomDB) => DealRoomFactory::create($dealRoomDB)
        )->all();
    }

    private function hasDuplicate(DealRoomId $dealRoomId): bool
    {
        return DealRoomDB::find($dealRoomId->value) !== null;
    }
}
