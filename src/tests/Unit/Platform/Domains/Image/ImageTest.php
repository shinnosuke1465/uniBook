<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Image;

use App\Platform\Domains\Image\Image;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Shared\String\String255;
use Tests\TestCase;

class ImageTest extends TestCase
{
    public function test_インスタンスが生成できること(): void
    {
        //given
        $expectedId = new ImageId();
        $expectedPath = new String255('images/sample');
        $expectedType = new String255('png');
        //when
        $actualImage = new Image(
            id: $expectedId,
            path: $expectedPath,
            type: $expectedType,
        );
        //then
        $this->assertEquals($expectedId, $actualImage->id);
        $this->assertEquals($expectedPath, $actualImage->path);
        $this->assertEquals($expectedType, $actualImage->type);
    }

    public function test_staticで生成できること(): void
    {
        //given
        $expectedPath = new String255('images/sample');
        $expectedType = new String255('png');
        //when
        $actualImage = Image::create(
            path: $expectedPath,
            type: $expectedType,
        );
        //then
        $this->assertEquals($expectedPath, $actualImage->path);
        $this->assertEquals($expectedType, $actualImage->type);
    }
}
