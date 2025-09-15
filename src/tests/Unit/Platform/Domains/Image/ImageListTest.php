<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Image;

use App\Exceptions\DomainException;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Image\ImageIdList;
use App\Platform\Domains\Image\ImageList;
use Tests\TestCase;

class ImageListTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function test_ImageIdListを返すことができる(): void
    {
        //given
        $imageId1 = new ImageId('00000000-0000-0000-0000-000000000001');
        $imageId2 = new ImageId('00000000-0000-0000-0000-000000000002');

        $imageList = new ImageList([
            TestImageFactory::create(id: $imageId1),
            TestImageFactory::create(id: $imageId2),
        ]);

        $expectedImageIdList = new ImageIdList([
            $imageId1,
            $imageId2,
        ]);

        //when
        $actualImageIdList = $imageList->getIds();

        //then
        $this->assertEquals($expectedImageIdList, $actualImageIdList);
    }

    public function test_要素数を取得できること(): void
    {
        //given
        $imageList = new ImageList([
            TestImageFactory::create(),
            TestImageFactory::create(),
        ]);
        $expectedCount = 2;

        //when
        $actualCount = $imageList->count();

        //then
        $this->assertSame($expectedCount, $actualCount);
    }
}
