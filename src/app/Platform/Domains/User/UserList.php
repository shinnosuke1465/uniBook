<?php

declare(strict_types=1);

namespace App\Platform\Domains\User;

use App\Exceptions\DomainException;
use Illuminate\Support\Collection;

readonly class UserList
{
    /**
     * @var Collection <int, User>
     */
    private Collection $collection;

    /**
     * @param User[] $users
     * @throws DomainException
     */
    public function __construct(array $users)
    {
        $this->collection = collect($users)->mapWithKeys(
            fn(User $user) => [$user->id->value => $user]
        );
        if ($this->collection->count() !== count($users)) {
            throw new DomainException('userIdが同じオブジェクトが存在しています。');
        }
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    public function toArray(): array
    {
        return $this->collection->values()->map(
            fn(User $user) => clone $user
        )->all();
    }
}
