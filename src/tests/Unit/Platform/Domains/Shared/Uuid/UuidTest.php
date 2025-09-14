<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Shared\Uuid;

use App\Exceptions\DomainException;
use Tests\TestCase;

class UuidTest extends TestCase
{
    public function test_引数なしで生成した場合に正しい書式になること(): void
    {
        //given
        $testId = new TestId();

        //when
        //then
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $testId->value,
        );
    }

    public function test_引数指定で生成した場合に同じ値が設定されること(): void
    {
        //given
        $testIdValue = '123e4567-e89b-12d3-a456-426614174000';
        $testId = new TestId($testIdValue);

        //when
        //then
        $this->assertSame($testIdValue, $testId->value);
        //stringキャストで文字列として判定されること
        $this->assertSame($testIdValue, (string)$testId);
    }

    /**
     * @dataProvider invalidArgumentDataProvider
     * @throws DomainException
     */
    public function test_フォーマットに合致しない引数を指定して生成した場合に例外が発生すること(
        string $value,
    ): void {
        //given
        //when
        //then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('uuid is invalid. value:');
        new TestId($value);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidArgumentDataProvider(): array
    {
        return [
            'UUIDが1桁少ない' => ['123e4567-e89b-12d3-a456-42661417400'],
            'UUIDが1桁多い' => ['123e4567-e89b-12d3-a456-4266141740000'],
            '禁止文字が含まれる' => ['123e4567-e89b-12d3-a456-42661417400z'],
            '空文字' => [''],
        ];
    }

    /**
     * @throws DomainException
     */
    public function test_値が同じ場合にオブジェクトが同じと判定されること(): void
    {
        //given
        $value = '123e4567-e89b-12d3-a456-426614174000';
        $testId1 = new TestId($value);
        $testId2 = new TestId($value);

        //when
        //then
        $this->assertTrue($testId1->equals($testId2));
    }

    /**
     * @throws DomainException
     */
    public function test_値が異なる場合にオブジェクトが異なると判定されること(): void
    {
        //given
        $testId1 = new TestId('123e4567-e89b-12d3-a456-426614174000');
        $testId2 = new TestId('123e4567-e89b-12d3-a456-426614174001');

        //when
        //then
        $this->assertFalse($testId1->equals($testId2));
    }

    public function test_生成されたUUIDが作成順にalphabetの昇順になっていること(): void
    {
        //given
        $expectedIds = [
            new TestId(),
            new TestId(),
            new TestId(),
            new TestId(),
        ];
        //少し間隔を開けて生成
        usleep(200);
        $expectedIds[] = new TestId();
        usleep(100);
        $expectedIds[] = new TestId();

        //when
        $actualIds = collect($expectedIds)->sortBy(
            fn (TestId $id) => $id->value,
        )->all();

        //then
        $this->assertEquals($expectedIds, $actualIds);
    }
}
