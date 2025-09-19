<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Textbook;

use App\Platform\Domains\Textbook\Price;
use App\Exceptions\DomainException;
use Tests\TestCase;

class PriceTest extends TestCase
{
    public function test_正常な価格でインスタンスが生成できること(): void
    {
        //given
        $expectedValue = 1000;

        //when
        $price = new Price($expectedValue);

        //then
        $this->assertSame($expectedValue, $price->value);
    }

    public function test_0円でインスタンスが生成できること(): void
    {
        //given
        $expectedValue = 0;

        //when
        $price = new Price($expectedValue);

        //then
        $this->assertSame($expectedValue, $price->value);
    }

    public function test_負の価格で例外が発生すること(): void
    {
        //given
        $invalidValue = -1;

        //when
        //then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('価格は0以上である必要があります');
        new Price($invalidValue);
    }

    public function test_値が同じ場合にオブジェクトが同じと判定されること(): void
    {
        //given
        $testValue = 500;
        $price1 = new Price($testValue);
        $price2 = new Price($testValue);

        //when
        //then
        $this->assertTrue($price1->equals($price2));
    }

    public function test_値が異なる場合にオブジェクトが異なると判定されること(): void
    {
        //given
        $price1 = new Price(100);
        $price2 = new Price(200);

        //when
        //then
        $this->assertFalse($price1->equals($price2));
    }
}