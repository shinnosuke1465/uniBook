<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Image;

use App\Exceptions\DomainException;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Image\ImageIdList;
use Tests\TestCase;

class ImageIdListTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function test_インスタンス化した時に昇順ソートされること(): void
    {
        //given
        $imageId1 = new ImageId('00000000-0000-0000-0000-000000000001');
        $imageId2 = new ImageId('00000000-0000-0000-0000-000000000002');
        $imageId3 = new ImageId('00000000-0000-0000-0000-000000000003');
        $unsortedRoleIds = [
            $imageId3,
            $imageId1,
            $imageId2,
        ];
        $expectedImageIds = [
            $imageId1,
            $imageId2,
            $imageId3,
        ];

        //when
        $imageIdList = new ImageIdList($unsortedRoleIds);

        //then
        $this->assertEquals($expectedImageIds, $imageIdList->toArray());
    }

    /**
     * @dataProvider equalsDataProvider
     */
    public function test_インスタンスの要素が等しいかどうか確認できること(
        ImageIdList $expectedImageIdList,
        ImageIdList $actualImageIdList,
        bool $result
    ): void {
        //given
        //when
        $equals = $expectedImageIdList->equals($actualImageIdList);
        //then
        $this->assertSame($result, $equals);
    }

    /**
     * @return array<string, array{
     *     expectedImageIdList: ImageIdList,
     *      actualImageIdList: ImageIdList,
     *      result: bool
     *     }
     * >
     * @throws DomainException
     */
    public static function equalsDataProvider(): array
    {
        return [
            '数と順番と要素が等しいときにtrueになること' => [
                'expectedImageIdList' => new ImageIdList([
                    new ImageId('00000000-0000-0000-0000-000000000001'),
                    new ImageId('00000000-0000-0000-0000-000000000002'),
                ]),
                'actualImageIdList' => new ImageIdList([
                    new ImageId('00000000-0000-0000-0000-000000000001'),
                    new ImageId('00000000-0000-0000-0000-000000000002'),
                ]),
                'result' => true,
            ],
            '数が異なるときにfalseになること' => [
                'expectedImageIdList' => new ImageIdList([
                    new ImageId('00000000-0000-0000-0000-000000000001'),
                    new ImageId('00000000-0000-0000-0000-000000000002'),
                ]),
                'actualImageIdList' => new ImageIdList([
                    new ImageId('00000000-0000-0000-0000-000000000001'),
                ]),
                'result' => false,
            ],
            '順番が異なるときにtrueになること' => [
                'expectedImageIdList' => new ImageIdList([
                    new ImageId('00000000-0000-0000-0000-000000000001'),
                    new ImageId('00000000-0000-0000-0000-000000000002'),
                ]),
                'actualImageIdList' => new ImageIdList([
                    new ImageId('00000000-0000-0000-0000-000000000002'),
                    new ImageId('00000000-0000-0000-0000-000000000001'),
                ]),
                'result' => true,
            ],
            '要素が異なるときにfalseになること' => [
                'expectedImageIdList' => new ImageIdList([
                    new ImageId('00000000-0000-0000-0000-000000000001'),
                    new ImageId('00000000-0000-0000-0000-000000000002'),
                ]),
                'actualImageIdList' => new ImageIdList([
                    new ImageId('00000000-0000-0000-0000-000000000003'),
                    new ImageId('00000000-0000-0000-0000-000000000004'),
                ]),
                'result' => false,
            ],
        ];
    }

    /**
     * @throws DomainException
     */
    public function test_文字列配列への変換が正常に行えること(): void
    {
        //given
        $expectImageIdValues = [
            '00000000-0000-0000-0000-000000000001',
            '00000000-0000-0000-0000-000000000002',
        ];
        $imageIdList = new ImageIdList([
            new ImageId($expectImageIdValues[0]),
            new ImageId($expectImageIdValues[1]),
        ]);

        //when
        $imageIdValues = $imageIdList->toStringArray();

        //then
        $this->assertSame($expectImageIdValues, $imageIdValues);
    }
}
