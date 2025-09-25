<?php

namespace App\Platform\UseCases\Textbook;

use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Image\ImageIdList;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\Textbook\ConditionType;
use App\Platform\Domains\Textbook\Price;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Domains\User\UserId;

interface CreateTextbookActionValuesInterface
{
    public function getName(): String255;
    public function getPrice(): Price;
    public function getDescription(): Text;
    public function getConditionType(): ConditionType;
    public function getUniversityId(): UniversityId;
    public function getFacultyId(): FacultyId;
    public function getImageIdList(): ImageIdList;
}