<?php

declare(strict_types=1);

namespace Feature\Platform\Infrastructures\Like;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Like\LikeRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Like\TestLikeFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class LikeRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private LikeRepository $likeRepository;
    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->likeRepository = new LikeRepository();
        $this->userRepository = new UserRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
    }

    /**
     * @throws DuplicateKeyException
     * @throws DomainException
     */
    public function test_insertでいいねを登録できること(): void
    {
        //given
        // 必要な関連データを作成
        $inputUser = TestUserFactory::create();
        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        $inputTextbook = TestTextbookFactory::create(
            universityId: $inputUser->universityId,
            facultyId: $inputUser->facultyId
        );
        $this->textbookRepository->insert($inputTextbook);

        // Likeを作成（既存のuser_idとtextbook_idを使用）
        $inputLike = TestLikeFactory::create(
            userId: $inputUser->id,
            textbookId: $inputTextbook->id
        );

        //when
        $this->likeRepository->insert($inputLike);

        //then
        $this->assertDatabaseHas('likes', [
            'id' => $inputLike->id->value,
            'user_id' => $inputLike->userId->value,
            'textbook_id' => $inputLike->textbookId->value,
        ]);
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_insertで同じIDのいいねを登録した場合DuplicateKeyExceptionが発生すること(): void
    {
        //given
        // 必要な関連データを作成
        $inputUser = TestUserFactory::create();
        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        $inputTextbook = TestTextbookFactory::create(
            universityId: $inputUser->universityId,
            facultyId: $inputUser->facultyId
        );
        $this->textbookRepository->insert($inputTextbook);

        // 同じIDのLikeを作成
        $inputLike1 = TestLikeFactory::create(
            userId: $inputUser->id,
            textbookId: $inputTextbook->id
        );
        $inputLike2 = TestLikeFactory::create(
            id: $inputLike1->id,
            userId: $inputUser->id,
            textbookId: $inputTextbook->id
        );
        $this->likeRepository->insert($inputLike1);

        //when
        //then
        $this->expectException(DuplicateKeyException::class);
        $this->expectExceptionMessage('いいねが重複しています。');
        $this->likeRepository->insert($inputLike2);
    }

    /**
     * @throws DuplicateKeyException
     * @throws DomainException
     */
//    public function test_deleteでいいねを削除できること(): void
//    {
//        //given
//        // 必要な関連データを作成
//        $inputUser = TestUserFactory::create();
//        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
//        $this->universityRepository->insert($university);
//        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
//        $this->facultyRepository->insert($faculty);
//        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);
//
//        $inputTextbook = TestTextbookFactory::create(
//            universityId: $inputUser->universityId,
//            facultyId: $inputUser->facultyId
//        );
//        $this->textbookRepository->insert($inputTextbook);
//
//        // Likeを作成・挿入
//        $inputLike = TestLikeFactory::create(
//            userId: $inputUser->id,
//            textbookId: $inputTextbook->id
//        );
//        $this->likeRepository->insert($inputLike);
//
//        // 削除前に存在することを確認
//        $this->assertDatabaseHas('likes', [
//            'user_id' => $inputLike->userId->value,
//            'textbook_id' => $inputLike->textbookId->value,
//        ]);
//
//        //when
//        $this->likeRepository->delete($inputUser->id, $inputTextbook->id);
//
//        //then
//        $this->assertDatabaseMissing('likes', [
//            'user_id' => $inputLike->userId->value,
//            'textbook_id' => $inputLike->textbookId->value,
//        ]);
//    }

    /**
     * @throws DuplicateKeyException
     * @throws DomainException
     */
    public function test_findByUserIdAndTextbookIdで存在するいいねを取得できること(): void
    {
        //given
        // 必要な関連データを作成
        $inputUser = TestUserFactory::create();
        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        $inputTextbook = TestTextbookFactory::create(
            universityId: $inputUser->universityId,
            facultyId: $inputUser->facultyId
        );
        $this->textbookRepository->insert($inputTextbook);

        // Likeを作成・挿入
        $inputLike = TestLikeFactory::create(
            userId: $inputUser->id,
            textbookId: $inputTextbook->id
        );
        $this->likeRepository->insert($inputLike);

        //when
        $result = $this->likeRepository->findByUserIdAndTextbookId($inputUser->id, $inputTextbook->id);

        //then
        $this->assertNotNull($result);
        $this->assertEquals($inputLike->id->value, $result->id->value);
        $this->assertEquals($inputLike->userId->value, $result->userId->value);
        $this->assertEquals($inputLike->textbookId->value, $result->textbookId->value);
    }

    /**
     * @throws DomainException
     */
    public function test_findByUserIdAndTextbookIdで存在しないいいねの場合nullが返されること(): void
    {
        //given
        // 必要な関連データを作成
        $inputUser = TestUserFactory::create();
        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        $inputTextbook = TestTextbookFactory::create(
            universityId: $inputUser->universityId,
            facultyId: $inputUser->facultyId
        );
        $this->textbookRepository->insert($inputTextbook);

        // Likeは挿入しない

        //when
        $result = $this->likeRepository->findByUserIdAndTextbookId($inputUser->id, $inputTextbook->id);

        //then
        $this->assertNull($result);
    }

    /**
     * @throws DomainException
     */
    public function test_deleteで存在しないいいねを削除してもエラーにならないこと(): void
    {
        //given
        // 必要な関連データを作成
        $inputUser = TestUserFactory::create();
        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        $inputTextbook = TestTextbookFactory::create(
            universityId: $inputUser->universityId,
            facultyId: $inputUser->facultyId
        );
        $this->textbookRepository->insert($inputTextbook);

        // Likeは挿入しない（存在しない状態）

        //when・then（例外が発生しないことを確認）
        $this->likeRepository->delete($inputUser->id, $inputTextbook->id);

        // データベースに該当レコードが存在しないことを確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $inputUser->id->value,
            'textbook_id' => $inputTextbook->id->value,
        ]);
    }
}
