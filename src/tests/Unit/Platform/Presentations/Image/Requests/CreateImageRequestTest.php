<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Presentations\Image\Requests;

use App\Platform\Presentations\Image\Requests\CreateImageRequest;
use Tests\CreatesApplication;
use Tests\TestCase;
use Validator;

class CreateImageRequestTest extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
        app()->setLocale('ja');
    }

    /**
     * @dataProvider invalidInputProvider
     */
    public function test_無効な入力でバリデーションエラーが発生すること(array $files, string $expectedError): void
    {
        //given
        $request = CreateImageRequest::create('', 'POST', [], [], $files);

        //when
        $validator = Validator::make($files, $request->rules(), $request->messages());

        //then
        $this->assertFalse($validator->passes());
        $this->assertStringContainsString($expectedError, $validator->errors()->first());
    }

    /**
     * @return array<string, mixed>
     */
    public static function invalidInputProvider(): array
    {
        return [
            'imageが空' => [
                [],
                'imageは必ず指定してください。',
            ],
        ];
    }
}

