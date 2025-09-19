<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Textbook;

use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Image\ImageIdList;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\Textbook\ConditionType;
use App\Platform\Domains\Textbook\Price;
use App\Platform\Domains\Textbook\Textbook;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\University\UniversityId;
use Tests\TestCase;

class TextbookTest extends TestCase
{
    public function test_インスタンスが生成できること(): void
    {
        //given
        $expectedId = new TextbookId();
        $expectedName = new String255('プログラミング入門');
        $expectedPrice = new Price(2000);
        $expectedDescription = new Text('初心者向けのプログラミング教科書です');
        $expectedImageIdList = new ImageIdList([new ImageId()]);
        $expectedUniversityId = new UniversityId();
        $expectedFacultyId = new FacultyId();
        $expectedConditionType = ConditionType::NEW;

        //when
        $textbook = new Textbook(
            id: $expectedId,
            name: $expectedName,
            price: $expectedPrice,
            description: $expectedDescription,
            imageIdList: $expectedImageIdList,
            universityId: $expectedUniversityId,
            facultyId: $expectedFacultyId,
            conditionType: $expectedConditionType,
        );

        //then
        $this->assertEquals($expectedId, $textbook->id);
        $this->assertEquals($expectedName, $textbook->name);
        $this->assertEquals($expectedPrice, $textbook->price);
        $this->assertEquals($expectedDescription, $textbook->description);
        $this->assertEquals($expectedImageIdList, $textbook->imageIdList);
        $this->assertEquals($expectedUniversityId, $textbook->universityId);
        $this->assertEquals($expectedFacultyId, $textbook->facultyId);
        $this->assertEquals($expectedConditionType, $textbook->conditionType);
    }

    public function test_staticで生成できること(): void
    {
        //given
        $expectedName = new String255('データベース概論');
        $expectedPrice = new Price(3000);
        $expectedDescription = new Text('データベースの基礎理論');
        $expectedImageIdList = new ImageIdList([]);
        $expectedUniversityId = new UniversityId();
        $expectedFacultyId = new FacultyId();
        $expectedConditionType = ConditionType::NEAR_NEW;

        //when
        $textbook = Textbook::create(
            name: $expectedName,
            price: $expectedPrice,
            description: $expectedDescription,
            imageIds: $expectedImageIdList,
            universityId: $expectedUniversityId,
            facultyId: $expectedFacultyId,
            conditionType: $expectedConditionType,
        );

        //then
        $this->assertEquals($expectedName, $textbook->name);
        $this->assertEquals($expectedPrice, $textbook->price);
        $this->assertEquals($expectedDescription, $textbook->description);
        $this->assertEquals($expectedImageIdList, $textbook->imageIdList);
        $this->assertEquals($expectedUniversityId, $textbook->universityId);
        $this->assertEquals($expectedFacultyId, $textbook->facultyId);
        $this->assertEquals($expectedConditionType, $textbook->conditionType);
        $this->assertInstanceOf(TextbookId::class, $textbook->id);
    }

    public function test_updateで情報を更新できること(): void
    {
        //given
        $textbook = $this->createSampleTextbook();
        $newName = new String255('更新されたタイトル');
        $newPrice = new Price(1500);
        $newDescription = new Text('更新された説明');
        $newImageIdList = new ImageIdList([new ImageId(), new ImageId()]);
        $newConditionType = ConditionType::SLIGHT_DAMAGE;

        //when
        $updatedTextbook = $textbook->update(
            name: $newName,
            price: $newPrice,
            description: $newDescription,
            imageIds: $newImageIdList,
            conditionType: $newConditionType,
        );

        //then
        $this->assertEquals($textbook->id, $updatedTextbook->id); // IDは変わらない
        $this->assertEquals($textbook->universityId, $updatedTextbook->universityId); // 大学IDは変わらない
        $this->assertEquals($textbook->facultyId, $updatedTextbook->facultyId); // 学部IDは変わらない
        $this->assertEquals($newName, $updatedTextbook->name);
        $this->assertEquals($newPrice, $updatedTextbook->price);
        $this->assertEquals($newDescription, $updatedTextbook->description);
        $this->assertEquals($newImageIdList, $updatedTextbook->imageIdList);
        $this->assertEquals($newConditionType, $updatedTextbook->conditionType);
    }

    private function createSampleTextbook(): Textbook
    {
        return new Textbook(
            id: new TextbookId(),
            name: new String255('サンプル教科書'),
            price: new Price(2000),
            description: new Text('サンプルの説明'),
            imageIdList: new ImageIdList([]),
            universityId: new UniversityId(),
            facultyId: new FacultyId(),
            conditionType: ConditionType::NEW,
        );
    }
}