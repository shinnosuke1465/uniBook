<?php

namespace Tests\Feature\Api;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Exceptions\NotFoundException;
use Hash;
use Illuminate\Auth\AuthenticationException;
use App\Models\User as UserDB;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Models\User;

/**
 * テスト時に事前ログインに使うトレイト
 */
trait ApiPreLoginTrait
{
    private function authenticate(
        string $mailAddress = 'test@example.com',
        string $password = 'password12345',
    ): void {
        $userDB = UserDB::query()
            ->where('mail_address', $mailAddress)
            ->first();
        if ($userDB === null) {
            throw new NotFoundException('ユーザーが見つかりません。');
        }
        if (!Hash::check($password, $userDB->password)) {
            throw new AuthenticationException('ユーザーが見つかりません。');
        }
        $this->actingAs($userDB);
    }

    /**
     * テスト用ユーザー・大学・学部をDBに登録し、Eloquentモデルを返す
     * @throws DuplicateKeyException
     * @throws DomainException
     */
    private function prepareUserWithFacultyAndUniversity(
        ?String255 $password = null,
        ?MailAddress $mailAddress = null,
        ?String255 $universityName = null,
        ?String255 $facultyName = null
    ): User {
        $inputUser = TestUserFactory::create(
            password: $password ?? new String255('password12345'),
            mailAddress: $mailAddress ?? new MailAddress(new String255('test@example.com')),
        );
        $university = TestUniversityFactory::create(
            $inputUser->universityId,
            $universityName ?? new String255('テスト大学')
        );
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create(
            $inputUser->facultyId,
            $facultyName ?? new String255('テスト学部'),
            $inputUser->universityId
        );
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);
        return User::find($inputUser->id->value);
    }
}
