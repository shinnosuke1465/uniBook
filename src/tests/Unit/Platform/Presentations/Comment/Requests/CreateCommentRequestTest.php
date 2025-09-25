<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Presentations\Comment\Requests;

use App\Platform\Presentations\Comment\Requests\CreateCommentRequest;
use Tests\CreatesApplication;
use Tests\TestCase;
use Validator;

class CreateCommentRequestTest extends TestCase
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
        $request = CreateCommentRequest::create('', 'POST', $inputData);

        //when
        $validator = Validator::make($inputData, $request->rules());

        //then
        $this->assertTrue($validator->passes());
    }

    /**
     * @dataProvider invalidInputProvider
     */
    public function test_無効な入力でバリデーションエラーが発生すること(array $input, string $expectedError): void
    {
        //given
        $request = CreateCommentRequest::create('', 'POST', $input);

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
            'textが空' => [
                self::createDefaultInput(text: ''),
                'textは必ず指定してください。',
            ],
            'textが文字列でない' => [
                self::createDefaultInput(text: 123),
                'textは文字列を指定してください。',
            ],
        ];
    }

    public function test_getメソッドが正しい値を取得すること(): void
    {
        //given
        $inputData = self::createDefaultInput();
        $request = CreateCommentRequest::create('', 'POST', $inputData);

        //when
        $actualText = $request->getText();

        //then
        $this->assertEquals($inputData['text'], $actualText->value);
    }

    /**
     * @return array{text: mixed}
     */
    private static function createDefaultInput(
        mixed $text = 'これはテストコメントです。',
    ): array {
        return compact('text');
    }
}
