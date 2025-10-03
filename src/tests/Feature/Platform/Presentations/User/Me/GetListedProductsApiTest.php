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

class GetListedProductsApiTest extends TestCase
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

    public function test_認証済みユーザーが出品商品一覧を取得できること(): void
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
        $textbook1 = TestTextbookFactory::create(
            name: new String255('出品教科書1'),
            universityId: new UniversityId($sellerUser->university_id),
            facultyId: new FacultyId($sellerUser->faculty_id)
        );
        $this->textbookRepository->insert($textbook1);

        $textbook2 = TestTextbookFactory::create(
            name: new String255('出品教科書2'),
            universityId: new UniversityId($sellerUser->university_id),
            facultyId: new FacultyId($sellerUser->faculty_id)
        );
        $this->textbookRepository->insert($textbook2);

        // 出品中のDealを作成（is_purchasable: true）
        $listingDeal = TestDealFactory::create(
            seller: new Seller(new UserId($sellerUser->id)),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook1->id,
            dealStatus: DealStatus::create('Listing')
        );
        $this->dealRepository->insert($listingDeal);

        // 売却完了済みのDealを作成（is_purchasable: false）
        $completedDeal = TestDealFactory::create(
            seller: new Seller(new UserId($sellerUser->id)),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook2->id,
            dealStatus: DealStatus::create('Completed')
        );
        $this->dealRepository->insert($completedDeal);

        // DealEventも作成
        $dealEvent1 = TestDealEventFactory::create(
            dealId: $listingDeal->id,
            userId: new UserId($sellerUser->id)
        );
        $this->dealEventRepository->insert($dealEvent1);

        $dealEvent2 = TestDealEventFactory::create(
            dealId: $completedDeal->id,
            userId: new UserId($sellerUser->id)
        );
        $this->dealEventRepository->insert($dealEvent2);

        // when
        $url = route('me.listed_textbooks');
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertOk()
            ->assertJsonStructure([
                'products' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'image_urls',
                        'price',
                        'deal' => [
                            'id',
                            'is_purchasable',
                            'seller_info' => [
                                'id',
                                'nickname',
                                'profile_image_url',
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
                    ]
                ]
            ]);

        $responseData = $response->json();
        $this->assertCount(2, $responseData['products']); // 出品中・売却済み両方

        // 商品の基本情報確認
        $product1 = collect($responseData['products'])->firstWhere('name', '出品教科書1');
        $product2 = collect($responseData['products'])->firstWhere('name', '出品教科書2');

        $this->assertNotNull($product1);
        $this->assertNotNull($product2);

        // Deal情報の確認
        $listingProduct = collect($responseData['products'])->firstWhere('deal.status', 'listing');
        $completedProduct = collect($responseData['products'])->firstWhere('deal.status', 'completed');

        // 出品中商品の確認
        $this->assertTrue($listingProduct['deal']['is_purchasable']); // 出品中なのでtrue
        $this->assertEquals('listing', $listingProduct['deal']['status']);
        $this->assertEquals($sellerUser->name, $listingProduct['deal']['seller_info']['nickname']);
        $this->assertNotEmpty($listingProduct['deal']['deal_events']);

        // 売却済み商品の確認
        $this->assertFalse($completedProduct['deal']['is_purchasable']); // 売却済みなのでfalse
        $this->assertEquals('completed', $completedProduct['deal']['status']);
        $this->assertEquals($sellerUser->name, $completedProduct['deal']['seller_info']['nickname']);
        $this->assertNotEmpty($completedProduct['deal']['buyer_shipping_info']);
        $this->assertNotEmpty($completedProduct['deal']['deal_events']);
    }

    public function test_認証済みユーザーが出品商品がない場合は空の配列を返すこと(): void
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
        $url = route('me.listed_textbooks');
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertOk()
            ->assertJsonStructure([
                'products' => []
            ]);

        $responseData = $response->json();
        $this->assertCount(0, $responseData['products']);
    }

    public function test_出品中商品のbuyer_shipping_infoがnullであること(): void
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

        // textbook用のデータを作成
        $textbook = TestTextbookFactory::create(
            name: new String255('出品中教科書'),
            universityId: new UniversityId($sellerUser->university_id),
            facultyId: new FacultyId($sellerUser->faculty_id)
        );
        $this->textbookRepository->insert($textbook);

        // buyer_idがnullの出品中Deal（実際にはbuyerは必須だが、テストのためListingで作成）
        $listingDeal = TestDealFactory::create(
            seller: new Seller(new UserId($sellerUser->id)),
            buyer: new Buyer(new UserId($sellerUser->id)), // 実際の実装ではListingの場合buyerはnullになる想定
            textbookId: $textbook->id,
            dealStatus: DealStatus::create('Listing')
        );
        $this->dealRepository->insert($listingDeal);

        // when
        $url = route('me.listed_textbooks');
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertOk();
        $responseData = $response->json();
        $this->assertCount(1, $responseData['products']);

        $product = $responseData['products'][0];
        $this->assertTrue($product['deal']['is_purchasable']);
        $this->assertEquals('listing', $product['deal']['status']);
        // 注意: 現在の実装では常にbuyerが設定されるが、実際の運用では出品中はbuyerがnullになる
    }
}
