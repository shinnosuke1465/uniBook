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
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Image\ImageRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Image\TestImageFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class ReportDeliveryApiTest extends TestCase
{
    use DatabaseTransactions, ApiPreLoginTrait;

    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;
    private DealRepository $dealRepository;
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
        $this->dealEventRepository = new DealEventRepository();
        $this->imageRepository = new ImageRepository();
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws DuplicateKeyException
     */
    public function test_正常に配送報告ができること(): void
    {
        // given: 出品者と購入者を作成
        $seller = $this->prepareUserWithFacultyAndUniversity(
            new String255('password12345'),
            new MailAddress(new String255('seller@example.com'))
        );

        $buyer = $this->prepareUserWithFacultyAndUniversity(
            new String255('password12345'),
            new MailAddress(new String255('buyer@example.com'))
        );

        // 購入済みの取引を作成
        $textbookId = $this->createTextbookWithPurchasedDeal($seller->id, $buyer->id);

        // 出品者として認証
        $token = $this->userRepository->createToken(
            new MailAddress(new String255('seller@example.com')),
            new String255('password12345')
        );

        $url = route('textbooks.deals.reportDelivery', ['textbookId' => $textbookId->value]);

        // when: 配送報告APIを呼び出す
        $response = $this->postJson($url, [], [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then: 成功ステータスを返す
        $response->assertNoContent();

        // 取引ステータスがShipping（配送中）に更新されていることを確認
        $this->assertDatabaseHas('deals', [
            'textbook_id' => $textbookId->value,
            'deal_status' => 'Shipping',
        ]);

        // 取引イベントが記録されていることを確認
        $deal = $this->dealRepository->findByTextbookId($textbookId);
        $this->assertDatabaseHas('deal_events', [
            'deal_id' => $deal->id->value,
            'event_type' => 'ReportDelivery',
            'actor_type' => 'seller',
            'user_id' => $seller->id,
        ]);
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws DuplicateKeyException
     */
    public function test_購入者が配送報告しようとすると403エラー(): void
    {
        // given: 出品者と購入者を作成
        $seller = $this->prepareUserWithFacultyAndUniversity(
            new String255('password12345'),
            new MailAddress(new String255('seller@example.com'))
        );

        $buyer = $this->prepareUserWithFacultyAndUniversity(
            new String255('password12345'),
            new MailAddress(new String255('buyer@example.com'))
        );

        // 購入済みの取引を作成
        $textbookId = $this->createTextbookWithPurchasedDeal($seller->id, $buyer->id);

        // 購入者として認証（配送報告権限なし）
        $token = $this->userRepository->createToken(
            new MailAddress(new String255('buyer@example.com')),
            new String255('password12345')
        );

        $url = route('textbooks.deals.reportDelivery', ['textbookId' => $textbookId->value]);

        // when: 購入者が配送報告APIを呼び出す
        $response = $this->postJson($url, [], [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then: 403エラー
        $response->assertForbidden();
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws DuplicateKeyException
     */
    public function test_取引ステータスがListing状態では配送報告できない(): void
    {
        // given: 出品者を作成
        $seller = $this->prepareUserWithFacultyAndUniversity(
            new String255('password12345'),
            new MailAddress(new String255('seller@example.com'))
        );

        // Listing状態の取引を作成
        $textbookId = $this->createTextbookWithDeal($seller->id);

        // 出品者として認証
        $token = $this->userRepository->createToken(
            new MailAddress(new String255('seller@example.com')),
            new String255('password12345')
        );

        $url = route('textbooks.deals.reportDelivery', ['textbookId' => $textbookId->value]);

        // when: Listing状態で配送報告APIを呼び出す
        $response = $this->postJson($url, [], [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        // then: 403エラー
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
     * @throws DomainException
     * @throws NotFoundException
     * @throws DuplicateKeyException
     */
    private function createTextbookWithPurchasedDeal(UserId $sellerId, UserId $buyerId): TextbookId
    {
        // 出品者の情報を取得
        $seller = $this->userRepository->findById($sellerId);
        if ($seller === null) {
            throw new NotFoundException('出品者が見つかりません。');
        }

        $image = TestImageFactory::create(
            path: new String255('/path/to/test_image2.jpg'),
            type: new String255('jpg')
        );
        $this->imageRepository->insert($image);

        $textbook = TestTextbookFactory::create(
            id: new TextbookId('e1f6d6cb-4f7a-4970-8b2a-9c1c1c3c4a79'),
            name: new String255('購入済み教科書'),
            price: new Price(2000),
            description: new Text('これは購入済みの教科書です。'),
            imageIdList: new ImageIdList([$image->id]),
            universityId: $seller->universityId,
            facultyId: $seller->facultyId,
            conditionType: ConditionType::NEW
        );
        $this->textbookRepository->insert($textbook);

        // 購入済みの取引を作成
        $deal = Deal::create(
            new Seller($sellerId),
            new Buyer($buyerId),
            $textbook->id,
            DealStatus::create('Purchased')
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
    private function prepareUserWithFacultyAndUniversity(
        String255 $password,
        MailAddress $mailAddress
    ): User {
        // ランダムなIDを生成して重複を避ける
        $universityId = new UniversityId(\Illuminate\Support\Str::uuid()->toString());
        $facultyId = new FacultyId(\Illuminate\Support\Str::uuid()->toString());

        // 大学を作成
        $university = TestUniversityFactory::create(
            id: $universityId,
            name: new String255('テスト大学')
        );
        $this->universityRepository->insert($university);

        // 学部を作成
        $faculty = TestFacultyFactory::create(
            id: $facultyId,
            name: new String255('テスト学部'),
            universityId: $university->id
        );
        $this->facultyRepository->insert($faculty);

        // ユーザーを作成
        $user = TestUserFactory::create(
            name: new Name('テストユーザー'),
            password: $password,
            postCode: new PostCode(new String255('1111111')),
            address: new Address(new String255('東京都渋谷区')),
            mailAddress: $mailAddress,
            facultyId: $faculty->id,
            universityId: $university->id
        );
        $this->userRepository->insertWithLoginId($user, $mailAddress);

        return $user;
    }
}
