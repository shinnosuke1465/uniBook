<?php

declare(strict_types=1);

namespace Feature\Platform\Infrastructures\DealMessage;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\DealMessage\Sender;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\User\UserIdList;
use App\Platform\Infrastructures\Deal\DealRepository;
use App\Platform\Infrastructures\DealMessage\DealMessageRepository;
use App\Platform\Infrastructures\DealRoom\DealRoomRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Deal\TestDealFactory;
use Tests\Unit\Platform\Domains\DealMessage\TestDealMessageFactory;
use Tests\Unit\Platform\Domains\DealRoom\TestDealRoomFactory;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class DealMessageRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private DealMessageRepository $dealMessageRepository;
    private DealRoomRepository $dealRoomRepository;
    private DealRepository $dealRepository;
    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dealMessageRepository = new DealMessageRepository();
        $this->dealRoomRepository = new DealRoomRepository();
        $this->dealRepository = new DealRepository();
        $this->userRepository = new UserRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
    }

    /**
     * 取引メッセージ登録のテスト
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_insertで取引メッセージを登録できること(): void
    {
        // given
        $users = $this->createTestUsers();
        $textbook = $this->createTestTextbook($users['seller']);
        $deal = $this->createTestDeal($users['seller'], $users['buyer'], $textbook);
        $dealRoom = $this->createTestDealRoom($deal->id, $users['seller']->id, $users['buyer']->id);

        $dealMessage = TestDealMessageFactory::create(
            sender: new Sender($users['seller']->id),
            dealRoomId: $dealRoom->id,
            message: new Text('こんにちは、商品について質問があります。')
        );

        // when
        $this->dealMessageRepository->insert($dealMessage);

        // then
        $this->assertDatabaseHas('deal_messages', [
            'id' => $dealMessage->id->value,
            'user_id' => $users['seller']->id->value,
            'deal_room_id' => $dealRoom->id->value,
            'message' => 'こんにちは、商品について質問があります。',
        ]);
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     */
    public function test_insertで同じIDのメッセージを登録した場合DuplicateKeyExceptionが発生すること(): void
    {
        // given
        $users = $this->createTestUsers();
        $textbook = $this->createTestTextbook($users['seller']);
        $deal = $this->createTestDeal($users['seller'], $users['buyer'], $textbook);
        $dealRoom = $this->createTestDealRoom($deal->id, $users['seller']->id, $users['buyer']->id);

        $dealMessage1 = TestDealMessageFactory::create(
            sender: new Sender($users['seller']->id),
            dealRoomId: $dealRoom->id,
            message: new Text('最初のメッセージ')
        );
        $dealMessage2 = TestDealMessageFactory::create(
            id: $dealMessage1->id, // 同じID
            sender: new Sender($users['buyer']->id),
            dealRoomId: $dealRoom->id,
            message: new Text('2番目のメッセージ')
        );
        $this->dealMessageRepository->insert($dealMessage1);

        // when & then
        $this->expectException(DuplicateKeyException::class);
        $this->expectExceptionMessage('メッセージが重複しています。');
        $this->dealMessageRepository->insert($dealMessage2);
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

    /**
     * テスト用のDealRoomデータを作成
     */
    private function createTestDealRoom($dealId, $sellerId, $buyerId)
    {
        $userIds = new UserIdList([$sellerId, $buyerId]);
        $dealRoom = TestDealRoomFactory::create(
            dealId: $dealId,
            userIds: $userIds
        );
        $this->dealRoomRepository->insert($dealRoom);
        return $dealRoom;
    }
}
