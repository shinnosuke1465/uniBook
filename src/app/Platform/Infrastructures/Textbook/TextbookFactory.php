<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\Textbook;

use App\Exceptions\DomainException;
use App\Models\Textbook as TextbookDB;
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

readonly class TextbookFactory
{
    /**
     * @throws DomainException
     */
    public static function create(TextbookDB $textbookDB): Textbook
    {
        // 画像IDリストを作成
        $imageIds = $textbookDB->imageIds->map(
            fn (TextbookDB $image) => new ImageId($image->id)
        )->all();

        return new Textbook(
            id: new TextbookId($textbookDB->id),
            name: new String255($textbookDB->name),
            price: new Price($textbookDB->price),
            description: new Text($textbookDB->description ?? ''),
            imageIdList: new ImageIdList($imageIds),
            universityId: new UniversityId($textbookDB->university_id),
            facultyId: new FacultyId($textbookDB->faculty_id),
            conditionType: ConditionType::create($textbookDB->condition_type),
        );
    }
}
