<?php

declare(strict_types=1);

namespace Feature\Platform\Presentations\Like;

use App\Exceptions\DuplicateKeyException;
use App\Exceptions\NotFoundException;
use App\Models\Like;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\Textbook\Price;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Image\ImageRepository;
use App\Platform\Infrastructures\Like\LikeRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use DomainException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\ApiPreLoginTrait;
use Tests\TestCase;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\Image\TestImageFactory;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;


class CreateLikeApiTest extends TestCase
{
    use DatabaseTransactions, ApiPreLoginTrait;

    private UserRepository $userRepository;
    private TextbookRepository $textbookRepository;
    private LikeRepository $likeRepository;
    private UniversityRepository $universityRepository;
    private FacultyRepository $facultyRepository;
    private ImageRepository $imageRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->textbookRepository = new TextbookRepository();
        $this->likeRepository = new LikeRepository();
        $this->universityRepository = new UniversityRepository();
        $this->facultyRepository = new FacultyRepository();
        $this->imageRepository = new ImageRepository();
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     * @throws NotFoundException
     * @throws AuthenticationException
     */
    public function test_認証済みユーザーが教科書にいいねを作成できること(): void
    {
        //given
        $this->prepareUserWithFacultyAndUniversity();

        // トークンを生成
        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

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

        $image = TestImageFactory::create(path: new String255('/path/to/image.jpg'), type: new String255('jpg'));
        $this->imageRepository->insert($image);

        $textbook = TestTextbookFactory::create(
            name: new String255('テスト教科書'),
            price: new Price(1500),
            description: new Text('これはテスト用の教科書です。'),
            universityId: $university->id,
            facultyId: $faculty->id,
        );
        $this->textbookRepository->insert($textbook);

        $url = route('likes.store', ['textbookId' => $textbook->id->value]);
        $requestData = []; // いいねはリクエストボディが空

        //when
        $response = $this->postJson($url, $requestData, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        //then
        $response->assertNoContent();

        // データベースから作成されたいいねを確認
        $likes = Like::all();
        $this->assertCount(1, $likes);

        $createdLike = $likes->first();
        $this->assertEquals($textbook->id->value, $createdLike->textbook_id);
        $this->assertNotNull($createdLike->user_id);
    }

    /**
     * @throws DomainException
     * @throws DuplicateKeyException
     * @throws NotFoundException
     * @throws AuthenticationException
     */
    public function test_存在しない教科書にいいねを作成しようとした場合404エラーが返ること(): void
    {
        //given
        $this->prepareUserWithFacultyAndUniversity();

        // トークンを生成
        $token = $this->userRepository->createToken(
            new MailAddress(
                new String255('test@example.com')
            ),
            new String255('password12345')
        );

        $nonExistentTextbookId = '11111111-1111-1111-1111-111111111111';
        $url = route('likes.store', ['textbookId' => $nonExistentTextbookId]);
        $requestData = [];

        //when
        $response = $this->postJson($url, $requestData, [
            'Authorization' => 'Bearer ' . $token->token,
        ]);

        //then
        $response->assertNotFound();
    }

}
