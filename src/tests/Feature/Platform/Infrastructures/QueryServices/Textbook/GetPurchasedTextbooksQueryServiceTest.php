<?php

declare(strict_types=1);

namespace Feature\Platform\Infrastructures\QueryServices\Textbook;

use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Infrastructures\Deal\DealRepository;
use App\Platform\Infrastructures\DealEvent\DealEventRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\QueryServices\Textbook\GetPurchasedTextbooksQueryService;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Deal\TestDealFactory;
use Tests\Unit\Platform\Domains\DealEvent\TestDealEventFactory;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class GetPurchasedTextbooksQueryServiceTest extends TestCase
{
    use DatabaseTransactions;

    private GetPurchasedTextbooksQueryService $queryService;
    private DealRepository $dealRepository;
    private DealEventRepository $dealEventRepository;
    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->queryService = new GetPurchasedTextbooksQueryService();
        $this->dealRepository = new DealRepository();
        $this->dealEventRepository = new DealEventRepository();
        $this->userRepository = new UserRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
    }

    public function test_getPurchasedTextbooksByUserで購入済み教科書一覧を取得できること(): void
    {
        // given
        // seller用のユーザーを作成
        $sellerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('seller@test.com')));
        $sellerUniversity = TestUniversityFactory::create($sellerUser->universityId, new String255('販売者大学'));
        $this->universityRepository->insert($sellerUniversity);
        $sellerFaculty = TestFacultyFactory::create($sellerUser->facultyId, new String255('販売者学部'), $sellerUser->universityId);
        $this->facultyRepository->insert($sellerFaculty);
        $this->userRepository->insertWithLoginId($sellerUser, $sellerUser->mailAddress);

        // buyer用のユーザーを作成
        $buyerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('buyer@test.com')));
        $buyerUniversity = TestUniversityFactory::create($buyerUser->universityId, new String255('購入者大学'));
        $this->universityRepository->insert($buyerUniversity);
        $buyerFaculty = TestFacultyFactory::create($buyerUser->facultyId, new String255('購入者学部'), $buyerUser->universityId);
        $this->facultyRepository->insert($buyerFaculty);
        $this->userRepository->insertWithLoginId($buyerUser, $buyerUser->mailAddress);

        // textbook用のデータを作成
        $textbook = TestTextbookFactory::create(
            universityId: $sellerUser->universityId,
            facultyId: $sellerUser->facultyId
        );
        $this->textbookRepository->insert($textbook);

        // 購入完了済みのDealを作成
        $completedDeal = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook->id,
            dealStatus: DealStatus::create('Completed')
        );
        $this->dealRepository->insert($completedDeal);

        // DealEventも作成（sellerユーザーのIDを使用）
        $dealEvent = TestDealEventFactory::create(
            dealId: $completedDeal->id,
            userId: $sellerUser->id
        );
        $this->dealEventRepository->insert($dealEvent);

        // 別のユーザーの購入済み商品（結果に含まれないはず）
        $otherBuyerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('other@test.com')));
        $otherBuyerUniversity = TestUniversityFactory::create($otherBuyerUser->universityId, new String255('他購入者大学'));
        $this->universityRepository->insert($otherBuyerUniversity);
        $otherBuyerFaculty = TestFacultyFactory::create($otherBuyerUser->facultyId, new String255('他購入者学部'), $otherBuyerUser->universityId);
        $this->facultyRepository->insert($otherBuyerFaculty);
        $this->userRepository->insertWithLoginId($otherBuyerUser, $otherBuyerUser->mailAddress);
        $otherDeal = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer($otherBuyerUser->id),
            textbookId: $textbook->id,
            dealStatus: DealStatus::create('Completed')
        );
        $this->dealRepository->insert($otherDeal);

        // 同じユーザーのListing状態のDeal（結果に含まれないはず）
        $listingDeal = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook->id,
            dealStatus: DealStatus::create('Listing')
        );
        $this->dealRepository->insert($listingDeal);

        // when
        $result = $this->queryService->getPurchasedTextbooksByUser($buyerUser->id);

        // then
        $this->assertCount(1, $result);
        $this->assertEquals($completedDeal->id->value, $result->first()->id);
        $this->assertEquals($textbook->id->value, $result->first()->textbook->id);
        $this->assertEquals('販売者大学', $result->first()->textbook->university->name);
        $this->assertEquals('販売者学部', $result->first()->textbook->faculty->name);
        $this->assertEquals($sellerUser->name->name, $result->first()->seller->name);
        $this->assertEquals($buyerUser->name->name, $result->first()->buyer->name);
        $this->assertNotEmpty($result->first()->dealEvents);
    }

    public function test_getPurchasedTextbooksByUserで購入済み教科書がない場合は空の配列を返すこと(): void
    {
        // given
        $buyerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('buyer@test.com')));
        $buyerUniversity = TestUniversityFactory::create($buyerUser->universityId, new String255('購入者大学'));
        $this->universityRepository->insert($buyerUniversity);
        $buyerFaculty = TestFacultyFactory::create($buyerUser->facultyId, new String255('購入者学部'), $buyerUser->universityId);
        $this->facultyRepository->insert($buyerFaculty);
        $this->userRepository->insertWithLoginId($buyerUser, $buyerUser->mailAddress);

        // when
        $result = $this->queryService->getPurchasedTextbooksByUser($buyerUser->id);

        // then
        $this->assertCount(0, $result);
    }
}
