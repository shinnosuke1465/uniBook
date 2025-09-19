<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Presentations\Textbook\Requests;

use App\Platform\Presentations\Textbook\Requests\GetTextbookRequest;
use Tests\CreatesApplication;
use Tests\TestCase;
use Validator;

class GetTextbookRequestTest extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
        app()->setLocale('ja');
    }

    public function test_必須項目が正しく入力されている場合バリデーションが成功する()
    {
        //given
        $inputData = self::createDefaultInput();
        $request = GetTextbookRequest::create('', 'GET', $inputData);

        //when
        $validator = Validator::make($inputData, $request->rules(), $request->messages());

        //then
        $this->assertTrue($validator->passes());
    }

    /**
     * @return array{}
     */
    private static function createDefaultInput(): array
    {
        return [];
    }
}