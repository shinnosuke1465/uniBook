<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\Textbook;

use App\Exceptions\DuplicateKeyException;
use App\Exceptions\NotFoundException;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Infrastructures\Image\ImageRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use App\Platform\Infrastructures\Deal\DealRepository;
use App\Platform\Infrastructures\DealEvent\DealEventRepository;
use DomainException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Runner\Exception;
use Tests\TestCase;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Image\TestImageFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;

class CreateTextbookApiTest extends TestCase
{
    use DatabaseTransactions, ApiPreLoginTrait;

    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;
    private DealRepository $dealRepository;
    private DealEventRepository $dealEventRepository;
    private ImageRepository $imageRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
        $this->dealRepository = new DealRepository();
        $this->dealEventRepository = new DealEventRepository();
        $this->imageRepository = new ImageRepository();
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws Exception
     */
    public function test_認証済みユーザーが教科書を作成できること(): void
    {
        //given
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

        $url = route('textbooks.store');
        $requestData = [
            'name' => 'テスト教科書',
            'price' => 1500,
            'description' => 'これはテスト用の教科書です。',
            'condition_type' => 'new',
            'university_id' => $university->id->value,
            'faculty_id' => $faculty->id->value,
            'image_ids' => [$image1->id->value, $image2->id->value],
        ];

        $response = $this->postJson($url, $requestData);

        $response->assertNoContent();

        $textbooks = $this->textbookRepository->findAll();
        $this->assertCount(1, $textbooks);

        $createdTextbook = $textbooks[0];
        $this->assertEquals('テスト教科書', $createdTextbook->name->value);
        $this->assertEquals(1500, $createdTextbook->price->value);
        $this->assertEquals('これはテスト用の教科書です。', $createdTextbook->description->value);
        $this->assertEquals('new', $createdTextbook->conditionType->value);
        $this->assertEquals($university->id->value, $createdTextbook->universityId->value);
        $this->assertEquals($faculty->id->value, $createdTextbook->facultyId->value);
    }

}
