<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Textbook;

use App\Exceptions\DomainException;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Image\ImageIdList;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\Textbook\ConditionType;
use App\Platform\Domains\Textbook\Price;
use App\Platform\Domains\Textbook\Textbook;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\University\UniversityId;

class TestTextbookFactory
{
    /**
     * @throws DomainException
     */
    public static function create(
        TextbookId $id = new TextbookId(),
        String255 $name = new String255('テスト教科書'),
        Price $price = new Price(1000),
        Text $description = new Text('テスト説明'),
        ?ImageIdList $imageIdList = null,
        UniversityId $universityId = new UniversityId(),
        FacultyId $facultyId = new FacultyId(),
        ConditionType $conditionType = ConditionType::NEW,
    ): Textbook {
        return new Textbook(
            $id,
            $name,
            $price,
            $description,
            $imageIdList ?? new ImageIdList([]),
            $universityId,
            $facultyId,
            $conditionType,
        );
    }
}
