<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\User;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Exceptions\IllegalUserException;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\User\AuthenticateToken\AuthenticateToken;
use App\Platform\Domains\User\UserId;
use App\Platform\Domains\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Platform\Domains\User\User;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User as UserDB;
use Hash;
use App\Platform\Infrastructures\User\AuthenticateToken\AuthenticateTokenFactory;

readonly class UserRepository implements UserRepositoryInterface
{
    /**
     * @throws DomainException
     * @throws IllegalUserException
     */
    public function getAuthenticatedUser(): User
    {
        /**@var UserDB $user */
        $user = Auth::user();
        if ($user === null) {
            throw new IllegalUserException('認証済みユーザー情報が取得できませんでした。');
        }

        return UserFactory::create($user);
    }

    /**
     * @throws IllegalUserException
     */
    public function createToken(MailAddress $mailAddress, String255 $password): AuthenticateToken
    {
        $user = UserDB::where('mail_address', $mailAddress->mailAddress->value)->first();
        if ($user === null) {
            throw new IllegalUserException('認証済みユーザー情報が取得できませんでした。');
        }

        if (!Hash::check($password->value, $user->password)) {
            throw new IllegalUserException('パスワードが違います。');
        }

        return AuthenticateTokenFactory::create(
            $user->createToken('authenticate_token')->plainTextToken
        );
    }

    public function deleteToken(?string $token = null): void
    {
        $tokenToDelete = $token ?? request()->bearerToken();
        if ($tokenToDelete === null) {
            throw new IllegalUserException('認証トークンが見つかりません。');
        }

        $accessToken = PersonalAccessToken::findToken($tokenToDelete);
        if ($accessToken === null) {
            throw new IllegalUserException('無効なトークンです。');
        }

        $accessToken->delete();
    }

    /**
     * @throws DomainException
     */
    public function findById(
        UserId $userId
    ): ?User {
        $userModel = UserDB::find($userId->value);
        if ($userModel === null) {
            return null;
        }

        return UserFactory::create($userModel);
    }

    /**
     * @throws DuplicateKeyException
     * @throws DomainException
     */
    public function insertWithLoginId(
        User $user, MailAddress $mailAddress
    ): AuthenticateToken {
        if ($this->hasDuplicateLoginId($user->mailAddress)) {
            throw new DuplicateKeyException('loginIdが重複しています。');
        }

        $userModel = UserDB::create([
            'id' => $user->id->value,
            'name' => $user->name->name,
            'password' => Hash::make($user->password->value),
            'post_code' => $user->postCode->postCode->value,
            'address' => $user->address->address->value,
            'mail_address' => $user->mailAddress->mailAddress->value,
            'image_id' => $user->imageId?->value,
            'faculty_id' => $user->facultyId->value,
            'university_id' => $user->universityId->value,
        ]);

        return AuthenticateTokenFactory::create(
            $userModel->createToken('authenticate_token')->plainTextToken
        );
    }

    /**
     * @throws DomainException
     */
    public function findByMailAddress(MailAddress $mailAddress): ?User
    {
        $userModel = UserDB::where('mail_address', $mailAddress->mailAddress->value)->first();
        if ($userModel === null) {
            return null;
        }

        return UserFactory::create($userModel);
    }

//    public function update(User $user): void
//    {
//
//    }

    private function hasDuplicateLoginId(MailAddress $mailAddress): bool
    {
        $query = UserDB::where('mail_address', $mailAddress->mailAddress->value);

        return $query->exists();
    }
}
