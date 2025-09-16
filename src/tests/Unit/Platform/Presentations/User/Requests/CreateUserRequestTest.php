<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Presentations\User\Requests;

use App\Exceptions\DomainException;
use App\Platform\Presentations\User\Requests\CreateUserRequest;
use Tests\CreatesApplication;
use Tests\TestCase;
use Validator;

class CreateUserRequestTest extends TestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
        app()->setLocale('ja');
    }

    public function test_必須項目が正し���入力されている場合バリデーションが成功する()
    {
        //given
        $inputData = self::createDefaultInput();
        $request = CreateUserRequest::create('', 'POST', $inputData);

        //when
        $validator = Validator::make($inputData, $request->rules());

        //then
        $this->assertTrue($validator->passes());
    }

    /**
     *
     * @dataProvider invalidInputProvider
     */
    public function test_無効な入力でバリデーションエラーが発生すること(array $input, string $expectedError): void
    {
        //given
        $request = CreateUserRequest::create('', 'POST', $input);

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
            'nameが空' => [
                self::createDefaultInput(name: ''),
                '名前は必ず指定してください。',
            ],
            'passwordが空' => [
                self::createDefaultInput(password: ''),
                'パスワードは必ず指定してください。',
            ],
            'passwordが8文字未満' => [
                self::createDefaultInput(password: 'pass12'),
                'パスワードは、8文字以上で指定してください。',
            ],
            'post_codeが空' => [
                self::createDefaultInput(post_code: ''),
                '郵便番号は必ず指定してください。',
            ],
            'post_codeが7文字以外' => [
                self::createDefaultInput(post_code: '123456'),
                '郵便番号は7桁で指定してください。',
            ],
            'addressが空' => [
                self::createDefaultInput(address: ''),
                '住所は必ず指定してください。',
            ],
            'mail_addressが空' => [
                self::createDefaultInput(mail_address: ''),
                'メールアドレスは必ず指定してください。',
            ],
            'mail_addressの形式が不正' => [
                self::createDefaultInput(mail_address: 'invalid-email'),
                'メールアドレスには、有効なメールアドレスを指定してください。',
            ],
            'faculty_idが空' => [
                self::createDefaultInput(faculty_id: ''),
                '学部IDは必ず指定してください。',
            ],
            'university_idが空' => [
                self::createDefaultInput(university_id: ''),
                '大学IDは必ず指定してください。',
            ],
        ];
    }

    /**
     * @throws DomainException
     */
    public function test_getメソッドが正しい値を取得すること(): void
    {
        //given
        $inputData = self::createDefaultInput();
        $request = CreateUserRequest::create('', 'POST', $inputData);

        //when
        $actualName = $request->getName();
        $actualPassword = $request->getUserPassword();
        $actualPostCode = $request->getPostCode();
        $actualAddress = $request->getAddress();
        $actualMailAddress = $request->getMailAddress();
        $actualImageId = $request->getImageId();
        $actualFacultyId = $request->getFacultyId();
        $actualUniversityId = $request->getUniversityId();

        //then
        $this->assertEquals($inputData['name'], $actualName->name);
        $this->assertEquals($inputData['password'], $actualPassword->value);
        $this->assertEquals($inputData['post_code'], $actualPostCode->postCode->value);
        $this->assertEquals($inputData['address'], $actualAddress->address->value);
        $this->assertEquals($inputData['mail_address'], $actualMailAddress->mailAddress->value);
        $this->assertNull($actualImageId);
        $this->assertEquals($inputData['faculty_id'], $actualFacultyId->value);
        $this->assertEquals($inputData['university_id'], $actualUniversityId->value);
    }

    /**
     * @return array{
     *     name: ?string,
     *     password: ?string,
     *     post_code: ?string,
     *     address: ?string,
     *     mail_address: ?string,
     *     image_id: ?string,
     *     faculty_id: ?string,
     *     university_id: ?string,
     * }
     */
    private static function createDefaultInput(
        string $name = 'テストユーザー',
        string $password = 'password123',
        string $postCode = '1234567',
        string $address = '東京都千代田区1-1-1',
        string $mailAddress = 'sample@example.com',
        ?string $imageId = null,
        ?string $facultyId = '1',
        ?string $universityId = '1',
    ): array {
        return [
            'name' => $name,
            'password' => $password,
            'post_code' => $postCode,
            'address' => $address,
            'mail_address' => $mailAddress,
            'image_id' => $imageId,
            'faculty_id' => $facultyId,
            'university_id' => $universityId,
        ];
    }

}
