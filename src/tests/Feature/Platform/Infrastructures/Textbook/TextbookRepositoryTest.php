<?php

declare(strict_types=1);

namespace Feature\Platform\Infrastructures\Textbook;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Exceptions\RepositoryException;
use App\Platform\Domains\Textbook\Textbook;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use App\Models\TextbookImage;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Textbook\Price;

class TextbookRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private TextbookRepository $repository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;

    /** @var Textbook[] */
    private array $testData;

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     * @throws RepositoryException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
        $this->testData = $this->prepareTestData();
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_insertで重複した教科書を挿入するとエラーが発生すること(): void
    {
        //given
        $duplicateTextbook = TestTextbookFactory::create(
            id: $this->testData[0]->id,
            universityId: $this->testData[0]->universityId,
            facultyId: $this->testData[0]->facultyId
        );

        //when
        //then
        $this->expectException(DuplicateKeyException::class);
        $this->expectExceptionMessage('指定された教科書は既に存在します。textbookId: '. $duplicateTextbook->id->value);
        $this->repository->insert($duplicateTextbook);
    }

    /**
     * @throws DomainException
     */
    public function test_findByIdで教科書が取得できること(): void
    {
        //given
        //when
        $actualTextbook = $this->repository->findById($this->testData[0]->id);

        //then
        $this->assertEquals($this->testData[0], $actualTextbook);
    }

    /**
     * @throws DomainException
     */
    public function test_findByIdで存在しない教科書はnullが返ること(): void
    {
        //given
        //when
        $actualTextbook = $this->repository->findById(new TextbookId());

        //then
        $this->assertNull($actualTextbook);
    }

    /**
     * @throws DomainException
     * @throws RepositoryException
     */
    public function test_findAllで全ての教科書が取得できること(): void
    {
        //given
        //when
        $actualTextbooks = $this->repository->findAll();

        //then
        $this->assertCount(3, $actualTextbooks);

        // IDで比較
        $actualIds = array_map(fn($textbook) => $textbook->id->value, $actualTextbooks);
        $expectedIds = array_map(fn($textbook) => $textbook->id->value, $this->testData);

        foreach ($expectedIds as $expectedId) {
            $this->assertContains($expectedId, $actualIds);
        }
    }

    /**
     * @throws DomainException
     * @throws RepositoryException
     */
    public function test_updateで教科書情報が更新できること(): void
    {
        //given
        $updatedTextbook = TestTextbookFactory::create(
            id: $this->testData[0]->id,
            name: new String255('更新された教科書'),
            price: new Price(2000),
            universityId: $this->testData[0]->universityId,
            facultyId: $this->testData[0]->facultyId
        );

        //when
        $this->repository->update($updatedTextbook);

        //then
        $actualTextbook = $this->repository->findById($this->testData[0]->id);
        $this->assertEquals($updatedTextbook->name, $actualTextbook->name);
        $this->assertEquals($updatedTextbook->price, $actualTextbook->price);
    }

    /**
     * @throws DomainException
     * @throws RepositoryException
     */
    public function test_updateで存在しない教科書を更新するとエラーが発生すること(): void
    {
        //given
        $nonExistentTextbook = TestTextbookFactory::create(id: new TextbookId());

        //when
        //then
        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('指定された教科書が見つかりません。');
        $this->repository->update($nonExistentTextbook);
    }

    /**
     * @throws DomainException
     * @throws RepositoryException
     */
    public function test_deleteで教科書が削除できること(): void
    {
        //given
        $textbookId = $this->testData[0]->id;

        //when
        $this->repository->delete($textbookId);

        //then
        $deletedTextbook = $this->repository->findById($textbookId);
        $this->assertNull($deletedTextbook);

        // 関連する画像データも削除されることを確認
        $imageRelations = TextbookImage::where('textbook_id', $textbookId->value)->get();
        $this->assertCount(0, $imageRelations);
    }

    /**
     * @throws DomainException
     * @throws RepositoryException
     */
    public function test_deleteで存在しない教科書を削除するとエラーが発生すること(): void
    {
        //given
        $nonExistentId = new TextbookId();

        //when
        //then
        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('指定された教科書が見つかりません。');
        $this->repository->delete($nonExistentId);
    }

    /**
     * @return Textbook[] $textbooks
     *
     * @throws DomainException
     * @throws RepositoryException
     */
    private function prepareTestData(): array
    {
        $university = TestUniversityFactory::create();
        $this->universityRepository->insert($university);

        $faculty = TestFacultyFactory::create(universityId: $university->id);
        $this->facultyRepository->insert($faculty);

        $items = [
            TestTextbookFactory::create(universityId: $university->id, facultyId: $faculty->id),
            TestTextbookFactory::create(universityId: $university->id, facultyId: $faculty->id),
            TestTextbookFactory::create(universityId: $university->id, facultyId: $faculty->id),
        ];
        foreach ($items as $item) {
            $this->repository->insert($item);
        }
        return $items;
    }
}
