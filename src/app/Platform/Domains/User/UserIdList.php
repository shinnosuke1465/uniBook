<?php

declare(strict_types=1);

namespace App\Platform\Domains\User;

use App\Exceptions\DomainException;
use Illuminate\Support\Collection;

readonly class UserIdList
{
    /**
     * @var Collection<int, UserId>
     */
    private Collection $collection;

    /**
     * @param UserId[] $userIds
     * @throws DomainException
     */
    public function __construct(array $userIds)
    {
        $this->collection = collect($userIds)->mapWithKeys(
            fn (UserId $userId) => [$userId->value => $userId]
        )->sortKeys();
        if ($this->collection->count() !== count($userIds)) {
            throw new DomainException('userIdが同じオブジェクトが存在しています。');
        }
    }

    /**
     * @return string[]
     */
    public function toStringArray(): array
    {
        return $this->collection->map(
            fn (UserId $userId) => $userId->value
        )->values()->all();
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return $this->collection->values()->map(
            fn (UserId $userId) => clone $userId
        )->all();
    }

    /**
     * 指定されたUserIdがリストに含まれているかチェック
     *
     * @param UserId $userId
     * @return bool
     */
    public function contains(UserId $userId): bool
    {
        return $this->collection->has($userId->value);
    }
}
