<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Presentations\University\Requests;

use App\Exceptions\DomainException;
use App\Platform\Presentations\University\Requests\CreateUniversityRequest;
use Tests\CreatesApplication;
use Tests\TestCase;
use Validator;

class CreateUniversityRequestTest extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
        app()->setLocale('ja');
    }

    /**
     * @throws DomainException
     */
    public function test_必須項目が正しく入力されている場合バリデーションが成功する()
    {
        //given
        $inputData = self::createDefaultInput();
        $request = CreateUniversityRequest::create('', 'POST', $inputData);

        //when
        $actualName = $request->getName();

        //then
        $this->assertEquals($inputData['name'], $actualName->value);
    }

    /**
     * @dataProvider invalidInputProvider
     */
    public function test_無効な入力でバリデーションエラーが発生すること(array $input, string $expectedError): void
    {
        //given
        $request = CreateUniversityRequest::create('', 'POST', $input);

        //when
        $validator = Validator::make($input, $request->rules(), $request->messages());

        //then
        $this->assertFalse($validator->passes());
        $this->assertStringContainsString($expectedError, $validator->errors()->first());
    }

    /**
     * @return array<string, mixed>
     */
    public static function invalidInputProvider()
    {
        return [
            'nameが空' => [
                self::createDefaultInput(name: ''),
                '名前は必ず指定してください。',
            ],
        ];
    }

    /**
     * @return array{
     *     name: ?string,
     * }
     */
    private static function createDefaultInput(
        ?string $name = 'テスト大学',
    ): array {
        return compact('name');
    }
}

