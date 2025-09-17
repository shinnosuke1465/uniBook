<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Presentations\Faculty\Requests;

use App\Platform\Presentations\Faculty\Requests\CreateFacultyRequest;
use Tests\CreatesApplication;
use Tests\TestCase;
use Validator;

class CreateFacultyRequestTest extends TestCase
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
        $request = CreateFacultyRequest::create('', 'POST', $inputData);

        //when
        $actualName = $request->getName();
        $actualUniversityId = $request->getUniversityId();

        //then
        $this->assertEquals($inputData['name'], $actualName->value);
        $this->assertEquals($inputData['university_id'], $actualUniversityId->value);

    }

    /**
     * @dataProvider invalidInputProvider
     */
    public function test_無効な入力でバリデーションエラーが発生すること(array $input, string $expectedError): void
    {
        //given
        $request = CreateFacultyRequest::create('', 'POST', $input);

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
            'university_idが空' => [
                self::createDefaultInput(university_id: ''),
                '大学IDは必ず指定してください。',
            ],
        ];
    }

    /**
     * @return array{
     *     name: ?string,
     *     university_id: ?string,
     * }
     */
    private static function createDefaultInput(
        ?string $name = 'テスト学部',
        ?string $university_id = '00000000-0000-0000-0000-000000000001',
    ): array {
        return compact(
            'name',
            'university_id',
        );
    }
}
