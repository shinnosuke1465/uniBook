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
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;

class GetFacultyApiTest extends TestCase
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

    public function test_認証済みユーザーが指定した学部を取得できること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $university = TestUniversityFactory::create(name: new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create(name: new String255('テスト学部'), universityId: $university->id);
        $this->facultyRepository->insert($faculty);

        $url = route('faculties.show', ['facultyIdString' => $faculty->id->value]);

        // when
        $response = $this->getJson($url);

        // then
        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
                'universityId',
            ])
            ->assertJson([
                'id' => $faculty->id->value,
                'name' => $faculty->name->value,
                'universityId' => $faculty->universityId->value,
            ]);
    }

    public function test_存在しない学部IDでアクセスするとエラーが返ること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $nonExistentId = '9fe68f34-8a7e-4ce1-97a9-1033c2c6deb8';
        $url = route('faculties.show', ['facultyIdString' => $nonExistentId]);

        // when
        $response = $this->getJson($url);

        // then
        $response->assertNotFound();
    }
}
