<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Shared\Address;

use App\Platform\Domains\Shared\Address\Address;
use App\Platform\Domains\Shared\String\String255;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function test_オブジェクトが生成できること(): void
    {
       //given
        $expectAddress = new String255('愛知県名古屋市中区錦3丁目15-13');

        //when
        $actualAddress = new Address($expectAddress);

        //then
        $this->assertInstanceOf(Address::class, $actualAddress);
        $this->assertEquals($expectAddress, $actualAddress->address);
    }

    public function test_staticメソッドから作成できること(): void
    {
        //given
        $expectAddress = '愛知県名古屋市中区錦3丁目15-13';

        //when
        $actualAddress = Address::create($expectAddress);

        //then
        $this->assertEquals($expectAddress, $actualAddress->address->value);
    }
}
