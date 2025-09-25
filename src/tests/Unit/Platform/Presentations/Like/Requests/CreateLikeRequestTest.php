<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Presentations\Like\Requests;

use App\Platform\Presentations\Like\Requests\CreateLikeRequest;
use Tests\CreatesApplication;
use Tests\TestCase;
use Validator;

class CreateLikeRequestTest extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
        app()->setLocale('ja');
    }

    public function test_空のリクエストでもバリデーションが成功する()
    {
        //given
        $inputData = [];
        $request = CreateLikeRequest::create('', 'POST', $inputData);

        //when
        $validator = Validator::make($inputData, $request->rules());

        //then
        $this->assertTrue($validator->passes());
    }
}
