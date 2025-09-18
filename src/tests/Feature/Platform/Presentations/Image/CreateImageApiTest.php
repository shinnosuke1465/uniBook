<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\Image;

use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Image\ImageRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Tests\Feature\Api\ApiPreLoginTrait;

class CreateImageApiTest extends TestCase
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

    public function test_認証済みユーザーが画像をアップロードできること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $url = route('images.store');

        // when
        $response = $this->postJson($url, [
            'path' => 'test-image.jpg',
            'type' => 'jpg',
        ]);

        // then
        $response->assertNoContent();


    }

    public function test_画像ファイルが指定されていない場合エラーが返ること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $url = route('images.store');

        // when
        $response = $this->postJson($url, [
            // image が欠けている
        ]);

        // then
        $response->assertUnprocessable();
    }

    public function test_画像ファイル以外をアップロードしようとするとエラーが返ること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $url = route('images.store');
        $file = UploadedFile::fake()->create('document.txt', 100);

        // when
        $response = $this->postJson($url, [
            'image' => $file,
        ]);

        // then
        $response->assertUnprocessable();
    }
}
