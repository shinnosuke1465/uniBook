<?php

declare(strict_types=1);

namespace Feature\Platform\UseCases\Authenticate;

use App\Exceptions\IllegalUserException;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Infrastructures\User\UserRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Presentations\Authenticate\Requests\LogoutRequest;
use App\Platform\UseCases\Authenticate\LogoutAction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Feature\Api\ApiPreLoginTrait;
use Throwable;

class LogoutActionTest extends TestCase
{
    use ApiPreLoginTrait;
    use DatabaseTransactions;

    private UserRepository $userRepository;

    private UniversityRepository $universityRepository;

    private FacultyRepository $facultyRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->facultyRepository = new FacultyRepository();
        $this->universityRepository = new UniversityRepository();
    }

    /**
     * @throws IllegalUserException
     * @throws Throwable
     */
    public function test_ログアウトが成功すること(): void
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

        // トークンが存在することを確認
        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'authenticate_token',
            'token' => hash('sha256', explode('|', $token->token)[1])
        ]);

        // グローバルリクエストにBearerトークンを設定
        request()->headers->set('Authorization', 'Bearer ' . $token->token);

        $request = LogoutRequest::create('', 'POST');

        //when
        $logoutAction = new LogoutAction($this->userRepository);
        $logoutAction($request);

        //then
        // トークンが削除されていることを確認
        $this->assertDatabaseMissing('personal_access_tokens', [
            'name' => 'authenticate_token',
            'token' => hash('sha256', explode('|', $token->token)[1])
        ]);
    }

    /**
     * @throws IllegalUserException
     * @throws Throwable
     */
    public function test_トークンが存在しない場合ログアウトでエラーが発生すること(): void
    {
        //given
        $request = LogoutRequest::create('', 'POST');

        //when
        //then
        $this->expectException(IllegalUserException::class);
        $this->expectExceptionMessage('ログアウトに失敗しました。');

        $logoutAction = new LogoutAction($this->userRepository);
        $logoutAction($request);
    }

}
