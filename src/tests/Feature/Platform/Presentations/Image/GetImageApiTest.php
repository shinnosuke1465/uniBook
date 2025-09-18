<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\Image;

use App\Platform\Domains\Shared\String\String255;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Image\ImageRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\Unit\Platform\Domains\Image\TestImageFactory;

class GetImageApiTest extends TestCase
{
    use DatabaseTransactions, ApiPreLoginTrait;

    private UserRepository $userRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;
    private ImageRepository $imageRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->facultyRepository = new FacultyRepository();
        $this->universityRepository = new UniversityRepository();
        $this->imageRepository = new ImageRepository();
    }

    public function test_認証済みユーザーが指定した画像を取得できること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $image = TestImageFactory::create(
            path: new String255('/path/to/test-image.jpg'),
            type: new String255('jpg')
        );
        $this->imageRepository->insert($image);

        $url = route('images.show', ['imageIdString' => $image->id->value]);

        // when
        $response = $this->getJson($url);

        // then
        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'path',
                'type',
            ])
            ->assertJson([
                'id' => $image->id->value,
                'path' => $image->path->value,
                'type' => $image->type->value,
            ]);
    }

    public function test_存在しない画像IDでアクセスするとエラーが返ること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $nonExistentId = '9fe68f34-8a7e-4ce1-97a9-1033c2c6deb8';
        $url = route('images.show', ['imageIdString' => $nonExistentId]);

        // when
        $response = $this->getJson($url);

        // then
        $response->assertNotFound();
    }

}
