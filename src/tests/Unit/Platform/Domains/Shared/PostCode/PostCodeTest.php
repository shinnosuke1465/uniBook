<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Shared\PostCode;

use App\Platform\Domains\Shared\PostCode\PostCode;
use App\Platform\Domains\Shared\String\String255;
use App\Exceptions\DomainException;
use Tests\TestCase;

class PostCodeTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function test_オブジェクトが生成できること(): void
    {
       //given
        $expectValue = '1234567';

        //when
        $actualPostCode = new PostCode(new String255($expectValue));

        //then
        $this->assertEquals($expectValue, $actualPostCode->postCode->value);
    }

    /**
     * @throws DomainException
     */
    public function test_staticで生成できること(): void
    {
       //given
        $expectValue = '1234567';

        //when
        $actualPostCode = PostCode::create($expectValue);

        //then
        $this->assertEquals($expectValue, $actualPostCode->postCode->value);
    }

    /**
     * @throws DomainException
     */
    public function test_getValueで値が取得できること(): void
    {
        $expectValue = '1234567';
        $postCode = new PostCode(new String255($expectValue));
        $this->assertEquals($expectValue, $postCode->getValue());
    }

    /**
     * @dataProvider validPostCodeProvider
     */
    public function test_有効な郵便番号で生成できること(string $value): void
    {
        $postCode = new PostCode(new String255($value));
        $this->assertEquals($value, $postCode->postCode->value);
    }

    /**
     * @dataProvider invalidPostCodeProvider
     */
    public function test_無効な郵便番号で例外が発生すること(string $value): void
    {
        $this->expectException(DomainException::class);
        new PostCode(new String255($value));
    }

    public static function validPostCodeProvider(): array
    {
        return [
            '7桁数字' => ['1234567'],
            '先頭0の7桁' => ['0123456'],
        ];
    }

    public static function invalidPostCodeProvider(): array
    {
        return [
            '6桁' => ['123456'],
            '8桁' => ['12345678'],
            'ハイフンあり' => ['123-4567'],
            '英字含む' => ['1234a67'],
            '全角数字' => ['１２３４５６７'],
            '空文字' => [''],
        ];
    }
}
