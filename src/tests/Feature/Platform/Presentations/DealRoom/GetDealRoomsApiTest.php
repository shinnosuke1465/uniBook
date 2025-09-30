<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\DealRoom;

use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Image\ImageIdList;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\Textbook\ConditionType;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Domains\User\UserId;
use App\Platform\Domains\User\UserIdList;
use App\Platform\Infrastructures\Deal\DealRepository;
use App\Platform\Infrastructures\DealRoom\DealRoomRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Deal\TestDealFactory;
use Tests\Unit\Platform\Domains\DealRoom\TestDealRoomFactory;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;
use Tests\Feature\Api\ApiPreLoginTrait;

class GetDealRoomsApiTest extends TestCase
{
    use DatabaseTransactions, ApiPreLoginTrait;

    private DealRoomRepository $dealRoomRepository;
    private DealRepository $dealRepository;
    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dealRoomRepository = new DealRoomRepository();
        $this->dealRepository = new DealRepository();
        $this->userRepository = new UserRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
    }

    public function test_認証済みユーザーが参加している取引ルーム一覧を取得できること(): void
    {
        // given - 認証ユーザー（seller）を準備
        $sellerUser = $this->prepareUserWithFacultyAndUniversity();
        // トークンを生成
        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

        // buyer用のユーザーを作成
        $buyerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('buyer@test.com')));
        $buyerUniversity = TestUniversityFactory::create($buyerUser->universityId, new String255('買い手大学'));
        $this->universityRepository->insert($buyerUniversity);
        $buyerFaculty = TestFacultyFactory::create($buyerUser->facultyId, new String255('買い手学部'), $buyerUser->universityId);
        $this->facultyRepository->insert($buyerFaculty);
        $this->userRepository->insertWithLoginId($buyerUser, $buyerUser->mailAddress);

        // textbookを作成
        $textbook = TestTextbookFactory::create(
            name: new String255('テスト教科書'),
            description: new Text('これはテスト用の教科書です'),
            imageIdList: new ImageIdList([]),
            universityId: new UniversityId($sellerUser->university_id),
            facultyId: new FacultyId($sellerUser->faculty_id),
            conditionType: ConditionType::NEW
        );
        $this->textbookRepository->insert($textbook);

        // dealを作成
        $deal = TestDealFactory::create(
            seller: new Seller(new UserId($sellerUser->id)),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook->id,
            dealStatus: DealStatus::Purchased
        );
        $this->dealRepository->insert($deal);

        // dealRoomを作成
        $userIds = new UserIdList([new UserId($sellerUser->id), $buyerUser->id]);
        $dealRoom = TestDealRoomFactory::create(
            dealId: $deal->id,
            userIds: $userIds
        );
        $this->dealRoomRepository->insert($dealRoom);

        // when
        $url = route('dealrooms.index');
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertOk()
            ->assertJsonStructure([
                'deal_rooms' => [
                    '*' => [
                        'id',
                        'deal' => [
                            'id',
                            'seller_info' => [
                                'id',
                                'nickname',
                                'profile_image_url',
                            ],
                            'textbook' => [
                                'name',
                                'image_url',
                            ],
                        ],
                        'created_at',
                    ]
                ]
            ])
            ->assertJsonCount(1, 'deal_rooms');

        $responseData = $response->json();
        $this->assertEquals($dealRoom->id, $responseData['deal_rooms'][0]['id']);
        $this->assertEquals($deal->id->value, $responseData['deal_rooms'][0]['deal']['id']);
        $this->assertEquals($sellerUser->id, $responseData['deal_rooms'][0]['deal']['seller_info']['id']);
        $this->assertEquals($sellerUser->name, $responseData['deal_rooms'][0]['deal']['seller_info']['nickname']);
        $this->assertEquals('テスト教科書', $responseData['deal_rooms'][0]['deal']['textbook']['name']);
    }

    public function test_認証済みユーザーが複数の取引ルームを作成日時の降順で取得できること(): void
    {
        // given - 認証ユーザー（seller）を準備
        $sellerUser = $this->prepareUserWithFacultyAndUniversity();
        // トークンを生成
        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

        // buyer用のユーザーを作成
        $buyerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('buyer@test.com')));
        $buyerUniversity = TestUniversityFactory::create($buyerUser->universityId, new String255('買い手大学'));
        $this->universityRepository->insert($buyerUniversity);
        $buyerFaculty = TestFacultyFactory::create($buyerUser->facultyId, new String255('買い手学部'), $buyerUser->universityId);
        $this->facultyRepository->insert($buyerFaculty);
        $this->userRepository->insertWithLoginId($buyerUser, $buyerUser->mailAddress);

        // 1つ目の取引ルームを作成
        $textbook1 = TestTextbookFactory::create(
            name: new String255('教科書1'),
            universityId: new UniversityId($sellerUser->university_id),
            facultyId: new FacultyId($sellerUser->faculty_id)
        );
        $this->textbookRepository->insert($textbook1);
        $deal1 = TestDealFactory::create(
            seller: new Seller(new UserId($sellerUser->id)),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook1->id,
            dealStatus: DealStatus::Purchased
        );
        $this->dealRepository->insert($deal1);
        $userIds = new UserIdList([new UserId($sellerUser->id), $buyerUser->id]);
        $dealRoom1 = TestDealRoomFactory::create(dealId: $deal1->id, userIds: $userIds);
        $this->dealRoomRepository->insert($dealRoom1);

        // 時間差を作る
        sleep(1);

        // 2つ目の取引ルームを作成
        $textbook2 = TestTextbookFactory::create(
            name: new String255('教科書2'),
            universityId: new UniversityId($sellerUser->university_id),
            facultyId: new FacultyId($sellerUser->faculty_id)
        );
        $this->textbookRepository->insert($textbook2);
        $deal2 = TestDealFactory::create(
            seller: new Seller(new UserId($sellerUser->id)),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook2->id,
            dealStatus: DealStatus::Purchased
        );
        $this->dealRepository->insert($deal2);
        $dealRoom2 = TestDealRoomFactory::create(dealId: $deal2->id, userIds: $userIds);
        $this->dealRoomRepository->insert($dealRoom2);

        // when
        $url = route('dealrooms.index');
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertOk()
            ->assertJsonCount(2, 'deal_rooms');

        $responseData = $response->json();
        // 新しい方が先に来ることを確認（降順）
        $this->assertEquals($dealRoom2->id->value, $responseData['deal_rooms'][0]['id']);
        $this->assertEquals($dealRoom1->id->value, $responseData['deal_rooms'][1]['id']);
        $this->assertEquals('教科書2', $responseData['deal_rooms'][0]['deal']['textbook']['name']);
        $this->assertEquals('教科書1', $responseData['deal_rooms'][1]['deal']['textbook']['name']);
    }


    public function test_認証済みユーザーが取引ルームを持っていない場合空の配列が返ること(): void
    {
        // given - 認証ユーザーを準備（取引ルームなし）
        $this->prepareUserWithFacultyAndUniversity();
        // トークンを生成
        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

        // when
        $url = route('dealrooms.index');
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertOk()
            ->assertJsonStructure([
                'deal_rooms' => []
            ])
            ->assertJsonCount(0, 'deal_rooms');
    }
}
