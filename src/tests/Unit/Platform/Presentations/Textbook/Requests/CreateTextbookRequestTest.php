<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Presentations\Textbook\Requests;

use App\Exceptions\DomainException;
use App\Platform\Presentations\Textbook\Requests\CreateTextbookRequest;
use Tests\CreatesApplication;
use Tests\TestCase;
use Validator;

class CreateTextbookRequestTest extends TestCase
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
        $request = CreateTextbookRequest::create('', 'POST', $inputData);

        //when
        $actualName = $request->getName();
        $actualPrice = $request->getPrice();
        $actualDescription = $request->getDescription();
        $actualConditionType = $request->getConditionType();
        $actualUniversityId = $request->getUniversityId();
        $actualFacultyId = $request->getFacultyId();
        $actualImageIdList = $request->getImageIdList();

        //then
        $this->assertEquals($inputData['name'], $actualName->value);
        $this->assertEquals($inputData['price'], $actualPrice->value);
        $this->assertEquals($inputData['description'], $actualDescription->value);
        $this->assertEquals($inputData['condition_type'], $actualConditionType->value);
        $this->assertEquals($inputData['university_id'], $actualUniversityId->value);
        $this->assertEquals($inputData['faculty_id'], $actualFacultyId->value);
        $this->assertCount(count($inputData['image_ids']), $actualImageIdList->toArray());
    }

    /**
     * @dataProvider invalidInputProvider
     */
    public function test_無効な入力でバリデーションエラーが発生すること(array $input, string $expectedError): void
    {
        //given
        $request = CreateTextbookRequest::create('', 'POST', $input);

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
            'priceが空' => [
                self::createDefaultInput(price: null),
                '価格は必ず指定してください。',
            ],
            'priceが負の値' => [
                self::createDefaultInput(price: -1),
                '価格には、0以上の数字を指定してください。',
            ],
            'condition_typeが空' => [
                self::createDefaultInput(condition_type: ''),
                'condition typeは必ず指定してください。',
            ],
            'university_idが空' => [
                self::createDefaultInput(university_id: ''),
                '大学IDは必ず指定してください。',
            ],
            'faculty_idが空' => [
                self::createDefaultInput(faculty_id: ''),
                '学部IDは必ず指定してください。',
            ],
        ];
    }

    /**
     * @return array{
     *     name: ?string,
     *     price: ?int,
     *     description: ?string,
     *     condition_type: ?string,
     *     university_id: ?string,
     *     faculty_id: ?string,
     *     image_ids: array,
     * }
     */
    private static function createDefaultInput(
        ?string $name = 'テスト教科書',
        ?int $price = 1000,
        ?string $description = 'テスト説明',
        ?string $condition_type = 'new',
        ?string $university_id = '00000000-0000-0000-0000-000000000001',
        ?string $faculty_id = '00000000-0000-0000-0000-000000000002',
        array $image_ids = ['00000000-0000-0000-0000-000000000003'],
    ): array {
        return compact(
            'name',
            'price',
            'description',
            'condition_type',
            'university_id',
            'faculty_id',
            'image_ids',
        );
    }
}
