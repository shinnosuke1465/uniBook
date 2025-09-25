<?php

declare(strict_types=1);

namespace Feature\Platform\Infrastructures\Deal;

use App\Exceptions\DuplicateKeyException;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Infrastructures\Deal\DealRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Deal\TestDealFactory;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Shared\MailAddress\MailAddress;

class DealRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private DealRepository $dealRepository;
    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dealRepository = new DealRepository();
        $this->userRepository = new UserRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
    }

    public function test_insertで取引を登録できること(): void
    {
        //given
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

        $inputDeal = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook->id
        );

        //when
        $this->dealRepository->insert($inputDeal);

        //then
        $this->assertDatabaseHas('deals', [
            'id' => $inputDeal->id->value,
            'seller_id' => $inputDeal->seller->userId->value,
            'buyer_id' => $inputDeal->buyer->userId->value,
            'textbook_id' => $inputDeal->textbookId->value,
            'deal_status' => $inputDeal->dealStatus->value,
        ]);
    }

    public function test_insertで同じIDの取引を登録した場合DuplicateKeyExceptionが発生すること(): void
    {
        //given
        // seller用のユーザーを作成
        $sellerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('seller2@test.com')));
        $sellerUniversity = TestUniversityFactory::create($sellerUser->universityId, new String255('売り手大学2'));
        $this->universityRepository->insert($sellerUniversity);
        $sellerFaculty = TestFacultyFactory::create($sellerUser->facultyId, new String255('売り手学部2'), $sellerUser->universityId);
        $this->facultyRepository->insert($sellerFaculty);
        $this->userRepository->insertWithLoginId($sellerUser, $sellerUser->mailAddress);

        // buyer用のユーザーを作成
        $buyerUser = TestUserFactory::create(mailAddress: new MailAddress(new String255('buyer2@test.com')));
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

        $inputDeal1 = TestDealFactory::create(
            seller: new Seller($sellerUser->id),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook->id
        );
        $inputDeal2 = TestDealFactory::create(
            id: $inputDeal1->id,
            seller: new Seller($sellerUser->id),
            buyer: new Buyer($buyerUser->id),
            textbookId: $textbook->id
        );
        $this->dealRepository->insert($inputDeal1);

        //when
        //then
        $this->expectException(DuplicateKeyException::class);
        $this->expectExceptionMessage('取引が重複しています。');
        $this->dealRepository->insert($inputDeal2);
    }
}
