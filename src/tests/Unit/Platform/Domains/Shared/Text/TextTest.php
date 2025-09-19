<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Shared\Text;

use App\Platform\Domains\Shared\Text\Text;
use Tests\TestCase;
use App\Exceptions\DomainException;

class TextTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function test_インスタンスが生成できること(): void
    {
        // given
        $expectString = str_repeat('あ', 1000);
        $expectText = new Text($expectString);

        // when
        // then
        $this->assertSame($expectString, $expectText->value);
    }

    public function test_空文字でインスタンスが生成できること(): void
    {
        // given
        $expectString = '';
        $expectText = new Text($expectString);

        // when
        // then
        $this->assertSame($expectString, $expectText->value);
    }

    /**
     * @dataProvider dataProviderCharacterCount
     *
     * @throws DomainException
     */
    public function test_1001文字以上が指定された場合例外となること(
        string $char
    ): void {
        // given
        $expectString = str_repeat($char, 1001);

        //when
        //then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('1000文字以内の文字列を指定してください。');
        new Text($expectString);
    }

    /**
     * @return array<string, array<string>>
     */
    public static function dataProviderCharacterCount(): array
    {
        return [
            '全角の場合' => [
                'char' => 'あ',
            ],
            '半角の場合' => [
                'char' => 'a',
            ],
        ];
    }

    /**
     * @throws DomainException
     */
    public function test_値が同じ場合にオブジェクトが同じと判定されること(): void
    {
        // given
        $testValue = 'テスト文字列';
        $testString1 = new Text($testValue);
        $testString2 = new Text($testValue);

        // when
        // then
        $this->assertTrue($testString1->equals($testString2));
    }

    public function test_値が異なる場合にオブジェクトが異なると判定されること(): void
    {
        // given
        $testString1 = new Text('テスト文字列1');
        $testString2 = new Text('テスト文字列2');

        // when
        // then
        $this->assertFalse($testString1->equals($testString2));
    }
}
