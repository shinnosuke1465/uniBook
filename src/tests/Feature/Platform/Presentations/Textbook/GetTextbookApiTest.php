<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\Textbook;

use App\Exceptions\NotFoundException;
use App\Exceptions\RepositoryException;
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
use DomainException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;

class GetTextbookApiTest extends TestCase
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

    /**
     * @throws AuthenticationException
     * @throws DomainException
     * @throws NotFoundException
     * @throws RepositoryException
     */
    public function test_認証済みユーザーが指定した教科書を取得できること(): void
    {
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();
        $university = TestUniversityFactory::create(id: new UniversityId('de23bfca-fb58-4802-8eb3-270ba67815a6'), name: new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create(id: new FacultyId('e0d11e80-77ad-4b4c-b539-0a6118ad36bf'), name: new String255('テスト学部'), universityId: $university->id);
        $this->facultyRepository->insert($faculty);


        $textbook = TestTextbookFactory::create(
            name: new String255('テスト教科書'),
            price: new Price(1500),
            description: new Text('これはテスト用の教科書です。'),
            imageIdList: new ImageIdList([]),
            universityId: $university->id,
            facultyId: $faculty->id,
        );
        $this->textbookRepository->insert($textbook);

        $url = route('textbooks.show', ['textbookIdString' => $textbook->id->value]);

        $response = $this->getJson($url);

        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
                'price',
                'description',
                'image_ids',
                'university_id',
                'faculty_id',
                'condition_type',
            ])
            ->assertJson([
                'id' => $textbook->id->value,
                'name' => 'テスト教科書',
                'price' => 1500,
                'description' => 'これはテスト用の教科書です。',
                'image_ids' => [],
                'university_id' => $university->id->value,
                'faculty_id' => $faculty->id->value,
                'condition_type' => 'new',
            ]);
    }

    public function test_存在しない教科書IDを指定した場合は404エラーが返されること(): void
    {
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $nonExistentId = '00000000-0000-0000-0000-000000000000';
        $url = route('textbooks.show', ['textbookIdString' => $nonExistentId]);

        $response = $this->getJson($url);

        $response->assertNotFound();
    }
}
