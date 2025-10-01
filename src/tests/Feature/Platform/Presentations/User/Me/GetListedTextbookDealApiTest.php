<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\User\Me;

use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Domains\User\UserId;
use App\Platform\Infrastructures\Deal\DealRepository;
use App\Platform\Infrastructures\DealEvent\DealEventRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Deal\TestDealFactory;
use Tests\Unit\Platform\Domains\DealEvent\TestDealEventFactory;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class GetListedTextbookDealApiTest extends TestCase
{
    use DatabaseTransactions, ApiPreLoginTrait;

    private DealRepository $dealRepository;
    private DealEventRepository $dealEventRepository;
    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dealRepository = new DealRepository();
        $this->dealEventRepository = new DealEventRepository();
        $this->userRepository = new UserRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
    }

    public function test_認証済みユーザーが出品商品詳細を取得できること_出品中の場合(): void
    {
        // given
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
        $buyerUniversity = TestUniversityFactory::create($buyerUser->universityId, new String255('購入者大学'));
        $this->universityRepository->insert($buyerUniversity);
        $buyerFaculty = TestFacultyFactory::create($buyerUser->facultyId, new String255('購入者学部'), $buyerUser->universityId);
        $this->facultyRepository->insert($buyerFaculty);
        $this->userRepository->insertWithLoginId($buyerUser, $buyerUser->mailAddress);

        // textbook用のデータを作成
        $textbook = TestTextbookFactory::create(
            name: new String255('出品中教科書'),
            universityId: new UniversityId($sellerUser->university_id),
            facultyId: new FacultyId($sellerUser->faculty_id)
        );
        $this->textbookRepository->insert($textbook);

        // 出品中のDealを作成
        $listingDeal = TestDealFactory::create(
            seller: new Seller(new UserId($sellerUser->id)),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook->id,
            dealStatus: DealStatus::create('Listing')
        );
        $this->dealRepository->insert($listingDeal);

        // DealEventも作成
        $dealEvent = TestDealEventFactory::create(
            dealId: $listingDeal->id,
            userId: new UserId($sellerUser->id)
        );
        $this->dealEventRepository->insert($dealEvent);

        // when
        $url = route('me.listed_textbooks.show', ['textbookIdString' => $textbook->id->value]);
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'image_url',
                'image_urls',
                'price',
                'deal' => [
                    'id',
                    'is_purchasable',
                    'seller_info' => [
                        'id',
                        'nickname',
                        'profile_image_url',
                        'university_name',
                        'faculty_name',
                    ],
                    'buyer_shipping_info',
                    'status',
                    'deal_events' => [
                        '*' => [
                            'id',
                            'actor_type',
                            'event_type',
                        ]
                    ]
                ]
            ]);

        $responseData = $response->json();

        // 商品の基本情報確認
        $this->assertEquals($textbook->id->value, $responseData['id']);
        $this->assertEquals('出品中教科書', $responseData['name']);
        $this->assertEquals($textbook->price->value, $responseData['price']);

        // Deal情報の確認（出品中の場合）
        $this->assertTrue($responseData['deal']['is_purchasable']); // 出品中なのでtrue
        $this->assertEquals('listing', $responseData['deal']['status']);
        $this->assertEquals($sellerUser->name, $responseData['deal']['seller_info']['nickname']);
        $this->assertNull($responseData['deal']['buyer_shipping_info']); // 出品中はnull
        $this->assertNotEmpty($responseData['deal']['deal_events']);
    }

    public function test_認証済みユーザーが出品商品詳細を取得できること_売却済みの場合(): void
    {
        // given
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
        $buyerUniversity = TestUniversityFactory::create($buyerUser->universityId, new String255('購入者大学'));
        $this->universityRepository->insert($buyerUniversity);
        $buyerFaculty = TestFacultyFactory::create($buyerUser->facultyId, new String255('購入者学部'), $buyerUser->universityId);
        $this->facultyRepository->insert($buyerFaculty);
        $this->userRepository->insertWithLoginId($buyerUser, $buyerUser->mailAddress);

        // textbook用のデータを作成
        $textbook = TestTextbookFactory::create(
            name: new String255('売却済み教科書'),
            universityId: new UniversityId($sellerUser->university_id),
            facultyId: new FacultyId($sellerUser->faculty_id)
        );
        $this->textbookRepository->insert($textbook);

        // 売却完了済みのDealを作成
        $completedDeal = TestDealFactory::create(
            seller: new Seller(new UserId($sellerUser->id)),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook->id,
            dealStatus: DealStatus::create('Completed')
        );
        $this->dealRepository->insert($completedDeal);

        // DealEventも作成
        $dealEvent = TestDealEventFactory::create(
            dealId: $completedDeal->id,
            userId: new UserId($sellerUser->id)
        );
        $this->dealEventRepository->insert($dealEvent);

        // when
        $url = route('me.listed_textbooks.show', ['textbookIdString' => $textbook->id->value]);
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'image_url',
                'image_urls',
                'price',
                'deal' => [
                    'id',
                    'is_purchasable',
                    'seller_info' => [
                        'id',
                        'nickname',
                        'profile_image_url',
                        'university_name',
                        'faculty_name',
                    ],
                    'buyer_shipping_info' => [
                        'id',
                        'name',
                        'postal_code',
                        'address',
                        'nickname',
                        'profile_image_url',
                    ],
                    'status',
                    'deal_events' => [
                        '*' => [
                            'id',
                            'actor_type',
                            'event_type',
                        ]
                    ]
                ]
            ]);

        $responseData = $response->json();

        // 商品の基本情報確認
        $this->assertEquals($textbook->id->value, $responseData['id']);
        $this->assertEquals('売却済み教科書', $responseData['name']);
        $this->assertEquals($textbook->price->value, $responseData['price']);

        // Deal情報の確認（売却済みの場合）
        $this->assertFalse($responseData['deal']['is_purchasable']); // 売却済みなのでfalse
        $this->assertEquals('completed', $responseData['deal']['status']);
        $this->assertEquals($sellerUser->name, $responseData['deal']['seller_info']['nickname']);
        $this->assertNotNull($responseData['deal']['buyer_shipping_info']); // 売却済みは購入者情報あり
        $this->assertEquals($buyerUser->name->name, $responseData['deal']['buyer_shipping_info']['name']);
        $this->assertNotEmpty($responseData['deal']['deal_events']);
    }

    public function test_他のユーザーが出品した商品詳細は取得できないこと(): void
    {
        // given
        $sellerUser = $this->prepareUserWithFacultyAndUniversity();
        // トークンを生成
        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

        // 他のseller用のユーザーを作成
        $otherSellerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('other@test.com')));
        $otherSellerUniversity = TestUniversityFactory::create($otherSellerUser->universityId, new String255('他出品者大学'));
        $this->universityRepository->insert($otherSellerUniversity);
        $otherSellerFaculty = TestFacultyFactory::create($otherSellerUser->facultyId, new String255('他出品者学部'), $otherSellerUser->universityId);
        $this->facultyRepository->insert($otherSellerFaculty);
        $this->userRepository->insertWithLoginId($otherSellerUser, $otherSellerUser->mailAddress);

        // buyer用のユーザーを作成
        $buyerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('buyer@test.com')));
        $buyerUniversity = TestUniversityFactory::create($buyerUser->universityId, new String255('購入者大学'));
        $this->universityRepository->insert($buyerUniversity);
        $buyerFaculty = TestFacultyFactory::create($buyerUser->facultyId, new String255('購入者学部'), $buyerUser->universityId);
        $this->facultyRepository->insert($buyerFaculty);
        $this->userRepository->insertWithLoginId($buyerUser, $buyerUser->mailAddress);

        // textbook用のデータを作成
        $textbook = TestTextbookFactory::create(
            name: new String255('他人の出品商品'),
            universityId: $otherSellerUser->universityId,
            facultyId: $otherSellerUser->facultyId
        );
        $this->textbookRepository->insert($textbook);

        // 他のユーザーが出品したDealを作成
        $listingDeal = TestDealFactory::create(
            seller: new Seller($otherSellerUser->id),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook->id,
            dealStatus: DealStatus::create('Listing')
        );
        $this->dealRepository->insert($listingDeal);

        // when
        $url = route('me.listed_textbooks.show', ['textbookIdString' => $textbook->id->value]);
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertNotFound();
    }

    public function test_存在しない商品IDを指定した場合は404エラーが返されること(): void
    {
        // given
        $sellerUser = $this->prepareUserWithFacultyAndUniversity();
        // トークンを生成
        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

        // when
        $nonExistentId = '00000000-0000-0000-0000-000000000000';
        $url = route('me.listed_textbooks.show', ['textbookIdString' => $nonExistentId]);
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertNotFound();
    }
}
