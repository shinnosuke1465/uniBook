<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\Authenticate;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class CreateTokenApiTest extends TestCase
{
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
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_正しいメールアドレスとパスワードでトークンが取得できること(): void
    {
        // given
        $inputUser = TestUserFactory::create();
        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        $url = route('tokens.create');
        $requestData = [
            'mail_address' => $inputUser->mailAddress->mailAddress->value,
            'password' => $inputUser->password->value,
        ];

        // when
        $response = $this->postJson($url, $requestData);

        // then
        $response->assertOk()
            ->assertJsonStructure([
                'token'
            ]);
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_間違ったパスワードでエラーが返ること(): void
    {
        // given
        $inputUser = TestUserFactory::create();
        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        $url = route('tokens.create');
        $requestData = [
            'mail__address' => $inputUser->mailAddress->mailAddress->value,
            'password' => 'wrong_password',
        ];

        // when
        $response = $this->postJson($url, $requestData);

        // then
        $response->assertUnprocessable();
    }

    public function test_存在しないメールアドレスでエラーが返ること(): void
    {
        // given
        $url = route('tokens.create');
        $requestData = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        // when
        $response = $this->postJson($url, $requestData);

        // then
        $response->assertUnprocessable();
    }
}
