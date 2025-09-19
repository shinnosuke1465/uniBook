<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Textbook;

use App\Platform\Domains\Textbook\ConditionType;
use App\Exceptions\DomainException;
use Tests\TestCase;

class ConditionTypeTest extends TestCase
{
    public function test_全ての条件タイプが正常に作成できること(): void
    {
        //given
        $types = [
            'new' => ConditionType::NEW,
            'near_new' => ConditionType::NEAR_NEW,
            'no_damage' => ConditionType::NO_DAMAGE,
            'slight_damage' => ConditionType::SLIGHT_DAMAGE,
            'damage' => ConditionType::DAMAGE,
            'poor_condition' => ConditionType::POOR_CONDITION,
        ];

        //when
        //then
        foreach ($types as $value => $expectedType) {
            $actualType = ConditionType::create($value);
            $this->assertSame($expectedType, $actualType);
            $this->assertSame($value, $actualType->value);
        }
    }

    public function test_無効な条件タイプで例外が発生すること(): void
    {
        //given
        $invalidType = 'invalid_type';

        //when
        //then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Invalid condition type: ' . $invalidType);
        ConditionType::create($invalidType);
    }
}
