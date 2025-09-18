<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\Authenticate;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\User\UserId;
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
     */
    public function test_認証済みユーザーがログアウトできること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $url = route('logout');

        // when
        $response = $this->postJson($url);

        // then
        $response->assertNoContent();
    }

    public function test_未認証ユーザーがログアウトしようとするとエラーが返ること(): void
    {
        // given
        $url = route('logout');

        // when
        $response = $this->postJson($url);

        // then
        $response->assertUnauthorized();
    }
}
