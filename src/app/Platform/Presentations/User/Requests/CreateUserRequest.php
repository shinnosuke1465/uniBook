<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Requests;

use App\Exceptions\DomainException;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Shared\Address\Address;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\Name\Name;
use App\Platform\Domains\Shared\PostCode\PostCode;
use App\Platform\Domains\Shared\String\NonEmptyString255;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\User\CreateUserValuesInterface;

class CreateUserRequest extends BaseRequest implements CreateUserValuesInterface
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string'
            ],
            'password' => [
                'required',
                'min:8',
            ],
            'post_code' => [
                'required',
                'digits:7'
            ],
            'address' => [
                'required',
                'string',
            ],
            'mail_address' => [
                'required',
                'string',
                'email',
            ],
            'image_id' => [
                'nullable',
                'string',
            ],
            'university_id' => [
                'required',
                'string',
            ],
            'faculty_id' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'digits:7' => '郵便番号は7桁で入力してください。'
        ];
    }

    public function getName(): Name
    {
        return new Name($this->input('name'));
    }

    /**
     * @throws DomainException
     */
    public function getUserPassword(): String255
    {
        return new String255($this->input('password'));
    }

    /**
     * @throws DomainException
     */
    public function getPostCode(): PostCode
    {
        return new PostCode(new String255($this->input('post_code')));
    }

    /**
     * @throws DomainException
     */
    public function getAddress(): Address
    {
        return new Address(new String255($this->input('address')));
    }

    /**
     * @throws DomainException
     */
    public function getMailAddress(): MailAddress
    {
        return new MailAddress(new String255($this->input('mail_address')));
    }

    /**
     * @throws DomainException
     */
    public function getImageId(): ?ImageId
    {
        $imageId = $this->input('image_id');
        if (empty($imageId)) {
            return null;
        }
        return new ImageId($imageId);
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
    public function getUniversityId(): UniversityId
    {
        return new UniversityId($this->input('university_id'));
    }
}
