<?php

declare(strict_types=1);

namespace Feature\Platform\Infrastructures\University;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Platform\Domains\University\University;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Infrastructures\University\UniversityRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;

class UniversityRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private UniversityRepository $repository;

    /** @var University[] */
    private array $testData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new UniversityRepository();
        $this->testData = $this->prepareTestData();
    }

    /**
     * @throws DuplicateKeyException
     */
    public function test_insertで重複した大学を挿入するとエラーが発生すること(): void
    {
        //given
        $duplicateUniversity = TestUniversityFactory::create(id: $this->testData[0]->id);

        //when
        //then
        $this->expectException(DuplicateKeyException::class);
        $this->expectExceptionMessage('学部が重複しています。');
        $this->repository->insert($duplicateUniversity);
    }

    /**
     * @throws DomainException
     */
    public function test_findByIdで大学が取得できること(): void
    {
        //given
        //when
        $actualUniversity = $this->repository->findById($this->testData[0]->id);

        //then
        $this->assertEquals($this->testData[0], $actualUniversity);
    }

    /**
     * @throws DomainException
     */
    public function test_findByIdで存在しない大学はnullが返ること(): void
    {
        //given
        //when
        $actualUniversity = $this->repository->findById(new UniversityId());

        //then
        $this->assertNull($actualUniversity);
    }


    /**
     * @return University[] $university
     *
     * @throws DuplicateKeyException
     */
    private function prepareTestData(): array
    {
        $items = [
            TestUniversityFactory::create(),
            TestUniversityFactory::create(),
            TestUniversityFactory::create(),
        ];
        foreach ($items as $item) {
            $this->repository->insert($item);
        }
        return $items;
    }
}
