<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\University;

 use App\Platform\Domains\Shared\String\String255;
 use App\Platform\Domains\University\University;
 use App\Platform\Domains\University\UniversityId;

 class TestUniversityFactory
{
    public static function create(
        UniversityId $id = new UniversityId(),
        String255 $name = new String255('テスト大学'),
    ): University
    {
        return new University(
            $id,
            $name,
        );
    }
}
