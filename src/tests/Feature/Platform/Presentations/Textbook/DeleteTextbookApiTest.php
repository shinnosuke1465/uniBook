<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\Textbook;

use App\Exceptions\DuplicateKeyException;
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

class DeleteTextbookApiTest extends TestCase
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
     * @throws DomainException
     * @throws DuplicateKeyException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws RepositoryException
     */
    public function test_認証済みユーザーが教科書を削除できること(): void
    {
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $university = TestUniversityFactory::create(id: new UniversityId('de23bfca-fb58-4802-8eb3-270ba67815a6'), name: new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create(id: new FacultyId('e0d11e80-77ad-4b4c-b539-0a6118ad36bf'), name: new String255('テスト学部'), universityId: $university->id);
        $this->facultyRepository->insert($faculty);


        $textbook = TestTextbookFactory::create(
            name: new String255('削除対象教科書'),
            price: new Price(1500),
            description: new Text('削除される教科書です。'),
            imageIdList: new ImageIdList([]),
            universityId: $university->id,
            facultyId: $faculty->id,
        );
        $this->textbookRepository->insert($textbook);

        $this->assertCount(1, $this->textbookRepository->findAll());

        $url = route('textbooks.destroy', ['textbookIdString' => $textbook->id->value]);

        $response = $this->deleteJson($url);

        $response->assertNoContent();
        $this->assertCount(0, $this->textbookRepository->findAll());
    }

    public function test_存在しない教科書IDを指定して削除した場合は404エラーが返されること(): void
    {
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate();

        $nonExistentId = '00000000-0000-0000-0000-000000000000';
        $url = route('textbooks.destroy', ['textbookIdString' => $nonExistentId]);

        $response = $this->deleteJson($url);

        $response->assertNotFound();
    }

}
