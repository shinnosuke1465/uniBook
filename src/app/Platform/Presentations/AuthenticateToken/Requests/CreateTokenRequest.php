<?php

declare(strict_types=1);

namespace App\Platform\Presentations\AuthenticateToken\Requests;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Authenticate\CreateTokenActionValuesInterface;

class CreateTokenRequest extends BaseRequest implements CreateTokenActionValuesInterface
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:8'
            ],
        ];
    }

    /**
     * @throws DomainException
     */
    public function getEmail(): MailAddress
    {
        return new MailAddress(new String255($this->input('mail')));
    }

    /**
     * @throws DomainException
     */
    public function getUserPassword(): String255
    {
        return new String255($this->input('password'));
    }
}
