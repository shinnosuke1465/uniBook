<?php

declare(strict_types=1);

namespace Feature\Platform\Infrastructures\Faculty;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Platform\Domains\Faculty\Faculty;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Illuminate\Support\Facades\DB;
use App\Models\Faculty as FacultyDB;
use App\Models\University as UniversityDB;

class FacultyRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private FacultyRepository $repository;
    private UniversityRepository $universityRepository;

    /** @var Faculty[] */
    private array $testData;

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new FacultyRepository();
        $this->universityRepository = new UniversityRepository();
        $this->testData = $this->prepareTestData();
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_insertで重複した学部を挿入するとエラーが発生すること(): void
    {
        //given
        $duplicateFaculty = TestFacultyFactory::create(id: $this->testData[0]->id, universityId: $this->testData[0]->universityId);

        //when
        //then
        $this->expectException(DuplicateKeyException::class);
        $this->expectExceptionMessage('学部が重複しています。');
        $this->repository->insert($duplicateFaculty);
    }

    /**
     * @throws DomainException
     */
    public function test_findByIdで学部が取得できること(): void
    {
        //given
        //when
        $actualFaculty = $this->repository->findById($this->testData[0]->id);

        //then
        $this->assertEquals($this->testData[0], $actualFaculty);
    }

    /**
     * @throws DomainException
     */
    public function test_findByIdで存在しない学部はnullが返ること(): void
    {
        //given
        //when
        $actualFaculty = $this->repository->findById(new FacultyId());

        //then
        $this->assertNull($actualFaculty);
    }

    /**
     * @return Faculty[] $faculty
     *
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    private function prepareTestData(): array
    {
        $university = TestUniversityFactory::create();
        $this->universityRepository->insert($university);

        $items = [
            TestFacultyFactory::create(universityId: $university->id),
            TestFacultyFactory::create(universityId: $university->id),
            TestFacultyFactory::create(universityId: $university->id),
        ];
        foreach ($items as $item) {
            $this->repository->insert($item);
        }
        return $items;
    }
}
