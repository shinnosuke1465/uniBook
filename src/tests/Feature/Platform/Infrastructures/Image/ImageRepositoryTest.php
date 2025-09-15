<?php

declare(strict_types=1);

namespace Feature\Platform\Infrastructures\Image;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Platform\Domains\Image\ImageIdList;
use App\Platform\Domains\Image\ImageList;
use App\Platform\Infrastructures\Image\ImageRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Image\TestImageFactory;
use App\Platform\Domains\Image\ImageId;
use App\Models\Image as ImageDB;

class ImageRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private ImageRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ImageRepository();
    }

    /**
     * @throws DuplicateKeyException
     */
    public function test_insertで画像を保存できること(): void
    {
        $image = TestImageFactory::create();
        $this->repository->insert($image);
        $imageDB = ImageDB::find($image->id->value);
        $this->assertNotNull($imageDB);
        $this->assertSame($image->id->value, $imageDB->id);
        $this->assertSame($image->path->value, $imageDB->path);
        $this->assertSame($image->type->value, $imageDB->type);
    }

    /**
     * @throws DuplicateKeyException
     */
    public function test_insertで重複した画像IDを挿入するとエラーが発生すること(): void
    {
        $image = TestImageFactory::create();
        $this->repository->insert($image);
        $this->expectException(DuplicateKeyException::class);
        $this->repository->insert($image);
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_findByIdで画像を取得できること(): void
    {
        $image = TestImageFactory::create();
        $this->repository->insert($image);
        $actual = $this->repository->findById($image->id);
        $this->assertNotNull($actual);
        $this->assertSame($image->id->value, $actual->id->value);
        $this->assertSame($image->path->value, $actual->path->value);
        $this->assertSame($image->type->value, $actual->type->value);
    }

    /**
     * @throws DomainException
     */
    public function test_findByIdで存在しないIDを指定した場合nullが返ること(): void
    {
        $notExistsId = new ImageId();
        $actual = $this->repository->findById($notExistsId);
        $this->assertNull($actual);
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_ImageIdsを指定して取得できること(): void
    {
        //given
        $insertImages = [
            TestImageFactory::create(),
            TestImageFactory::create(),
        ];

        foreach ($insertImages as $image) {
            $this->repository->insert($image);
        }

        $searchIds = new ImageIdList([
            $insertImages[0]->id,
            $insertImages[1]->id,
        ]);

        $expectedImageList = new ImageList(
            [
                $insertImages[0],
                $insertImages[1],
            ]
        );

        //when
        $actualImageList = $this->repository->findByIds($searchIds);

        //then
        $this->assertEquals($expectedImageList, $actualImageList);
    }

    /**
     * @throws DomainException
     */
    public function test_指定したImageIdsで検索した結果がなければからのImageListが返ること(): void
    {
        //given
        $searchIds = new ImageIdList([
            new ImageId(),
            new ImageId(),
        ]);

        //when
        $expectedImageList = $this->repository->findByIds($searchIds);

        //then
        $this->assertEmpty($expectedImageList->toArray());
    }
}

