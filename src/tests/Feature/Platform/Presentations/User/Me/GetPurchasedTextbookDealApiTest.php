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
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Deal\TestDealFactory;
use Tests\Unit\Platform\Domains\DealEvent\TestDealEventFactory;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class GetPurchasedTextbookDealApiTest extends TestCase
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

    public function test_認証済みユーザーが購入商品詳細を取得できること(): void
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
        $textbook = TestTextbookFactory::create(
            name: new String255('購入済み教科書'),
            universityId: $sellerUser->universityId,
            facultyId: $sellerUser->facultyId
        );
        $this->textbookRepository->insert($textbook);

        // 購入完了済みのDealを作成
        $completedDeal = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer(new UserId($buyerUser->id)),
            textbookId: $textbook->id,
            dealStatus: DealStatus::create('Completed')
        );
        $this->dealRepository->insert($completedDeal);

        // DealEventも作成
        $dealEvent = TestDealEventFactory::create(
            dealId: $completedDeal->id,
            userId: $sellerUser->id
        );
        $this->dealEventRepository->insert($dealEvent);

        // when
        $url = route('me.purchased_textbooks.show', ['textbookIdString' => $textbook->id->value]);
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
                            'created_at',
                        ]
                    ]
                ]
            ]);

        $responseData = $response->json();

        // 商品の基本情報確認
        $this->assertEquals($textbook->id->value, $responseData['id']);
        $this->assertEquals('購入済み教科書', $responseData['name']);
        $this->assertEquals($textbook->price->value, $responseData['price']);

        // Deal情報の確認
        $this->assertFalse($responseData['deal']['is_purchasable']); // 購入済みなのでfalse
        $this->assertEquals('completed', $responseData['deal']['status']);
        $this->assertEquals($sellerUser->name->name, $responseData['deal']['seller_info']['nickname']);
        $this->assertEquals($buyerUser->name, $responseData['deal']['buyer_shipping_info']['name']);
        $this->assertNotEmpty($responseData['deal']['deal_events']);
    }

    public function test_他のユーザーが購入した商品詳細は取得できないこと(): void
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

        // 他のbuyer用のユーザーを作成
        $otherBuyerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('other@test.com')));
        $otherBuyerUniversity = TestUniversityFactory::create($otherBuyerUser->universityId, new String255('他購入者大学'));
        $this->universityRepository->insert($otherBuyerUniversity);
        $otherBuyerFaculty = TestFacultyFactory::create($otherBuyerUser->facultyId, new String255('他購入者学部'), $otherBuyerUser->universityId);
        $this->facultyRepository->insert($otherBuyerFaculty);
        $this->userRepository->insertWithLoginId($otherBuyerUser, $otherBuyerUser->mailAddress);

        // seller用のユーザーを作成
        $sellerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('seller@test.com')));
        $sellerUniversity = TestUniversityFactory::create($sellerUser->universityId, new String255('販売者大学'));
        $this->universityRepository->insert($sellerUniversity);
        $sellerFaculty = TestFacultyFactory::create($sellerUser->facultyId, new String255('販売者学部'), $sellerUser->universityId);
        $this->facultyRepository->insert($sellerFaculty);
        $this->userRepository->insertWithLoginId($sellerUser, $sellerUser->mailAddress);

        // textbook用のデータを作成
        $textbook = TestTextbookFactory::create(
            name: new String255('他人の購入商品'),
            universityId: $sellerUser->universityId,
            facultyId: $sellerUser->facultyId
        );
        $this->textbookRepository->insert($textbook);

        // 他のユーザーが購入したDealを作成
        $completedDeal = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer($otherBuyerUser->id),
            textbookId: $textbook->id,
            dealStatus: DealStatus::create('Completed')
        );
        $this->dealRepository->insert($completedDeal);

        // when
        $url = route('me.purchased_textbooks.show', ['textbookIdString' => $textbook->id->value]);
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertNotFound();
    }

    public function test_存在しない商品IDを指定した場合は404エラーが返されること(): void
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
        $nonExistentId = '00000000-0000-0000-0000-000000000000';
        $url = route('me.purchased_textbooks.show', ['textbookIdString' => $nonExistentId]);
        $response = $this->getJson($url, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then
        $response->assertNotFound();
    }
}
