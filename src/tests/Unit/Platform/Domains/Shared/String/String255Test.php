<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Shared\String;

use App\Platform\Domains\Shared\String\String255;
use App\Exceptions\DomainException;
use Tests\TestCase;

class String255Test extends TestCase
{
    /**
     * @throws DomainException
     */
    public function test_インスタンスが生成できること(): void
    {
        //given
        $expectString = str_repeat('a', 255);
        $expectString255 = new String255($expectString);

        //when
        //then
        $this->assertSame($expectString, $expectString255->value);
    }

    public function test_空文字でインスタンスが生成できること(): void
    {
        //given
        $expectString = '';
        $expectString255 = new String255($expectString);

        //when
        //then
        $this->assertSame($expectString, $expectString255->value);
    }

    public function test_256文字で例外が発生すること(): void
    {
        //given
        $expectString = str_repeat('a', 256);

        //when
        //then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('255文字以内の文字列を指定してください。');
        new String255($expectString);
    }

    public function test_値が同じ場合にオブジェクトが同じと判定されること(): void
    {
        //given
        $testValue = 'test';
        $string1 = new String255($testValue);
        $string2 = new String255($testValue);

        //when
        //then
        $this->assertTrue($string1->equals($string2));
    }

    public function test_値が異なる場合にオブジェクトが異なると判定されること(): void
    {
        //given
        $string1 = new String255('test1');
        $string2 = new String255('test2');

        //when
        //then
        $this->assertFalse($string1->equals($string2));
    }
}
