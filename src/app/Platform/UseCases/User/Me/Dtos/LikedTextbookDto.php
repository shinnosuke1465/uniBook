<?php

declare(strict_types=1);

namespace App\Platform\UseCases\User\Me\Dtos;

readonly class LikedTextbookDto
{
    public function __construct(
        public string $id,
        public string $name,
        public int $price,
        public string $description,
        public array $imageUrls,
        public string $universityName,
        public string $facultyName,
        public string $conditionType,
        public ?array $deal,
        public array $comments,
        public bool $isLiked,
    ) {
    }
}