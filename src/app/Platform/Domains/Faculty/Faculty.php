<?php

declare(strict_types=1);

namespace App\Platform\Domains\Faculty;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\University;
use App\Platform\Domains\University\UniversityId;

readonly class Faculty
{
    /**
     * @throws DomainException
     */
    public function __construct(
        public FacultyId $id,
        public String255 $name,
        public UniversityId $universityId,
    ) {
        if (is_null($this->universityId)) {
            throw new DomainException('学部は必ず1つの大学に属する必要があります。');
        }
    }

    /**
     * @throws DomainException
     */
    public static function create(
        String255 $name,
        UniversityId $universityId,
    ): self {
        return new self(
            new FacultyId(),
            $name,
            $universityId,
        );
    }

}
