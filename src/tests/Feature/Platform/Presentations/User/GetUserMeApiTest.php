<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\User;

use App\Exceptions\DomainException;
use Tests\TestCase;

class GetUserMeApiTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function test_API定義通りのレスポンスを得られること(): void
    {
        $url = route('users.me');

        //when
        $response = $this->get($url);

        //then
        //レスポンス確認
        $response->assertOk()
            ->assertJsonStructure(
                [
                    'id',
                    'name',
                    'PostCode',
                    'address',
                    'email',
                    'imageId',
                    'facultyId',
                    'universityId',
                ]
            );
    }
}
