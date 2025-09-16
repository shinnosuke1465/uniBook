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

    public function test_必須項目が正しく入力されている場合バリデーションが成功する(): void
    {
        //given
        $inputData = self::createDefaultInput();
        $request = CreateImageRequest::create('', 'POST', $inputData);

        //when
        $actualPath = $request->getPath();
        $actualType = $request->getType();

        //then
        $this->assertEquals($inputData['path'], $actualPath->value);
        $this->assertEquals($inputData['type'], $actualType->value);
    }

    /**
     * @dataProvider invalidInputProvider
     */
    public function test_無効な入力でバリデーションエラーが発生すること(array $input, string $expectedError): void
    {
        //given
        $request = CreateImageRequest::create('', 'POST', $input);

        //when
        $validator = Validator::make($input, $request->rules(), $request->messages());

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
            'pathが空' => [
                self::createDefaultInput(path: ''),
                '画像パスは必ず指定してください。',
            ],
            'typeが空' => [
                self::createDefaultInput(type: ''),
                '画像タイプは必ず指定してください。',
            ],
        ];
    }

    /**
     * @return array{
     *     id: ?string,
     *     path: ?string,
     *     type: ?string,
     * }
     */
    private static function createDefaultInput(
        ?string $path = '/images/test.png',
        ?string $type = 'png',
    ): array {
        return compact('path', 'type');
    }
}

