<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\University;

use App\Platform\Domains\Shared\String\String255;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Exceptions\NotFoundException;
use Tests\TestCase;
use Tests\Feature\Api\ApiPreLoginTrait;

class CreateUniversityApiTest extends TestCase
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
     * @throws NotFoundException
     */
    public function test_認証済みユーザーが大学を作成できること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $url = route('universities.store');
        $requestData = [
            'name' => '新しいテスト大学',
        ];

        $expected = new String255('新しいテスト大学');

        // when
        $response = $this->postJson($url, $requestData);

        // then
        $response->assertNoContent();

        $actual = $this->universityRepository->findByName(new String255('新しいテスト大学'));
        $this->assertEquals($expected, $actual->name);
    }

    /**
     * @return void
     * @throws DomainException
     * @throws DuplicateKeyException
     * @throws NotFoundException
     */
    public function test_必須項目が欠けている場合エラーが返ること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $url = route('universities.store');
        $requestData = [
            // name が欠けている
        ];

        // when
        $response = $this->postJson($url, $requestData);

        // then
        $response->assertUnprocessable();
    }
}
