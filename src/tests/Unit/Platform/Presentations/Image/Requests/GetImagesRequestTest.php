<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Presentations\Image\Requests;

use App\Platform\Presentations\Image\Requests\GetImagesRequest;
use App\Platform\Domains\Image\ImageIdList;
use Tests\CreatesApplication;
use Tests\TestCase;
use Validator;

class GetImagesRequestTest extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
        app()->setLocale('ja');
    }

    public function test_必須項目が正しく入力されている場合バリデーションが成功しImageIdListが取得できる(): void
    {
        //given
        $inputData = self::createDefaultInput();
        $request = GetImagesRequest::create('', 'POST', $inputData);

        //when
        $actualImageIdList = $request->getImageIdList();

        //then
        $this->assertInstanceOf(ImageIdList::class, $actualImageIdList);
        $this->assertCount(count($inputData['ids']), $actualImageIdList->toArray());
    }

    /**
     * @dataProvider invalidInputProvider
     */
    public function test_無効な入力でバリデーションエラーが発生すること(array $input, string $expectedError): void
    {
        //given
        $request = GetImagesRequest::create('', 'POST', $input);

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
            'image_idsが空' => [
                self::createDefaultInput(ids: []),
                'idsは必ず指定してください。',
            ],
            'image_idsの要素が空' => [
                self::createDefaultInput(ids: ['']),
                'ids.0は必ず指定してください。',
            ],
        ];
    }

    /**
     * @return array{
     *     image_ids: array,
     * }
     */
    private static function createDefaultInput(
        array $ids = ['00000000-0000-0000-0000-000000000001', '00000000-0000-0000-0000-000000000002'],
    ): array {
        return compact('ids');
    }
}

