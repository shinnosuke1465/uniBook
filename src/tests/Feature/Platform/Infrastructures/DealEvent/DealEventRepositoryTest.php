<?php

declare(strict_types=1);

namespace Feature\Platform\Infrastructures\DealEvent;

use App\Exceptions\DuplicateKeyException;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Infrastructures\Deal\DealRepository;
use App\Platform\Infrastructures\DealEvent\DealEventRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
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
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\User\MailAddress;

class DealEventRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private DealEventRepository $dealEventRepository;

    private DealRepository $dealRepository;

    private UserRepository $userRepository;

    private UniversityRepository $universityRepository;

    private FacultyRepository $facultyRepository;

    private TextbookRepository $textbookRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dealEventRepository = new DealEventRepository();
        $this->dealRepository = new DealRepository();
        $this->userRepository = new UserRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
        $this->textbookRepository = new TextbookRepository();
    }

    public function test_insertで取引イベントを登録できること(): void
    {
        //given
        // 必要な関連データを作成
        $inputUser = TestUserFactory::create(
            mailAddress: new MailAddress(new String255('event-user@test.com'))
        );
        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        // 追加でseller/buyerユーザーを作成（異なるメールアドレスを使用）
        $sellerUser = TestUserFactory::create(
            mailAddress: new MailAddress(new String255('seller@test.com'))
        );
        $sellerUniversity = TestUniversityFactory::create($sellerUser->universityId, new String255('売り手大学'));
        $this->universityRepository->insert($sellerUniversity);
        $sellerFaculty = TestFacultyFactory::create($sellerUser->facultyId, new String255('売り手学部'), $sellerUser->universityId);
        $this->facultyRepository->insert($sellerFaculty);
        $this->userRepository->insertWithLoginId($sellerUser, $sellerUser->mailAddress);

        $buyerUser = TestUserFactory::create(
            mailAddress: new MailAddress(new String255('buyer@test.com'))
        );
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

        $inputDeal = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook->id
        );
        $this->dealRepository->insert($inputDeal);

        // DealEventを作成（既存のuser_idとdeal_idを使用）
        $inputDealEvent = TestDealEventFactory::create(
            userId: $inputUser->id,
            dealId: $inputDeal->id
        );

        //when
        $this->dealEventRepository->insert($inputDealEvent);

        //then
        $this->assertDatabaseHas('deal_events', [
            'id' => $inputDealEvent->id->value,
            'user_id' => $inputDealEvent->userId->value,
            'deal_id' => $inputDealEvent->dealId->value,
            'actor_type' => $inputDealEvent->actorType->value,
            'event_type' => $inputDealEvent->eventType->value,
        ]);
    }

    public function test_insertで同じIDの取引イベントを登録した場合DuplicateKeyExceptionが発生すること(): void
    {
        //given
        // 必要な関連データを作成
        $inputUser = TestUserFactory::create(
            mailAddress: new MailAddress(new String255('event-user2@test.com'))
        );
        $university = TestUniversityFactory::create($inputUser->universityId, new String255('テスト大学'));
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create($inputUser->facultyId, new String255('テスト学部'), $inputUser->universityId);
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);

        // 追加でseller/buyerユーザーを作成（異なるメールアドレスを使用）
        $sellerUser = TestUserFactory::create(
            mailAddress: new MailAddress(new String255('seller2@test.com'))
        );
        $sellerUniversity = TestUniversityFactory::create($sellerUser->universityId, new String255('売り手大学2'));
        $this->universityRepository->insert($sellerUniversity);
        $sellerFaculty = TestFacultyFactory::create($sellerUser->facultyId, new String255('売り手学部2'), $sellerUser->universityId);
        $this->facultyRepository->insert($sellerFaculty);
        $this->userRepository->insertWithLoginId($sellerUser, $sellerUser->mailAddress);

        $buyerUser = TestUserFactory::create(
            mailAddress: new MailAddress(new String255('buyer2@test.com'))
        );
        $buyerUniversity = TestUniversityFactory::create($buyerUser->universityId, new String255('買い手大学2'));
        $this->universityRepository->insert($buyerUniversity);
        $buyerFaculty = TestFacultyFactory::create($buyerUser->facultyId, new String255('買い手学部2'), $buyerUser->universityId);
        $this->facultyRepository->insert($buyerFaculty);
        $this->userRepository->insertWithLoginId($buyerUser, $buyerUser->mailAddress);

        // textbook用のデータを作成
        $textbook = TestTextbookFactory::create(
            universityId: $sellerUser->universityId,
            facultyId: $sellerUser->facultyId
        );
        $this->textbookRepository->insert($textbook);

        $inputDeal = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook->id
        );
        $this->dealRepository->insert($inputDeal);

        // 同じIDのDealEventを作成
        $inputDealEvent1 = TestDealEventFactory::create(
            userId: $inputUser->id,
            dealId: $inputDeal->id
        );
        $inputDealEvent2 = TestDealEventFactory::create(
            id: $inputDealEvent1->id,
            userId: $inputUser->id,
            dealId: $inputDeal->id
        );
        $this->dealEventRepository->insert($inputDealEvent1);

        //when
        //then
        $this->expectException(DuplicateKeyException::class);
        $this->expectExceptionMessage('取引イベントが重複しています。');
        $this->dealEventRepository->insert($inputDealEvent2);
    }
}
