<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\Faculty;

use App\Platform\Domains\Shared\String\String255;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;

class CreateFacultyApiTest extends TestCase
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

    public function test_認証済みユーザーが学部を作成できること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $university = TestUniversityFactory::create(name: new String255('テスト大学'));
        $this->universityRepository->insert($university);

        $url = route('faculties.store');
        $requestData = [
            'name' => '新しいテスト学部',
            'university_id' => $university->id->value,
        ];

        // when
        $response = $this->postJson($url, $requestData);

        // then
        $response->assertNoContent();
    }

    public function test_必須項目が欠けている場合エラーが返ること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $url = route('faculties.store');
        $requestData = [
            'name' => '新しいテスト学部',
            // universityId が欠けている
        ];

        // when
        $response = $this->postJson($url, $requestData);

        // then
        $response->assertUnprocessable();
    }

    public function test_存在しない大学IDで学部を作成しようとするとエラーが返ること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $url = route('faculties.store');
        $requestData = [
            'name' => '新しいテスト学部',
            'universityId' => '9fe68f34-8a7e-4ce1-97a9-1033c2c6deb8',
        ];

        // when
        $response = $this->postJson($url, $requestData);

        // then
        $response->assertUnprocessable();
    }
}
