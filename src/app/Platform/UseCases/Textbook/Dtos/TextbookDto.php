<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Textbook\Dtos;

use App\Platform\Domains\Textbook\Textbook;
use App\Models\Textbook as TextbookDB;

readonly class TextbookDto
{
    public function __construct(
        public string $id,
        public string $name,
        public int $price,
        public string $description,
        public array $imageIds,
        public string $universityId,
        public string $facultyId,
        public string $conditionType,
    ) {
    }

    public static function create(
        Textbook $textbook,
    ): self {
        return new self(
            $textbook->id->value,
            $textbook->name->value,
            $textbook->price->value,
            $textbook->description->value,
            $textbook->imageIdList->toArray(),
            $textbook->universityId->value,
            $textbook->facultyId->value,
            $textbook->conditionType->value,
        );
    }
}
