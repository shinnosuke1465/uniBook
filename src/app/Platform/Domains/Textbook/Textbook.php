<?php

declare(strict_types=1);

namespace App\Platform\Domains\Textbook;

use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Image\ImageIdList;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\University\UniversityId;

readonly class Textbook
{
    public function __construct(
        public TextbookId $id,
        public String255 $name,
        public Price $price,
        public Text $description,
        public ?ImageIdList $imageIdList,
        public UniversityId $universityId,
        public FacultyId $facultyId,
        public ConditionType $conditionType,
    ) {
    }

    public static function create(
        String255 $name,
        Price $price,
        Text $description,
        ?ImageIdList $imageIds,
        UniversityId $universityId,
        FacultyId $facultyId,
        ConditionType $conditionType,
    ): self {
        return new self(
            new TextbookId(),
            $name,
            $price,
            $description,
            $imageIds,
            $universityId,
            $facultyId,
            $conditionType,
        );
    }

    public function update(
        String255 $name,
        Price $price,
        Text $description,
        ImageIdList $imageIds,
        ConditionType $conditionType,
    ): self {
        return new self(
            $this->id,
            $name,
            $price,
            $description,
            $imageIds,
            $this->universityId,
            $this->facultyId,
            $conditionType,
        );
    }

    public function delete(): self
    {
        return new self(
            $this->id,
            $this->name,
            $this->price,
            $this->description,
            $this->imageIdList,
            $this->universityId,
            $this->facultyId,
            $this->conditionType,
        );
    }

}
