<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\User;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class CreateUserApiTest extends TestCase
{
    use DatabaseTransactions;

    private UserRepository $userRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->facultyRepository = new FacultyRepository();
        $this->universityRepository = new UniversityRepository();
    }

    /**
     * @throws DomainException
     */
    public function test_正しいデータでユーザーが作成できること(): void
    {
        // given
        // まずUniversityとFacultyを作成してから、それらのIDでUserを作成
        $university = TestUniversityFactory::create(name: new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create(name: new String255('テスト学部'), universityId: $university->id);
        $this->facultyRepository->insert($faculty);

        $inputUser = TestUserFactory::create(
            facultyId: $faculty->id,
            universityId: $university->id
        );

        $url = route('users.create');
        $requestData = [
            'name' => $inputUser->name->name,
            'password' => $inputUser->password->value,
            'post_code' => $inputUser->postCode->postCode->value,
            'address' => $inputUser->address->address->value,
            'mail_address' => $inputUser->mailAddress->mailAddress->value,
            'image_id' => $inputUser->imageId?->value,
            'faculty_id' => $inputUser->facultyId->value,
            'university_id' => $inputUser->universityId->value,
        ];

        // when
        $response = $this->postJson($url, $requestData);

        // then
        $response->assertSuccessful();
        $token = $response->json('token');
        $this->assertNotNull($token);
    }

    public function test_必須項目が欠けている場合エラーが返ること(): void
    {
        // given
        $url = route('users.create');
        $requestData = [
            'name' => 'テストユーザー',
            // email が欠けている
            'password' => 'password123',
        ];

        // when
        $response = $this->postJson($url, $requestData);

        // then
        $response->assertUnprocessable();
    }

    /**
     * @throws DomainException
     */
    public function test_重複するメールアドレスでエラーが返ること(): void
    {
        // given
        // まずUniversityとFacultyを作成
        $university = TestUniversityFactory::create(name: new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create(name: new String255('テスト学部'), universityId: $university->id);
        $this->facultyRepository->insert($faculty);

        $inputUser = TestUserFactory::create(
            facultyId: $faculty->id,
            universityId: $university->id
        );
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        $url = route('users.create');
        $requestData = [
            'name' => 'テストユーザー2',
            'mail_address' => $inputUser->mailAddress->mailAddress->value, // 重複するメールアドレス
            'password' => 'password123',
            'post_code' => '123-4567',
            'address' => 'テスト住所',
            'faculty_id' => $inputUser->facultyId->value,
            'university_id' => $inputUser->universityId->value,
        ];

        // when
        $response = $this->postJson($url, $requestData);

        // then
        $response->assertUnprocessable();
    }
}
