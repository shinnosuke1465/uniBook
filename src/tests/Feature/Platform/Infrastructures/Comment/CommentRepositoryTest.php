<?php

declare(strict_types=1);

namespace Feature\Platform\Infrastructures\Comment;

use App\Exceptions\DuplicateKeyException;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Infrastructures\Comment\CommentRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Comment\TestCommentFactory;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class CommentRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private CommentRepository $commentRepository;
    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commentRepository = new CommentRepository();
        $this->userRepository = new UserRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
    }

    public function test_insertでコメントを登録できること(): void
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

        // Commentを作成（既存のuser_idとtextbook_idを使用）
        $inputComment = TestCommentFactory::create(
            userId: $inputUser->id,
            textbookId: $inputTextbook->id
        );

        //when
        $this->commentRepository->insert($inputComment);

        //then
        $this->assertDatabaseHas('comments', [
            'id' => $inputComment->id->value,
            'text' => $inputComment->text->value,
            'user_id' => $inputComment->userId->value,
            'textbook_id' => $inputComment->textbookId->value,
        ]);
    }

    public function test_insertで同じIDのコメントを登録した場合DuplicateKeyExceptionが発生すること(): void
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

        // 同じIDのCommentを作成
        $inputComment1 = TestCommentFactory::create(
            userId: $inputUser->id,
            textbookId: $inputTextbook->id
        );
        $inputComment2 = TestCommentFactory::create(
            id: $inputComment1->id,
            userId: $inputUser->id,
            textbookId: $inputTextbook->id
        );
        $this->commentRepository->insert($inputComment1);

        //when
        //then
        $this->expectException(DuplicateKeyException::class);
        $this->expectExceptionMessage('コメントが重複しています。');
        $this->commentRepository->insert($inputComment2);
    }
}