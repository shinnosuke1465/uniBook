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
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;

class GetTextbooksApiTest extends TestCase
{
    use DatabaseTransactions, ApiPreLoginTrait;

    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
    }

    public function test_認証済みユーザーが教科書一覧を取得できること(): void
    {
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $university = TestUniversityFactory::create(id: new UniversityId('de23bfca-fb58-4802-8eb3-270ba67815a6'), name: new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create(id: new FacultyId('e0d11e80-77ad-4b4c-b539-0a6118ad36bf'), name: new String255('テスト学部'), universityId: $university->id);
        $this->facultyRepository->insert($faculty);


        $textbook1 = TestTextbookFactory::create(
            name: new String255('テスト教科書'),
            price: new Price(1500),
            description: new Text('これはテスト用の教科書です。'),
            imageIdList: new ImageIdList([]),
            universityId: $university->id,
            facultyId: $faculty->id,
        );

        $textbook2 = TestTextbookFactory::create(
            name: new String255('教科書2'),
            price: new Price(2000),
            description: new Text('説明2'),
            imageIdList: new ImageIdList([]),
            universityId: $university->id,
            facultyId: $faculty->id,
            conditionType: ConditionType::DAMAGE
        );

        $this->textbookRepository->insert($textbook1);
        $this->textbookRepository->insert($textbook2);

        $url = route('textbooks.index');

        $response = $this->getJson($url);

        $response->assertOk()
            ->assertJsonStructure([
                'textbooks' => [
                    '*' => [
                        'id',
                        'name',
                        'price',
                        'description',
                        'image_ids',
                        'university_id',
                        'faculty_id',
                        'condition_type',
                    ]
                ]
            ])
            ->assertJsonCount(2, 'textbooks');

        $responseData = $response->json();
        $this->assertContains($textbook1->id->value, array_column($responseData['textbooks'], 'id'));
        $this->assertContains($textbook2->id->value, array_column($responseData['textbooks'], 'id'));
    }

    public function test_認証済みユーザーが空の教科書一覧を取得できること(): void
    {
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $url = route('textbooks.index');

        $response = $this->getJson($url);

        $response->assertOk()
            ->assertJsonStructure([
                'textbooks' => []
            ])
            ->assertJsonCount(0, 'textbooks');
    }
}
