<?php

declare(strict_types=1);

namespace Feature\Platform\Infrastructures\DealRoom;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Deal\TestDealFactory;
use Tests\Unit\Platform\Domains\DealRoom\TestDealRoomFactory;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class DealRoomRepositoryTest extends TestCase
{
    use DatabaseTransactions;

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

    /**
     * 取引ルーム登録のテスト
     * @throws DomainException
     * @throws DuplicateKeyException
     * @throws NotFoundException
     */
    public function test_insertで取引ルームを登録できること(): void
    {
        // given
        // seller用のユーザーを作成
        $sellerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('seller1@test.com')));
        $sellerUniversity = TestUniversityFactory::create($sellerUser->universityId, new String255('売り手大学'));
        $this->universityRepository->insert($sellerUniversity);
        $sellerFaculty = TestFacultyFactory::create($sellerUser->facultyId, new String255('売り手学部'), $sellerUser->universityId);
        $this->facultyRepository->insert($sellerFaculty);
        $this->userRepository->insertWithLoginId($sellerUser, $sellerUser->mailAddress);

        // buyer用のユーザーを作成
        $buyerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('buyer1@test.com')));
        $buyerUniversity = TestUniversityFactory::create($buyerUser->universityId, new String255('買い手大学'));
        $this->universityRepository->insert($buyerUniversity);
        $buyerFaculty = TestFacultyFactory::create($buyerUser->facultyId, new String255('買い手学部'), $buyerUser->universityId);
        $this->facultyRepository->insert($buyerFaculty);
        $this->userRepository->insertWithLoginId($buyerUser, $buyerUser->mailAddress);

        // textbook用のデータを作成
        $textbook = TestTextbookFactory::create(
            universityId: $sellerUser->universityId,
            facultyId: $sellerUser->facultyId
        );
        $this->textbookRepository->insert($textbook);

        // deal作成
        $deal = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook->id,
            dealStatus: DealStatus::Purchased
        );
        $this->dealRepository->insert($deal);

        // dealRoom作成
        $userIds = new UserIdList([$sellerUser->id, $buyerUser->id]);
        $dealRoom = TestDealRoomFactory::create(
            dealId: $deal->id,
            userIds: $userIds
        );

        // when
        $this->dealRoomRepository->insert($dealRoom);

        // then
        $this->assertDatabaseHas('deal_rooms', [
            'id' => $dealRoom->id->value,
            'deal_id' => $dealRoom->dealId->value,
        ]);

        // 中間テーブルのチェック
        $this->assertDatabaseHas('deal_room_users', [
            'deal_room_id' => $dealRoom->id->value,
            'user_id' => $sellerUser->id->value,
        ]);
        $this->assertDatabaseHas('deal_room_users', [
            'deal_room_id' => $dealRoom->id->value,
            'user_id' => $buyerUser->id->value,
        ]);
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_findByIdで取引ルームを取得できること(): void
    {
        // given
        // テストデータを準備
        $users = $this->createTestUsers();
        $textbook = $this->createTestTextbook($users['seller']);
        $deal = $this->createTestDeal($users['seller'], $users['buyer'], $textbook);

        $userIds = new UserIdList([$users['seller']->id, $users['buyer']->id]);
        $dealRoom = TestDealRoomFactory::create(
            dealId: $deal->id,
            userIds: $userIds
        );
        $this->dealRoomRepository->insert($dealRoom);

        // when
        $result = $this->dealRoomRepository->findById($dealRoom->id);

        // then
        $this->assertNotNull($result);
        $this->assertEquals($dealRoom->id->value, $result->id->value);
        $this->assertEquals($dealRoom->dealId->value, $result->dealId->value);
        $this->assertCount(2, $result->getUserIds());
        $this->assertContains($users['seller']->id->value, $result->getUserIds());
        $this->assertContains($users['buyer']->id->value, $result->getUserIds());
    }

    public function test_findByUserIdでユーザーが参加する取引ルームを取得できること(): void
    {
        // given
        $users = $this->createTestUsers();
        $textbook = $this->createTestTextbook($users['seller']);
        $deal = $this->createTestDeal($users['seller'], $users['buyer'], $textbook);

        $userIds = new UserIdList([$users['seller']->id, $users['buyer']->id]);
        $dealRoom = TestDealRoomFactory::create(
            dealId: $deal->id,
            userIds: $userIds
        );
        $this->dealRoomRepository->insert($dealRoom);

        // when
        $result = $this->dealRoomRepository->findByUserId($users['seller']->id);

        // then
        $this->assertCount(1, $result);
        $this->assertEquals($dealRoom->id->value, $result[0]->id->value);
    }

    public function test_insertで同じIDの取引ルームを登録した場合DuplicateKeyExceptionが発生すること(): void
    {
        // given
        $users = $this->createTestUsers();
        $textbook = $this->createTestTextbook($users['seller']);
        $deal = $this->createTestDeal($users['seller'], $users['buyer'], $textbook);

        $userIds = new UserIdList([$users['seller']->id, $users['buyer']->id]);
        $dealRoom1 = TestDealRoomFactory::create(
            dealId: $deal->id,
            userIds: $userIds
        );
        $dealRoom2 = TestDealRoomFactory::create(
            id: $dealRoom1->id, // 同じID
            dealId: $deal->id,
            userIds: $userIds
        );
        $this->dealRoomRepository->insert($dealRoom1);

        // when & then
        $this->expectException(DuplicateKeyException::class);
        $this->expectExceptionMessage('取引ルームが重複しています。');
        $this->dealRoomRepository->insert($dealRoom2);
    }

    /**
     * テスト用のユーザーデータを作成
     */
    private function createTestUsers(): array
    {
        // seller用のユーザーを作成
        $sellerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('seller@test.com')));
        $sellerUniversity = TestUniversityFactory::create($sellerUser->universityId, new String255('売り手大学'));
        $this->universityRepository->insert($sellerUniversity);
        $sellerFaculty = TestFacultyFactory::create($sellerUser->facultyId, new String255('売り手学部'), $sellerUser->universityId);
        $this->facultyRepository->insert($sellerFaculty);
        $this->userRepository->insertWithLoginId($sellerUser, $sellerUser->mailAddress);

        // buyer用のユーザーを作成
        $buyerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('buyer@test.com')));
        $buyerUniversity = TestUniversityFactory::create($buyerUser->universityId, new String255('買い手大学'));
        $this->universityRepository->insert($buyerUniversity);
        $buyerFaculty = TestFacultyFactory::create($buyerUser->facultyId, new String255('買い手学部'), $buyerUser->universityId);
        $this->facultyRepository->insert($buyerFaculty);
        $this->userRepository->insertWithLoginId($buyerUser, $buyerUser->mailAddress);

        return [
            'seller' => $sellerUser,
            'buyer' => $buyerUser,
        ];
    }

    /**
     * テスト用のTextbookデータを作成
     */
    private function createTestTextbook($user)
    {
        $textbook = TestTextbookFactory::create(
            universityId: $user->universityId,
            facultyId: $user->facultyId
        );
        $this->textbookRepository->insert($textbook);
        return $textbook;
    }

    /**
     * テスト用のDealデータを作成
     */
    private function createTestDeal($sellerUser, $buyerUser, $textbook)
    {
        $deal = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook->id,
            dealStatus: DealStatus::Purchased
        );
        $this->dealRepository->insert($deal);
        return $deal;
    }
}
