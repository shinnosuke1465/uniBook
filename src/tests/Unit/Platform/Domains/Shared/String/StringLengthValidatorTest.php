<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Shared\String;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\String\StringLengthValidator;
use Tests\TestCase;

class StringLengthValidatorTest extends TestCase
{
    /**
     * @dataProvider stringProvider
     * @throws DomainException
     */
    public function test_正しいパラメータを渡すとチェック��パスすること(
        int $length,
        string $text,
    ): void {
        //given

        //when
        StringLengthValidator::check($length, $text);

        //then
        $this->assertTrue(true);
    }

    /**
     * @return array<string, mixed>
     */
    public static function stringProvider(): array
    {
        return [
            '1文字の文字列を作成できること(半角文字)' => [
                'length' => 1,
                'text' => 'a',
            ],
            '1文字の文字列を作成できること(全角文字)' => [
                'length' => 1,
                'text' => 'あ',
            ],
            '255文字の文字列を作成できること(半角文字)' => [
                'length' => 255,
                'text' => str_repeat('a', 255),
            ],
            '255文字の文字列を作成できること(全角文字)' => [
                'length' => 255,
                'text' => str_repeat('あ', 255),
            ],
            '空文字の文字列を作成できること' => [
                'length' => 255,
                'text' => '',
            ],
        ];
    }

    /**
     * @dataProvider stringProviderForException
     * @param array{length: int, text: string} $input
     * @throws DomainException
     */
    public function test_正しくないパラメータを渡すと例外となること(
        array $input,
        string $expectMessage,
    ): void {
        //given

        //when
        //then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage($expectMessage);
        StringLengthValidator::check($input['length'], $input['text']);
    }

    /**
     * @return array<string, mixed>
     */
    public static function stringProviderForException(): array
    {
        return [
            '長さが0以下の場合に例外が発生すること' => [
                'input' => [
                    'length' => 0,
                    'text' => 'a',
                ],
                'expectMessage' => '文字列の最大長は1以上を指定してください。',
            ],
            '1文字の枠に2文字以上入れないこと (半角文字)' => [
                'input' => [
                    'length' => 1,
                    'text' => 'aa',
                ],
                'expectMessage' => '1文字以内の文字列を指定してください。',
            ],
            '1文字の枠に2文字以上入れないこと (全角文字)' => [
                'input' => [
                    'length' => 1,
                    'text' => 'ああ',
                ],
                'expectMessage' => '1文字以内の文字列を指定してください。',
            ],
            '255文字の枠に256文字以上入れないこと (半角文字)' => [
                'input' => [
                    'length' => 255,
                    'text' => str_repeat('a', 256),
                ],
                'expectMessage' => '255文字以内の文字列を指定してください。',
            ],
            '255文字の枠に256文字以上入れないこと (全角文字)' => [
                'input' => [
                    'length' => 255,
                    'text' => str_repeat('あ', 256),
                ],
                'expectMessage' => '255文字以内の文字列を指定してください。',
            ],
        ];
    }
}
