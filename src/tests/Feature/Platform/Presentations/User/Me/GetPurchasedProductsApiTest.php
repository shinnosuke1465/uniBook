<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\User\Me;

use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\User\UserId;
use App\Platform\Infrastructures\Deal\DealRepository;
use App\Platform\Infrastructures\DealEvent\DealEventRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\Unit\Platform\Domains\Deal\TestDealFactory;
use Tests\Unit\Platform\Domains\DealEvent\TestDealEventFactory;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class GetPurchasedProductsApiTest extends TestCase
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

    public function test_認証済みユーザーが購入商品一覧を取得できること(): void
    {
        // given
        $buyerUser = $this->prepareUserWithFacultyAndUniversity();
        // トークンを生成
        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

        // seller用のユーザーを作成
        $sellerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('seller@test.com')));
        $sellerUniversity = TestUniversityFactory::create($sellerUser->universityId, new String255('販売者大学'));
        $this->universityRepository->insert($sellerUniversity);
        $sellerFaculty = TestFacultyFactory::create($sellerUser->facultyId, new String255('販売者学部'), $sellerUser->universityId);
        $this->facultyRepository->insert($sellerFaculty);
        $this->userRepository->insertWithLoginId($sellerUser, $sellerUser->mailAddress);

        // textbook用のデータを作成
        $textbook1 = TestTextbookFactory::create(
            name: new String255('購入済み教科書1'),
            universityId: $sellerUser->universityId,
            facultyId: $sellerUser->facultyId
        );
        $this->textbookRepository->insert($textbook1);

        $textbook2 = TestTextbookFactory::create(
            name: new String255('購入済み教科書2'),
            universityId: $sellerUser->universityId,
            facultyId: $sellerUser->facultyId
        );
        $this->textbookRepository->insert($textbook2);

        // 購入完了済みのDealを作成
        $completedDeal1 = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer(new UserId($buyerUser->id)),
            textbookId: $textbook1->id,
            dealStatus: DealStatus::create('Completed')
        );
        $this->dealRepository->insert($completedDeal1);

        $completedDeal2 = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer(new UserId($buyerUser->id)),
            textbookId: $textbook2->id,
            dealStatus: DealStatus::create('Completed')
        );
        $this->dealRepository->insert($completedDeal2);

        // DealEventも作成
        $dealEvent1 = TestDealEventFactory::create(
            dealId: $completedDeal1->id,
            userId: $sellerUser->id
        );
        $this->dealEventRepository->insert($dealEvent1);

        $dealEvent2 = TestDealEventFactory::create(
            dealId: $completedDeal2->id,
            userId: $sellerUser->id
        );
        $this->dealEventRepository->insert($dealEvent2);

        // Listing状態のDeal（結果に含まれないはず）
        $listingDeal = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer(new UserId($buyerUser->id)),
            textbookId: $textbook1->id,
            dealStatus: DealStatus::create('Listing')
        );
        $this->dealRepository->insert($listingDeal);

        // when
        $url = route('me.purchased_textbooks');
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
                    ]
                ]
            ]);

        $responseData = $response->json();
        $this->assertCount(2, $responseData['products']); // 購入完了済みの2つのみ

        // 商品の基本情報確認
        $product1 = collect($responseData['products'])->firstWhere('name', '購入済み教科書1');
        $product2 = collect($responseData['products'])->firstWhere('name', '購入済み教科書2');

        $this->assertNotNull($product1);
        $this->assertNotNull($product2);

        // Deal情報の確認
        $this->assertFalse($product1['deal']['is_purchasable']); // 購入済みなのでfalse
        $this->assertEquals('completed', $product1['deal']['status']);
        $this->assertEquals($sellerUser->name->name, $product1['deal']['seller_info']['nickname']);
        $this->assertEquals($buyerUser->name, $product1['deal']['buyer_shipping_info']['name']);
        $this->assertNotEmpty($product1['deal']['deal_events']);
    }

    public function test_認証済みユーザーが購入商品がない場合は空の配列を返すこと(): void
    {
        // given
        $buyerUser = $this->prepareUserWithFacultyAndUniversity();
        // トークンを生成
        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

        // when
        $url = route('me.purchased_textbooks');
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
}
