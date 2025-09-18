<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\User;

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

class GetUserMeApiTest extends TestCase
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
    public function test_認証済みユーザーの情報が取得できること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();

        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

        $url = route('users.me');

        // when
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->token,
        ])->getJson($url);

        // then
        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
                'mail_address',
                'post_code',
                'address',
                'image_id',
                'university_id',
                'faculty_id',
            ]);
    }

    public function test_無効なトークンでアクセスするとエラーが返ること(): void
    {
        // given
        $url = route('users.me');

        // when - 完全に無効な形式のトークンを使用
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token_format',
        ])->getJson($url);

        // then - Laravel Sanctumの標準的な認証エラーメッセージを期待
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }
}
