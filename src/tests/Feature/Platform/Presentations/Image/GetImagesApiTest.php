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

class GetImagesApiTest extends TestCase
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

    public function test_認証済みユーザーが指定したIDリストの画像一覧を取得できること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $image1 = TestImageFactory::create(path: new String255('/path/to/image1.jpg'), type: new String255('jpg'));
        $image2 = TestImageFactory::create(path: new String255('/path/to/image2.png'), type: new String255('png'));
        $this->imageRepository->insert($image1);
        $this->imageRepository->insert($image2);

        $url = route('images.index');
        $requestData = [
            'ids' => [$image1->id->value, $image2->id->value],
        ];

        // when
        $response = $this->getJson($url . '?' . http_build_query($requestData));

        // then
        $response->assertOk()
            ->assertJsonStructure(
                [
                    'images' => [
                        '*' => [
                            'id',
                            'path',
                            'type',
                        ]
                    ]
                ]
            );
    }

    public function test_空のIDリストで画像一覧を取得すると空配列が返ること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $url = route('images.index');
        $requestData = [
            'ids' => [],
        ];

        // when
        $response = $this->getJson($url . '?' . http_build_query($requestData));

        // then
        $response->assertOk()
            ->assertJson([]);
    }
}
