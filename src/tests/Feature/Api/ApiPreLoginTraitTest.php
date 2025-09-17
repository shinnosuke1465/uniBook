<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Exceptions\IllegalUserException;
use App\Exceptions\NotFoundException;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;
use App\Platform\Domains\Shared\Name\Name;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\PostCode\PostCode;
use App\Platform\Domains\Shared\Address\Address;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApiPreLoginTraitTest extends TestCase
{
    use ApiPreLoginTrait;
    use DatabaseTransactions;

    private UserRepository $userRepository;

    private UniversityRepository $universityRepository;

    private FacultyRepository $facultyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->facultyRepository = new FacultyRepository();
        $this->universityRepository = new UniversityRepository();
    }

    /**
     * テスト用ユーザー・大学・学部をDBに登録し、Eloquentモデルを返す
     * @throws DuplicateKeyException
     * @throws DomainException
     * @throws IllegalUserException
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

    /**
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws DomainException
     * @throws IllegalUserException
     * @throws DuplicateKeyException
     */
    public function test_正しい情報で認証できること(): void
    {
        //given
        $user = $this->prepareUserWithFacultyAndUniversity();
        //when
        $this->authenticate('test@example.com', 'password12345');
        //then
        $this->assertAuthenticatedAs($user);
    }

    public function test_存在しないメールアドレスの場合例外が投げられること(): void
    {
        //given
        //when
        $this->expectException(NotFoundException::class);
        $this->authenticate('notfound@example.com', 'password12345');
        //then
        //例外が投げられる
    }

    public function test_パスワード不一致の場合例外が投げられること(): void
    {
        //given
        $user = $this->prepareUserWithFacultyAndUniversity();
        //when
        $this->expectException(AuthenticationException::class);
        $this->authenticate('test@example.com', 'wrongpassword');
        //then
        //例外が投げられる
    }
}
