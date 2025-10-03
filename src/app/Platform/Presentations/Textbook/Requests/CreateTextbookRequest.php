<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Textbook\Requests;

use App\Exceptions\DomainException;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Image\ImageIdList;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\Textbook\ConditionType;
use App\Platform\Domains\Textbook\Price;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Textbook\CreateTextbookActionValuesInterface;

class CreateTextbookRequest extends BaseRequest implements CreateTextbookActionValuesInterface
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'price' => [
                'required',
                'integer',
                'min:0',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'condition_type' => [
                'required',
                'string',
                'in:new,near_new,no_damage,slight_damage,damage,poor_condition',
            ],
            'university_id' => [
                'required',
                'string',
            ],
            'faculty_id' => [
                'required',
                'string',
            ],
            'image_ids' => [
                'nullable',
                'array',
            ],
            'image_ids.*' => [
                'string',
            ],
        ];
    }

    /**
     * @throws DomainException
     */
    public function getName(): String255
    {
        return new String255($this->input('name'));
    }

    /**
     * @throws DomainException
     */
    public function getPrice(): Price
    {
        return new Price($this->input('price'));
    }

    public function getDescription(): Text
    {
        return new Text($this->input('description') ?? '');
    }

    /**
     * @throws DomainException
     */
    public function getConditionType(): ConditionType
    {
        return ConditionType::create($this->input('condition_type'));
    }

    /**
     * @throws DomainException
     */
    public function getUniversityId(): UniversityId
    {
        return new UniversityId($this->input('university_id'));
    }

    /**
     * @throws DomainException
     */
    public function getFacultyId(): FacultyId
    {
        return new FacultyId($this->input('faculty_id'));
    }

    /**
     * @throws DomainException
     */
    public function getImageIdList(): ImageIdList
    {
        $imageIds = $this->input('image_ids', []);
        $imageIdObjects = array_map(
            fn($imageId) => new ImageId($imageId),
            $imageIds
        );
        return new ImageIdList($imageIdObjects);
    }
}
