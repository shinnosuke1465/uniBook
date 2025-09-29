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
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_findByUserIdWithRelationsでリレーション付き取引ルームを取得できること(): void
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
        $result = $this->dealRoomRepository->findByUserIdWithRelations($users['seller']->id);

        // then
        $this->assertCount(1, $result);
        $this->assertEquals($dealRoom->id->value, $result[0]->id);

        // リレーションのチェック
        $this->assertNotNull($result[0]->deal);
        $this->assertEquals($deal->id->value, $result[0]->deal->id);

        // Seller情報のチェック
        $this->assertNotNull($result[0]->deal->seller);
        $this->assertEquals($users['seller']->id->value, $result[0]->deal->seller->id);

        // Textbook情報のチェック
        $this->assertNotNull($result[0]->deal->textbook);
        $this->assertEquals($textbook->id->value, $result[0]->deal->textbook->id);

        // Users情報のチェック
        $this->assertCount(2, $result[0]->users);
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_findByUserIdWithRelationsで複数の取引ルームを作成日時の降順で取得できること(): void
    {
        // given
        $users = $this->createTestUsers();

        // 複数の取引ルームを作成
        $textbook1 = $this->createTestTextbook($users['seller']);
        $deal1 = $this->createTestDeal($users['seller'], $users['buyer'], $textbook1);
        $userIds = new UserIdList([$users['seller']->id, $users['buyer']->id]);
        $dealRoom1 = TestDealRoomFactory::create(dealId: $deal1->id, userIds: $userIds);
        $this->dealRoomRepository->insert($dealRoom1);

        // 2秒待機して作成日時に差をつける
        sleep(1);

        $textbook2 = TestTextbookFactory::create(
            universityId: $users['seller']->universityId,
            facultyId: $users['seller']->facultyId
        );
        $this->textbookRepository->insert($textbook2);
        $deal2 = TestDealFactory::create(
            seller: new Seller($users['seller']->id),
            buyer: new Buyer($users['buyer']->id),
            textbookId: $textbook2->id,
            dealStatus: DealStatus::Purchased
        );
        $this->dealRepository->insert($deal2);
        $dealRoom2 = TestDealRoomFactory::create(dealId: $deal2->id, userIds: $userIds);
        $this->dealRoomRepository->insert($dealRoom2);

        // when
        $result = $this->dealRoomRepository->findByUserIdWithRelations($users['seller']->id);

        // then
        $this->assertCount(2, $result);
        // 新しい方が最初に来ることを確認（降順）
        $this->assertEquals($dealRoom2->id->value, $result[0]->id);
        $this->assertEquals($dealRoom1->id->value, $result[1]->id);
    }

    /**
     * @throws DomainException
     */
    public function test_findByUserIdWithRelationsで参加していない取引ルームは取得されないこと(): void
    {
        // given
        // 3人のユーザーを作成
        $seller = TestUserFactory::create(mailAddress: new MailAddress(new String255('seller_not_member@test.com')));
        $sellerUniversity = TestUniversityFactory::create($seller->universityId, new String255('売り手大学3'));
        $this->universityRepository->insert($sellerUniversity);
        $sellerFaculty = TestFacultyFactory::create($seller->facultyId, new String255('売り手学部3'), $seller->universityId);
        $this->facultyRepository->insert($sellerFaculty);
        $this->userRepository->insertWithLoginId($seller, $seller->mailAddress);

        $buyer = TestUserFactory::create(mailAddress: new MailAddress(new String255('buyer_member@test.com')));
        $buyerUniversity = TestUniversityFactory::create($buyer->universityId, new String255('買い手大学3'));
        $this->universityRepository->insert($buyerUniversity);
        $buyerFaculty = TestFacultyFactory::create($buyer->facultyId, new String255('買い手学部3'), $buyer->universityId);
        $this->facultyRepository->insert($buyerFaculty);
        $this->userRepository->insertWithLoginId($buyer, $buyer->mailAddress);

        $otherUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('other_user@test.com')));
        $otherUniversity = TestUniversityFactory::create($otherUser->universityId, new String255('その他大学'));
        $this->universityRepository->insert($otherUniversity);
        $otherFaculty = TestFacultyFactory::create($otherUser->facultyId, new String255('その他学部'), $otherUser->universityId);
        $this->facultyRepository->insert($otherFaculty);
        $this->userRepository->insertWithLoginId($otherUser, $otherUser->mailAddress);

        // sellerとbuyerでDealRoomを作成
        $textbook = $this->createTestTextbook($seller);
        $deal = $this->createTestDeal($seller, $buyer, $textbook);
        $userIds = new UserIdList([$seller->id, $buyer->id]);
        $dealRoom = TestDealRoomFactory::create(dealId: $deal->id, userIds: $userIds);
        $this->dealRoomRepository->insert($dealRoom);

        // when - 参加していないotherUserで検索
        $result = $this->dealRoomRepository->findByUserIdWithRelations($otherUser->id);

        // then
        $this->assertCount(0, $result);
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
