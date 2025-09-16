<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Authenticate\Requests;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Authenticate\LoginActionValuesInterface;

class LoginRequest extends BaseRequest implements LoginActionValuesInterface
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'mail_address' => [
                'required',
                'email',
                'max:255'
            ],
            'password' => [
                'required',
                'min:8',
                'max:255'
            ],
        ];
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
    public function getLoginPassword(): String255
    {
        return new String255($this->input('password'));
    }
}
