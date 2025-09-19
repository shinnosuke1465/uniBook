<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Textbook;

use App\Platform\Domains\Textbook\TextbookId;
use Tests\TestCase;

class TextbookIdTest extends TestCase
{
    public function test_インスタンスが生成できること(): void
    {
        //given
        //when
        $textbookId = new TextbookId();

        //then
        $this->assertInstanceOf(TextbookId::class, $textbookId);
        $this->assertNotEmpty($textbookId->value);
    }

    public function test_異なるインスタンスは異なるIDを持つこと(): void
    {
        //given
        //when
        $textbookId1 = new TextbookId();
        $textbookId2 = new TextbookId();

        //then
        $this->assertNotEquals($textbookId1->value, $textbookId2->value);
    }
}