<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\User\Me;

use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Domains\User\UserId;
use App\Platform\Infrastructures\Comment\CommentRepository;
use App\Platform\Infrastructures\Deal\DealRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Like\LikeRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Comment\TestCommentFactory;
use Tests\Unit\Platform\Domains\Deal\TestDealFactory;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Like\TestLikeFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class GetLikedTextbooksApiTest extends TestCase
{
    use DatabaseTransactions, ApiPreLoginTrait;

    private LikeRepository $likeRepository;
    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;
    private DealRepository $dealRepository;
    private CommentRepository $commentRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->likeRepository = new LikeRepository();
        $this->userRepository = new UserRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
        $this->dealRepository = new DealRepository();
        $this->commentRepository = new CommentRepository();
    }

    public function test_認証済みユーザーがいいねした教科書一覧を取得できること(): void
    {
        // given
        $user = $this->prepareUserWithFacultyAndUniversity();

        // トークンを生成
        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

        // 出品者用のユーザーを作成
        $sellerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('seller@test.com')));
        $sellerUniversity = TestUniversityFactory::create($sellerUser->universityId, new String255('販売者大学'));
        $this->universityRepository->insert($sellerUniversity);
        $sellerFaculty = TestFacultyFactory::create($sellerUser->facultyId, new String255('販売者学部'), $sellerUser->universityId);
        $this->facultyRepository->insert($sellerFaculty);
        $this->userRepository->insertWithLoginId($sellerUser, $sellerUser->mailAddress);

        // コメント投稿者用のユーザーを作成
        $commenterUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('commenter@test.com')));
        $commenterUniversity = TestUniversityFactory::create($commenterUser->universityId, new String255('コメント者大学'));
        $this->universityRepository->insert($commenterUniversity);
        $commenterFaculty = TestFacultyFactory::create($commenterUser->facultyId, new String255('コメント者学部'), $commenterUser->universityId);
        $this->facultyRepository->insert($commenterFaculty);
        $this->userRepository->insertWithLoginId($commenterUser, $commenterUser->mailAddress);

        // 教科書1を作成
        $textbook1 = TestTextbookFactory::create(
            name: new String255('いいねした教科書1'),
            universityId: new UniversityId($user->university_id),
            facultyId: new FacultyId($user->faculty_id)
        );
        $this->textbookRepository->insert($textbook1);

        // 教科書2を作成
        $textbook2 = TestTextbookFactory::create(
            name: new String255('いいねした教科書2'),
            universityId: new UniversityId($user->university_id),
            facultyId: new FacultyId($user->faculty_id)
        );
        $this->textbookRepository->insert($textbook2);

        // 教科書3を作成（いいねしない）
        $textbook3 = TestTextbookFactory::create(
            name: new String255('いいねしていない教科書'),
            universityId: new UniversityId($user->university_id),
            facultyId: new FacultyId($user->faculty_id)
        );
        $this->textbookRepository->insert($textbook3);

        // Deal情報を作成（教科書1）
        $deal1 = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer(new UserId($user->id)),
            textbookId: $textbook1->id,
            dealStatus: DealStatus::create('Listing')
        );
        $this->dealRepository->insert($deal1);

        // コメントを作成（教科書1）
        $comment1 = TestCommentFactory::create(
            userId: $commenterUser->id,
            textbookId: $textbook1->id,
            text: new Text('教科書1へのコメント')
        );
        $this->commentRepository->insert($comment1);

        // いいねを作成（教科書1と教科書2）
        $like1 = TestLikeFactory::create(
            userId: new UserId($user->id),
            textbookId: $textbook1->id
        );
        $this->likeRepository->insert($like1);

        $like2 = TestLikeFactory::create(
            userId: new UserId($user->id),
            textbookId: $textbook2->id
        );
        $this->likeRepository->insert($like2);

        // when
        $url = route('me.likes');
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertOk()
            ->assertJsonStructure([
                'textbooks' => [
                    '*' => [
                        'id',
                        'name',
                        'price',
                        'description',
                        'image_url',
                        'image_urls',
                        'university_name',
                        'faculty_name',
                        'condition_type',
                        'deal',
                        'comments' => [
                            '*' => [
                                'id',
                                'text',
                                'created_at',
                                'user' => [
                                    'id',
                                    'name',
                                    'profile_image_url',
                                ]
                            ]
                        ],
                        'is_liked',
                    ]
                ]
            ]);

        $responseData = $response->json();
        $this->assertCount(2, $responseData['textbooks']); // いいねした2つの教科書

        // いいね状態の確認
        foreach ($responseData['textbooks'] as $textbook) {
            $this->assertTrue($textbook['is_liked']);
        }

        // 教科書1の詳細確認
        $likedTextbook1 = collect($responseData['textbooks'])->firstWhere('name', 'いいねした教科書1');
        $this->assertNotNull($likedTextbook1);
        $this->assertEquals('テスト大学', $likedTextbook1['university_name']);
        $this->assertEquals('テスト学部', $likedTextbook1['faculty_name']);

        // Deal情報の確認
        $this->assertNotNull($likedTextbook1['deal']);
        $this->assertTrue($likedTextbook1['deal']['is_purchasable']);
        $this->assertEquals($sellerUser->name->name, $likedTextbook1['deal']['seller_info']['nickname']);

        // コメント情報の確認
        $this->assertCount(1, $likedTextbook1['comments']);
        $this->assertEquals('教科書1へのコメント', $likedTextbook1['comments'][0]['text']);
        $this->assertEquals($commenterUser->name->name, $likedTextbook1['comments'][0]['user']['name']);
    }

    public function test_いいねした教科書がない場合は空の配列を返すこと(): void
    {
        // given
        $user = $this->prepareUserWithFacultyAndUniversity();

        // トークンを生成
        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

        // 教科書を作成（いいねはしない）
        $textbook = TestTextbookFactory::create(
            name: new String255('いいねしていない教科書'),
            universityId: new UniversityId($user->university_id),
            facultyId: new FacultyId($user->faculty_id)
        );
        $this->textbookRepository->insert($textbook);

        // when
        $url = route('me.likes');
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertOk()
            ->assertJson([
                'textbooks' => []
            ]);
    }


    public function test_他のユーザーがいいねした教科書は含まれないこと(): void
    {
        // given
        $user = $this->prepareUserWithFacultyAndUniversity();

        // トークンを生成
        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

        // 他のユーザーを作成
        $otherUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('other@test.com')));
        $otherUniversity = TestUniversityFactory::create($otherUser->universityId, new String255('他大学'));
        $this->universityRepository->insert($otherUniversity);
        $otherFaculty = TestFacultyFactory::create($otherUser->facultyId, new String255('他学部'), $otherUser->universityId);
        $this->facultyRepository->insert($otherFaculty);
        $this->userRepository->insertWithLoginId($otherUser, $otherUser->mailAddress);

        // 教科書を作成
        $textbook1 = TestTextbookFactory::create(
            name: new String255('自分がいいねした教科書'),
            universityId: new UniversityId($user->university_id),
            facultyId: new FacultyId($user->faculty_id)
        );
        $this->textbookRepository->insert($textbook1);

        $textbook2 = TestTextbookFactory::create(
            name: new String255('他人がいいねした教科書'),
            universityId: new UniversityId($user->university_id),
            facultyId: new FacultyId($user->faculty_id)
        );
        $this->textbookRepository->insert($textbook2);

        // 自分のいいねを作成
        $myLike = TestLikeFactory::create(
            userId: new UserId($user->id),
            textbookId: $textbook1->id
        );
        $this->likeRepository->insert($myLike);

        // 他のユーザーのいいねを作成
        $otherLike = TestLikeFactory::create(
            userId: $otherUser->id,
            textbookId: $textbook2->id
        );
        $this->likeRepository->insert($otherLike);

        // when
        $url = route('me.likes');
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertOk();
        $responseData = $response->json();
        $this->assertCount(1, $responseData['textbooks']); // 自分がいいねした1つのみ
        $this->assertEquals('自分がいいねした教科書', $responseData['textbooks'][0]['name']);
    }

    public function test_いいねした教科書が新しい順に並ぶこと(): void
    {
        // given
        $user = $this->prepareUserWithFacultyAndUniversity();

        // トークンを生成
        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

        // 教科書を作成
        $textbook1 = TestTextbookFactory::create(
            name: new String255('最初にいいねした教科書'),
            universityId: new UniversityId($user->university_id),
            facultyId: new FacultyId($user->faculty_id)
        );
        $this->textbookRepository->insert($textbook1);

        $textbook2 = TestTextbookFactory::create(
            name: new String255('最後にいいねした教科書'),
            universityId: new UniversityId($user->university_id),
            facultyId: new FacultyId($user->faculty_id)
        );
        $this->textbookRepository->insert($textbook2);

        // いいねを時間差で作成
        $like1 = TestLikeFactory::create(
            userId: new UserId($user->id),
            textbookId: $textbook1->id
        );
        $this->likeRepository->insert($like1);

        // 少し時間を置く
        sleep(1);

        $like2 = TestLikeFactory::create(
            userId: new UserId($user->id),
            textbookId: $textbook2->id
        );
        $this->likeRepository->insert($like2);

        // when
        $url = route('me.likes');
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertOk();
        $responseData = $response->json();
        $this->assertCount(2, $responseData['textbooks']);

        // 新しい順なので、最後にいいねした教科書が先頭に来る
        $this->assertEquals('最後にいいねした教科書', $responseData['textbooks'][0]['name']);
        $this->assertEquals('最初にいいねした教科書', $responseData['textbooks'][1]['name']);
    }
}
