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

class GetUniversitiesApiTest extends TestCase
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

    public function test_認証済みユーザーが大学一覧を取得できること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        // 追加の大学を作成
        $university2 = TestUniversityFactory::create(name: new String255('テスト大学2'));
        $this->universityRepository->insert($university2);

        $url = route('universities.index');

        // when
        $response = $this->get($url);

        // then
        $response->assertOk()
            ->assertJsonStructure(
                [
                    'universities' => [
                        '*' => [
                            'id',
                            'name',
                        ]
                    ]
                ]
            );
    }
}
