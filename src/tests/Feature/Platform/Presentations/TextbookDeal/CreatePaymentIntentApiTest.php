<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\TextbookDeal;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Models\User;
use App\Platform\Domains\Deal\Deal;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Shared\Address\Address;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\Name\Name;
use App\Platform\Domains\Shared\PostCode\PostCode;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\Textbook\ConditionType;
use App\Platform\Domains\Textbook\Price;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Domains\User\UserId;
use App\Platform\Infrastructures\Deal\DealRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Image\ImageRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Image\TestImageFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class CreatePaymentIntentApiTest extends TestCase
{
    use DatabaseTransactions, ApiPreLoginTrait;

    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;
    private DealRepository $dealRepository;
    private ImageRepository $imageRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
        $this->dealRepository = new DealRepository();
        $this->imageRepository = new ImageRepository();
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthenticationException
     */
    public function test_出品者が購入しようとした場合は認可エラー(): void
    {
        // given: 出品者を作成し、認証
        $seller = $this->prepareUserWithFacultyAndUniversity(
            new String255('password12345'),
            new MailAddress(new String255('seller@example.com'))
        );

        // 教科書と取引を作成
        $textbook = $this->createTextbookWithDeal($seller->id);

        // 出品者として認証
        $token = $this->userRepository->createToken(
            new MailAddress(new String255('seller@example.com')),
            new String255('password12345')
        );

        $url = route('textbooks.deals.payment-intent.store', ['textbookId' => $textbook->id->value]);

        // when: 出品者が自分の教科書の支払いインテントを作成しようとする
        $response = $this->postJson($url, [], [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then: 認可エラー
        $response->assertForbidden();
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthenticationException
     */
    public function test_ステータスが出品中以外の場合はエラー(): void
    {
        // given: 出品者と購入者を作成
        $seller = $this->prepareUserWithFacultyAndUniversity(
            new String255('password12345'),
            new MailAddress(new String255('seller@example.com'))
        );

        $buyer = $this->prepareCompleteBuyerUser();

        // 教科書と取引を作成（出品中以外のステータス）
        $textbook = $this->createTextbookWithDeal($seller->id, DealStatus::Purchased);

        // 購入者として認証
        $token = $this->userRepository->createToken(
            new MailAddress(new String255('buyer@example.com')),
            new String255('password12345')
        );

        $url = route('textbooks.deals.payment-intent.store', ['textbookId' => $textbook->id->value]);

        // when: 取引中の教科書の支払いインテントを作成しようとする
        $response = $this->postJson($url, [], [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then: 認可エラー
        $response->assertForbidden();
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthenticationException
     */
    public function test_正常系_支払いインテントが正常に作成される(): void
    {
        // given: 出品者を作成
        $seller = $this->prepareUserWithFacultyAndUniversityFixedId(
            new UserId('c3c0d4e9-7f8d-4a4a-92e1-4eacbb1d1f56'),
            new String255('password12345'),
            new MailAddress(new String255('seller@example.com'))
        );

        // 完全な購入者を作成
        $buyer = $this->prepareCompleteBuyerUser();

        // 教科書と取引を作成
        $textbook = $this->createTextbookWithDeal($seller->id);

        // 購入者として認証
        $token = $this->userRepository->createToken(
            new MailAddress(new String255('buyer@example.com')),
            new String255('password12345')
        );

        $url = route('textbooks.deals.payment-intent.store', ['textbookId' => $textbook->id->value]);

        // when: 購入者が支払いインテントを作成
        $response = $this->postJson($url, [], [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then: 成功してclient_secretが返される
        $response->assertOk()->assertJsonStructure([
            'client_secret'
        ]);

        $this->assertNotEmpty($response->json('client_secret'));
    }

    private function createTextbookWithDeal(string $sellerId, DealStatus $dealStatus = DealStatus::Listing)
    {
        $university = TestUniversityFactory::create(
            id: new UniversityId('de23bfca-fb58-4802-8eb3-270ba67815a6'),
            name: new String255('テスト大学')
        );
        $this->universityRepository->insert($university);

        $faculty = TestFacultyFactory::create(
            id: new FacultyId('e0d11e80-77ad-4b4c-b539-0a6118ad36bf'),
            name: new String255('テスト学部'),
            universityId: $university->id
        );
        $this->facultyRepository->insert($faculty);

        $image = TestImageFactory::create(
            path: new String255('/path/to/image.jpg'),
            type: new String255('jpg')
        );
        $this->imageRepository->insert($image);

        $textbook = TestTextbookFactory::create(
            id: new TextbookId('e1f6d6cb-4f7a-4970-8b2a-9c1c1c3c4a78'),
            name: new String255('テスト教科書'),
            price: new Price(1500),
            description: new Text('これはテスト用の教科書です。'),
            universityId: $university->id,
            facultyId: $faculty->id,
        );
        $this->textbookRepository->insert($textbook);

        // 取引を作成
        $deal = Deal::create(
            new Seller(new UserId($sellerId)),
            null, // buyer
            $textbook->id,
            $dealStatus
        );

        try {
            $this->dealRepository->insert($deal);
        } catch (\Exception $e) {
            throw $e;
        }

        return $textbook;
    }

    private function prepareCompleteBuyerUser()
    {
        $buyerUser = TestUserFactory::create(
            id: new UserId('9b5f1c42-96c0-4a1c-8f28-13c78d4a3d6a'),
            name: new Name('購入者太郎'),
            password: new String255('password12345'),
            postCode: new PostCode(new String255('1234567')),
            address: new Address(new String255('東京都渋谷区1-1-1')),
            mailAddress: new MailAddress(new String255('buyer@example.com'))
        );

        $university = TestUniversityFactory::create(
            $buyerUser->universityId,
            new String255('購入者大学')
        );
        $this->universityRepository->insert($university);

        $faculty = TestFacultyFactory::create(
            $buyerUser->facultyId,
            new String255('購入者学部'),
            $buyerUser->universityId
        );
        $this->facultyRepository->insert($faculty);

        $this->userRepository->insertWithLoginId($buyerUser, $buyerUser->mailAddress);

        return $buyerUser;
    }

    /**
     * 固定IDでユーザー・大学・学部をDBに登録し、Eloquentモデルを返す
     */
    private function prepareUserWithFacultyAndUniversityFixedId(
        UserId $userId,
        String255 $password,
        MailAddress $mailAddress
    ) {
        $inputUser = TestUserFactory::create(
            id: $userId,
            password: $password,
            mailAddress: $mailAddress,
        );
        $university = TestUniversityFactory::create(
            $inputUser->universityId,
            new String255('テスト大学')
        );
        $this->universityRepository->insert($university);
        $faculty = TestFacultyFactory::create(
            $inputUser->facultyId,
            new String255('テスト学部'),
            $inputUser->universityId
        );
        $this->facultyRepository->insert($faculty);
        $this->userRepository->insertWithLoginId($inputUser, $inputUser->mailAddress);
        return User::find($inputUser->id->value);
    }
}
