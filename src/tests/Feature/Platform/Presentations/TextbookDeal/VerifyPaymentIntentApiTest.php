<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\TextbookDeal;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Exceptions\NotFoundException;
use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\Deal;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Image\ImageIdList;
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
use App\Platform\Domains\User\User;
use App\Platform\Domains\User\UserId;
use App\Platform\Infrastructures\Deal\DealRepository;
use App\Platform\Infrastructures\DealEvent\DealEventRepository;
use App\Platform\Infrastructures\DealRoom\DealRoomRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Image\ImageRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Deal\TestDealFactory;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Image\TestImageFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class VerifyPaymentIntentApiTest extends TestCase
{
    use DatabaseTransactions, ApiPreLoginTrait;

    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;
    private DealRepository $dealRepository;
    private DealRoomRepository $dealRoomRepository;
    private DealEventRepository $dealEventRepository;
    private ImageRepository $imageRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
        $this->dealRepository = new DealRepository();
        $this->dealRoomRepository = new DealRoomRepository();
        $this->dealEventRepository = new DealEventRepository();
        $this->imageRepository = new ImageRepository();
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws DuplicateKeyException
     */
    public function test_正常に支払い確認ができること(): void
    {
        // given: 出品者と購入者を作成
        $seller = $this->prepareUserWithFacultyAndUniversity(
            new String255('password12345'),
            new MailAddress(new String255('seller@example.com'))
        );

        $buyer = $this->prepareUserWithFacultyAndUniversityFixedId(
            new UserId('9feaadb4-b5a9-4c37-84d6-08f7b1a32a8b'),
            new MailAddress(new String255('buyer@example.com'))
        );

        // 教科書と取引を作成
        $textbookId = $this->createTextbookWithDeal(new UserId($seller->id));

        // 購入者として認証
        $token = $this->userRepository->createToken(
            new MailAddress(new String255('buyer@example.com')),
            new String255('password123')
        );

        $url = route('textbooks.deals.payment-intent.verify.store', ['textbookId' => $textbookId->value]);

        // when: 支払い確認APIを呼び出す
        $response = $this->postJson($url, [
            'payment_intent_id' => 'pi_123_secret_123'
        ], [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then: 成功ステータスを返す
        $response->assertNoContent();

        // 取引ステータスが購入済みに更新されていることを確認
        $this->assertDatabaseHas('deals', [
            'textbook_id' => $textbookId->value,
            'deal_status' => 'Purchased',
            'buyer_id' => $buyer->id->value,
        ]);

        // 取引ルームが作成されていることを確認
        $deal = $this->dealRepository->findByTextbookId($textbookId);
        $this->assertDatabaseHas('deal_rooms', [
            'deal_id' => $deal->id->value,
        ]);

        // 取引イベントが記録されていることを確認
        $this->assertDatabaseHas('deal_events', [
            'deal_id' => $deal->id->value,
            'event_type' => 'Purchase',
            'actor_type' => 'buyer',
            'user_id' => $buyer->id->value,
        ]);
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
        $textbook = $this->createTextbookWithDeal(new UserId($seller->id));

        // 出品者として認証
        $token = $this->userRepository->createToken(
            new MailAddress(new String255('seller@example.com')),
            new String255('password12345')
        );

        $url = route('textbooks.deals.payment-intent.verify.store', ['textbookId' => $textbook->value]);

        // when: 出品者が自分の教科書の支払い確認をしようとする
        $response = $this->postJson($url, [
            'payment_intent_id' => 'pi_test_123456'
        ], [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then: 認可エラー
        $response->assertForbidden();
    }


    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws DuplicateKeyException
     */
    private function createTextbookWithDeal(UserId $sellerId): TextbookId
    {
        // 出品者の情報を取得
        $seller = $this->userRepository->findById($sellerId);
        if ($seller === null) {
            throw new NotFoundException('出品者が見つかりません。');
        }

        $image = TestImageFactory::create(
            path: new String255('/path/to/test_image.jpg'),
            type: new String255('jpg')
        );
        $this->imageRepository->insert($image);

        $textbook = TestTextbookFactory::create(
            id: new TextbookId('e1f6d6cb-4f7a-4970-8b2a-9c1c1c3c4a78'),
            name: new String255('テスト教科書'),
            price: new Price(1500),
            description: new Text('これはテスト用の教科書です。'),
            imageIdList: new ImageIdList([$image->id]),
            universityId: $seller->universityId,
            facultyId: $seller->facultyId,
            conditionType: ConditionType::SLIGHT_DAMAGE
        );
        $this->textbookRepository->insert($textbook);

        // 取引を作成（Listing状態）
        $deal = Deal::create(
            new Seller($sellerId),
            null,
            $textbook->id,
            DealStatus::create('Listing')
        );
        $this->dealRepository->insert($deal);

        return $textbook->id;
    }

    /**
     * @throws NotFoundException
     * @throws DomainException
     * @throws AuthenticationException
     * @throws DuplicateKeyException
     */
    private function prepareUserWithFacultyAndUniversityFixedId(
        UserId $userId,
        MailAddress $mailAddress
    ): User {
        // 大学を作成
        $university = TestUniversityFactory::create(
            id: new UniversityId('f12cbb68-90a1-4c2f-9542-b8c9f2a342e0'),
            name: new String255('テスト大学')
        );
        $this->universityRepository->insert($university);

        // 学部を作成
        $faculty = TestFacultyFactory::create(
            id: new FacultyId('0d2c3d12-bf4d-4f41-8b13-6bcad3ff3ab4'),
            name: new String255('テスト学部'),
            universityId: $university->id
        );
        $this->facultyRepository->insert($faculty);

        // ユーザーを作成
        $user = TestUserFactory::create(
            id: $userId,
            name: new Name('テストユーザー'),
            postCode: new PostCode(new String255('1111111')),
            address: new Address(new String255('東京都渋谷区')),
            mailAddress: $mailAddress,
            facultyId: $faculty->id,
            universityId: $university->id
        );
        $this->userRepository->insertWithLoginId($user, $mailAddress);

        return $user;
    }

    /**
     * @throws NotFoundException
     * @throws DomainException
     * @throws AuthenticationException
     * @throws DuplicateKeyException
     */
    private function prepareUserWithFacultyAndUniversityNoAddress(
        String255 $password,
        MailAddress $mailAddress
    ): \App\Platform\Domains\User\User {
        // 大学を作成
        $university = TestUniversityFactory::create(
            id: new UniversityId('bb0a7cb1-8d63-4eb4-89d9-9c1b3db5678'),
            name: new String255('テスト大学2')
        );
        $this->universityRepository->insert($university);

        // 学部を作成
        $faculty = TestFacultyFactory::create(
            id: new FacultyId('5d0a7cb1-8d63-4eb4-89d9-9c1b3d8765'),
            name: new String255('テスト学部2'),
            universityId: $university->id
        );
        $this->facultyRepository->insert($faculty);

        // ユーザーを作成（住所情報なし）
        $user = TestUserFactory::create(
            name: new Name('テストユーザー'),
            postCode: null,
            address: null,
            mailAddress: $mailAddress,
            facultyId: $faculty->id,
            universityId: $university->id
        );
        $this->userRepository->insertWithPassword($user, $password);

        return $user;
    }
}
