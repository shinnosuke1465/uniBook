<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\DealRoom;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Exceptions\NotFoundException;
use App\Models\DealMessage;
use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\User\UserId;
use App\Platform\Domains\User\UserIdList;
use App\Platform\Infrastructures\Deal\DealRepository;
use App\Platform\Infrastructures\DealRoom\DealRoomRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Deal\TestDealFactory;
use Tests\Unit\Platform\Domains\DealRoom\TestDealRoomFactory;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class GetDealRoomApiTest extends TestCase
{
    use DatabaseTransactions, ApiPreLoginTrait;

    private UserRepository $userRepository;
    private DealRoomRepository $dealRoomRepository;
    private DealRepository $dealRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->dealRoomRepository = new DealRoomRepository();
        $this->dealRepository = new DealRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     * @throws NotFoundException
     * @throws AuthenticationException
     */
    public function test_認証済みユーザーが参加している取引ルームの詳細を取得できること(): void
    {
        // given
        // 売り手ユーザーを作成
        $sellerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('seller@test.com')));
        $sellerUniversity = TestUniversityFactory::create($sellerUser->universityId, new String255('売り手大学'));
        $this->universityRepository->insert($sellerUniversity);
        $sellerFaculty = TestFacultyFactory::create($sellerUser->facultyId, new String255('売り手学部'), $sellerUser->universityId);
        $this->facultyRepository->insert($sellerFaculty);
        $this->userRepository->insertWithLoginId($sellerUser, $sellerUser->mailAddress);

        // 買い手ユーザーを作成（ログイン用）
        $buyerUser = $this->prepareUserWithFacultyAndUniversity();
        $buyerToken = $this->userRepository->createToken(
            new MailAddress(new String255('test@example.com')),
            new String255('password12345')
        );

        // 教科書を作成
        $textbook = TestTextbookFactory::create(
            universityId: $sellerUser->universityId,
            facultyId: $sellerUser->facultyId
        );
        $this->textbookRepository->insert($textbook);

        // 取引を作成
        $deal = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer(new UserId($buyerUser->id)),
            textbookId: $textbook->id,
            dealStatus: DealStatus::Purchased
        );
        $this->dealRepository->insert($deal);

        // 取引ルームを作成
        $userIds = new UserIdList([$sellerUser->id, new UserId($buyerUser->id)]);
        $dealRoom = TestDealRoomFactory::create(
            dealId: $deal->id,
            userIds: $userIds
        );
        $this->dealRoomRepository->insert($dealRoom);

        // メッセージを追加
        DealMessage::create([
            'id' => \Illuminate\Support\Str::orderedUuid(),
            'user_id' => $buyerUser->id,
            'deal_room_id' => $dealRoom->id->value,
            'message' => 'こんにちは、商品について質問があります。',
        ]);
        DealMessage::create([
            'id' => \Illuminate\Support\Str::orderedUuid(),
            'user_id' => $sellerUser->id->value,
            'deal_room_id' => $dealRoom->id->value,
            'message' => 'はい、何でしょうか？',
        ]);

        $url = route('dealrooms.show', ['dealRoomId' => $dealRoom->id->value]);

        // when
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $buyerToken->token,
        ]);

        // then
        $response->assertOk()
            ->assertJsonStructure([
                'deal_room' => [
                    'id',
                    'deal' => [
                        'id',
                        'status',
                        'textbook' => [
                            'id',
                            'name',
                            'description',
                            'price',
                            'image_urls',
                        ],
                        'seller_info' => [
                            'id',
                            'name',
                            'profile_image_url',
                        ],
                        'buyer_info' => [
                            'id',
                            'name',
                            'postal_code',
                            'address',
                            'profile_image_url',
                        ],
                        'deal_events' => [
                            '*' => [
                                'id',
                                'actor_type',
                                'event_type',
                                'created_at',
                            ]
                        ],
                    ],
                    'messages' => [
                        '*' => [
                            'id',
                            'message',
                            'created_at',
                            'user' => [
                                'id',
                                'name',
                                'profile_image_url',
                            ],
                        ],
                    ],
                ],
            ])
            ->assertJson([
                'deal_room' => [
                    'id' => $dealRoom->id->value,
                    'deal' => [
                        'id' => $deal->id->value,
                        'status' => 'Purchased',
                        'textbook' => [
                            'id' => $textbook->id->value,
                        ],
                        'seller_info' => [
                            'id' => $sellerUser->id->value,
                        ],
                        'buyer_info' => [
                            'id' => $buyerUser->id,
                        ],
                    ],
                ],
            ]);

        // メッセージが2件あることを確認
        $this->assertCount(2, $response->json('deal_room.messages'));
        $this->assertEquals('こんにちは、商品について質問があります。', $response->json('deal_room.messages.0.message'));
        $this->assertEquals('はい、何でしょうか？', $response->json('deal_room.messages.1.message'));
    }

    /**
     * @throws AuthenticationException
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_存在しない取引ルームを取得しようとした場合404エラーが返ること(): void
    {
        // given
        $this->prepareUserWithFacultyAndUniversity();
        $token = $this->userRepository->createToken(
            new MailAddress(new String255('test@example.com')),
            new String255('password12345')
        );

        $nonExistentDealRoomId = '11111111-1111-1111-1111-111111111111';
        $url = route('dealrooms.show', ['dealRoomId' => $nonExistentDealRoomId]);

        // when
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertNotFound();
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     * @throws AuthenticationException
     * @throws NotFoundException
     */
    public function test_参加していない取引ルームを取得しようとした場合エラーが返ること(): void
    {
        // given
        // 売り手ユーザーを作成
        $sellerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('seller@test.com')));
        $sellerUniversity = TestUniversityFactory::create($sellerUser->universityId, new String255('売り手大学'));
        $this->universityRepository->insert($sellerUniversity);
        $sellerFaculty = TestFacultyFactory::create($sellerUser->facultyId, new String255('売り手学部'), $sellerUser->universityId);
        $this->facultyRepository->insert($sellerFaculty);
        $this->userRepository->insertWithLoginId($sellerUser, $sellerUser->mailAddress);

        // 買い手ユーザーを作成
        $buyerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('buyer@test.com')));
        $buyerUniversity = TestUniversityFactory::create($buyerUser->universityId, new String255('買い手大学'));
        $this->universityRepository->insert($buyerUniversity);
        $buyerFaculty = TestFacultyFactory::create($buyerUser->facultyId, new String255('買い手学部'), $buyerUser->universityId);
        $this->facultyRepository->insert($buyerFaculty);
        $this->userRepository->insertWithLoginId($buyerUser, $buyerUser->mailAddress);

        // 第三者ユーザーを作成（ログイン用、取引ルームに参加していない）
        $this->prepareUserWithFacultyAndUniversity();
        $otherUserToken = $this->userRepository->createToken(
            new MailAddress(new String255('test@example.com')),
            new String255('password12345')
        );

        // 教科書を作成
        $textbook = TestTextbookFactory::create(
            universityId: $sellerUser->universityId,
            facultyId: $sellerUser->facultyId
        );
        $this->textbookRepository->insert($textbook);

        // 取引を作成
        $deal = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook->id,
            dealStatus: DealStatus::Purchased
        );
        $this->dealRepository->insert($deal);

        // 取引ルームを作成（売り手と買い手のみ）
        $userIds = new UserIdList([$sellerUser->id, $buyerUser->id]);
        $dealRoom = TestDealRoomFactory::create(
            dealId: $deal->id,
            userIds: $userIds
        );
        $this->dealRoomRepository->insert($dealRoom);

        $url = route('dealrooms.show', ['dealRoomId' => $dealRoom->id->value]);

        // when
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $otherUserToken->token,
        ]);

        // then
        $response->assertStatus(403); // AuthorizationExceptionは403になる
        $response->assertJson([
            'message' => 'この取引ルームにアクセスする権限がありません。'
        ]);
    }
}
