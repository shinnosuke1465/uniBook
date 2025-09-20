<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\Textbook;

use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\Textbook\Price;
use App\Platform\Domains\Textbook\ConditionType;
use App\Platform\Domains\Image\ImageIdList;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Infrastructures\Image\ImageRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Image\TestImageFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;

class UpdateTextbooksApiTest extends TestCase
{
    use DatabaseTransactions, ApiPreLoginTrait;

    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;

    private ImageRepository $imageRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
        $this->imageRepository = new ImageRepository();
    }

    public function test_認証済みユーザーが教科書を更新できること(): void
    {
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $university = TestUniversityFactory::create(id: new UniversityId('de23bfca-fb58-4802-8eb3-270ba67815a6'), name: new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create(id: new FacultyId('e0d11e80-77ad-4b4c-b539-0a6118ad36bf'), name: new String255('テスト学部'), universityId: $university->id);
        $this->facultyRepository->insert($faculty);

        $image1 = TestImageFactory::create(path: new String255('/path/to/image1.jpg'), type: new String255('jpg'));
        $image2 = TestImageFactory::create(path: new String255('/path/to/image2.png'), type: new String255('png'));
        $this->imageRepository->insert($image1);
        $this->imageRepository->insert($image2);


        $textbook = TestTextbookFactory::create(
            name: new String255('テスト教科書'),
            price: new Price(1500),
            description: new Text('これはテスト用の教科書です。'),
            imageIdList: new ImageIdList([]),
            universityId: $university->id,
            facultyId: $faculty->id,
        );
        $this->textbookRepository->insert($textbook);

        $url = route('textbooks.update', ['textbookIdString' => $textbook->id->value]);
        $requestData = [
            'name' => '更新後教科書',
            'price' => 2000,
            'description' => '更新後の説明です。',
            'condition_type' => 'new',
            'university_id' => $university->id->value,
            'faculty_id' => $faculty->id->value,
            'image_ids' => [$image1->id->value, $image2->id->value],
        ];

        $response = $this->putJson($url, $requestData);

        $response->assertNoContent();

        $updatedTextbook = $this->textbookRepository->findById($textbook->id);
        $this->assertNotNull($updatedTextbook);
        $this->assertEquals('更新後教科書', $updatedTextbook->name->value);
        $this->assertEquals(2000, $updatedTextbook->price->value);
        $this->assertEquals('更新後の説明です。', $updatedTextbook->description->value);
        $this->assertEquals('new', $updatedTextbook->conditionType->value);
        $this->assertEquals([$image1->id->value, $image2->id->value], $updatedTextbook->imageIdList->toArray());
    }
}
