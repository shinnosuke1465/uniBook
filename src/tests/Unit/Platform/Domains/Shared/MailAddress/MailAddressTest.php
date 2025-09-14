<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Shared\MailAddress;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use Tests\TestCase;

class MailAddressTest extends TestCase
{
    /**
     * @dataProvider validArgumentDataProvider
     * @throws DomainException
     */
    public function test_生成できること(string $string): void
    {
        //given
        $mailAddressString = new String255($string);

        //when
        $mailAddress = new MailAddress($mailAddressString);

        //then
        $this->assertInstanceOf(MailAddress::class, $mailAddress);
    }

    /**
     * @dataProvider invalidArgumentDataProvider
     * @thorws DomainException
     */
    public function test_不正な値のとき例外になること(string $string): void
    {
        //given
        $mailAddressString = new String255($string);

        //when
        //then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('メールアドレスが不正です。' . $mailAddressString->value);
        new MailAddress($mailAddressString);
    }

    /**
     * @return array<int, string[]>
     */
    public static function validArgumentDataProvider(): array
    {
        return [
            1 => ['sam-p.l.e_1@example.com'],
            2 => ['a-.-z@example.com'],
            3 => ['1234567@example.com'],
            4 => ['abcdefghijklmnopqrstuvwxyz123456789@example.com'],
        ];
    }

    public static function invalidArgumentDataProvider(): array
    {
        return [
            1 => ['infoexample.com'],
            2 => ['.localPart@domain.co.jp'],
            3 => ['localPart.@domain.co.jp'],
            4 => ['local..Part@domain.co.jp'],
            5 => ['local[Part@.domain.co.jp'],
            6 => ['local@Part@domain.co.jp'],
        ];
    }
}
