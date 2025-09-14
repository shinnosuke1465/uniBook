<?php

namespace Tests\Unit\Platform\Domains\Shared\Address;

use App\Platform\Domains\Shared\Address\Address;
use App\Platform\Domains\Shared\String\String255;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function test_オブジェクトが生成できること(): void
    {
       //given
        $expectName = new String255('愛知県名古屋市中区錦3丁目15-13');

        //when
        $actualAddress = new Address($expectName);

        //then
        $this->assertInstanceOf(Address::class, $actualAddress);
        $this->assertEquals($expectName, $actualAddress->name);
    }

    public function test_staticメソッドから作成できること(): void
    {
        //given
        $expectName = '愛知県名古屋市中区錦3丁目15-13';

        //when
        $actualAddress = Address::create($expectName);

        //then
        $this->assertEquals($expectName, $actualAddress->name->value);
    }
}
