<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\University;

use App\Platform\Domains\Shared\String\String255;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;

class GetUniversityApiTest extends TestCase
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

    public function test_認証済みユーザーが指定した大学を取得できること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $university = TestUniversityFactory::create(name: new String255('テスト大学'));
        $this->universityRepository->insert($university);

        $url = route('universities.show', [
            'universityIdString' => $university->id->value
        ]);

        // when
        $response = $this->get($url);

        // then
        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
            ])
            ->assertJson([
                'id' => $university->id->value,
                'name' => $university->name->value,
            ]);
    }

    public function test_存在しない大学IDでアクセスするとエラーが返ること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $nonExistentId = '9fe68f34-8a7e-4ce1-97a9-1033c2c6deb8';
        $url = route('universities.show', ['universityIdString' => $nonExistentId]);

        // when
        $response = $this->get($url);

        // then
        $response->assertNotFound();
    }

}
