<?php

declare(strict_types=1);

namespace Feature\Platform\Infrastructures\User;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Exceptions\IllegalUserException;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Hash;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class UserRepositoryTest extends TestCase
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
     * @throws DuplicateKeyException
     * @throws DomainException
     * @throws IllegalUserException
     */
    public function test_getAuthenticatedUserで認証済みユーザー情報を取得できること(): void
    {
        //given
        $inputUser = TestUserFactory::create();

        $university = TestUniversityFactory::create(
            $inputUser->universityId,
            new String255('テスト大学')
        );
        $this->universityRepository->insert($university);

        $faculty = TestFacultyFactory::create(
            $inputUser->facultyId,
            new String255('テスト学部'),
            $inputUser->universityId
        );
        $this->facultyRepository->insert($faculty);

        // ユーザーとトークンを作成
        $token = $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        // グローバルリクエストにBearerトークンを設定
        request()->headers->set('Authorization', 'Bearer ' . $token->token);

        //when
        $actualUser = $this->userRepository->getAuthenticatedUser();

        //then
        // パスワードはハッシュ値なのでHash::checkで検証
        $this->assertTrue(Hash::check($inputUser->password->value, $actualUser->password->value));
        // パスワード以外のプロパティを配列で比較
        $this->assertEquals(
            $this->userToArrayForTest($inputUser),
            $this->userToArrayForTest($actualUser)
        );
    }

    public function test_getAuthenticatedUserでトークンが存在しない場合nullが返ること(): void
    {
        //given
        // トークンを設定せずにテスト

        //when
        $result = $this->userRepository->getAuthenticatedUser();

        //then
        $this->assertNull($result);
    }

    public function test_getAuthenticatedUserで無効なトークンを指定した場合エラーが発生すること(): void
    {
        //given
        // 無効なトークンを設定
        request()->headers->set('Authorization', 'Bearer invalid_token');

        //when
        //then
        $this->expectException(IllegalUserException::class);
        $this->expectExceptionMessage('無効なトークンです。');
        $this->userRepository->getAuthenticatedUser();
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_userIdを指定して取得できること(): void
    {
        //given
        $inputUser = TestUserFactory::create();
        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);
        //when
        $actualUser = $this->userRepository->findById($inputUser->id);
        //then
        $this->assertTrue(\Hash::check($inputUser->password->value, $actualUser->password->value));
        $this->assertEquals(
            $this->userToArrayForTest($inputUser),
            $this->userToArrayForTest($actualUser)
        );
    }

    public function test_存在しないuserIdで検索するとnullが返ること(): void
    {
        //given
        //when
        $actualUser = $this->userRepository->findById(TestUserFactory::create()->id);
        //then
        $this->assertNull($actualUser);
    }

    public function test_insertで他のユーザーのLoginIdと重複する場合はエラーが発生すること(): void
    {
        //given
        $inputUser1 = TestUserFactory::create();
        $inputUser2 = TestUserFactory::create(id: TestUserFactory::create()->id, mailAddress: $inputUser1->mailAddress);
        $university1 = TestUniversityFactory::create($inputUser1->universityId, new String255('テスト大学1'));
        $this->universityRepository->insert($university1);
        $faculty1 = TestFacultyFactory::create($inputUser1->facultyId, new String255('テスト学部1'), $inputUser1->universityId);
        $this->facultyRepository->insert($faculty1);
        $university2 = TestUniversityFactory::create($inputUser2->universityId, new String255('テスト大学2'));
        $this->universityRepository->insert($university2);
        $faculty2 = TestFacultyFactory::create($inputUser2->facultyId, new String255('テスト学部2'), $inputUser2->universityId);
        $this->facultyRepository->insert($faculty2);
        $this->userRepository->insertWithLoginId($inputUser1, $inputUser1->mailAddress);
        //when
        //then
        $this->expectException(DuplicateKeyException::class);
        $this->expectExceptionMessage('loginIdが重複しています。');
        $this->userRepository->insertWithLoginId($inputUser2, $inputUser2->mailAddress);
    }

    public function test_createTokenで存在しないメールアドレスを指定した場合エラーが発生すること(): void
    {
        //given
        $inputUser = TestUserFactory::create();
        $nonExistentMailAddress = TestUserFactory::create()->mailAddress;

        //when
        //then
        $this->expectException(IllegalUserException::class);
        $this->expectExceptionMessage('認証済みユーザー情報が取得できませんでした。');
        $this->userRepository->createToken($nonExistentMailAddress, $inputUser->password);
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_createTokenで間違ったパスワードを指定した場合エラーが発生すること(): void
    {
        //given
        $inputUser = TestUserFactory::create();
        $wrongPassword = new String255('wrong_password');

        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        //when
        //then
        $this->expectException(IllegalUserException::class);
        $this->expectExceptionMessage('パスワードが違います。');
        $this->userRepository->createToken($inputUser->mailAddress, $wrongPassword);
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_createTokenで正しいメールアドレスとパスワードを指定した場合トークンが取得できること(): void
    {
        //given
        $inputUser = TestUserFactory::create();

        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        //when
        $token = $this->userRepository->createToken($inputUser->mailAddress, $inputUser->password);

        //then
        $this->assertNotNull($token);
        $this->assertNotEmpty($token->token);
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     * @throws IllegalUserException
     */
    public function test_createTokenで同一ユーザーの既存トークンが削除されて新しいトークンが発行されること(): void
    {
        //given
        $inputUser = TestUserFactory::create();

        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        // 1回目のトークン生成
        $firstToken = $this->userRepository->createToken($inputUser->mailAddress, $inputUser->password);

        // 1回目のトークンがデータベースに存在することを確認
        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'authenticate_token',
            'token' => hash('sha256', explode('|', $firstToken->token)[1])
        ]);

        //when
        // 2回目のトークン生成
        $secondToken = $this->userRepository->createToken($inputUser->mailAddress, $inputUser->password);

        //then
        // 2回目のトークンが生成されていることを確認
        $this->assertNotNull($secondToken);
        $this->assertNotEmpty($secondToken->token);
        $this->assertNotEquals($firstToken->token, $secondToken->token);

        // 1回目のトークンがデータベースから削除されていることを確認
        $this->assertDatabaseMissing('personal_access_tokens', [
            'name' => 'authenticate_token',
            'token' => hash('sha256', explode('|', $firstToken->token)[1])
        ]);

        // 2回目のトークンがデータベースに存在することを確認
        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'authenticate_token',
            'token' => hash('sha256', explode('|', $secondToken->token)[1])
        ]);

        // authenticate_tokenという名前のトークンが1つだけ存在することを確認
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_deleteTokenで認証済みユーザーのトークンを削除できること(): void
    {
        //given
        $inputUser = TestUserFactory::create();

        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);

        // ユーザーを作成してトークンを生成
        $token = $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        // トークンが存在することを確認
        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'authenticate_token',
            'token' => hash('sha256', explode('|', $token->token)[1])
        ]);

        //when
        $this->userRepository->deleteToken($token->token);

        //then
        // トークンが削除されたことを確認
        $this->assertDatabaseMissing('personal_access_tokens', [
            'name' => 'authenticate_token',
            'token' => hash('sha256', explode('|', $token->token)[1])
        ]);
    }

    public function test_deleteTokenでトークンが存在しない場合エラーが発生すること(): void
    {
        //given
        // トークンを設定せずにリクエストを作成

        //when
        //then
        $this->expectException(IllegalUserException::class);
        $this->expectExceptionMessage('認証トークンが見つかりません。');
        $this->userRepository->deleteToken();
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_deleteTokenで無効なトークンを指定した場合エラーが発生すること(): void
    {
        //given
        $inputUser = TestUserFactory::create();

        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        //when
        //then
        $this->expectException(IllegalUserException::class);
        $this->expectExceptionMessage('無効なトークンです。');
        $this->userRepository->deleteToken('invalid_token');
    }

    /**
     * Userドメインを配列化（パスワードなど���外）
     */
    private function userToArrayForTest($user): array
    {
        return [
            'id' => $user->id->value,
            'name' => $user->name->name,
            'postCode' => $user->postCode->postCode->value,
            'address' => $user->address->address->value,
            'mailAddress' => $user->mailAddress->mailAddress->value,
            'imageId' => $user->imageId?->value,
            'facultyId' => $user->facultyId->value,
            'universityId' => $user->universityId->value,
        ];
    }
}
