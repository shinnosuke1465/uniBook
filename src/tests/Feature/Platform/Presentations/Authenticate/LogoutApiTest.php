<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\Authenticate;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Exceptions\IllegalUserException;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Feature\Api\ApiPreLoginTrait;

class LogoutApiTest extends TestCase
{
    use DatabaseTransactions, ApiPreLoginTrait;

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
     * @throws IllegalUserException
     */
    public function test_認証済みユーザーがログアウトできること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();

        // トークンを生成
        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

        // トークンが存在することを確認
        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'authenticate_token',
            'token' => hash('sha256', explode('|', $token->token)[1])
        ]);

        $url = route('logout');

        // when
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->token,
        ])->postJson($url);

        // then
        $response->assertNoContent();

        // トークンが削除されていることを確認
        $this->assertDatabaseMissing('personal_access_tokens', [
            'name' => 'authenticate_token',
            'token' => hash('sha256', explode('|', $token->token)[1])
        ]);
    }
}
