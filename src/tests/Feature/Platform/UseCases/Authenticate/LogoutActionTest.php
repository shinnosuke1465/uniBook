<?php

declare(strict_types=1);

namespace Feature\Platform\UseCases\Authenticate;

use App\Exceptions\IllegalUserException;
use App\Platform\Infrastructures\User\UserRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Presentations\Authenticate\Requests\LogoutRequest;
use App\Platform\UseCases\Authenticate\LogoutAction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Feature\Api\ApiPreLoginTrait;

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

    public function test_ログアウトが成功すること(): void
    {
        //given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate('test@example.com', 'password12345');
        //リクエストパラメータをセット
        $request = LogoutRequest::create(
            '',
            'POST',
        );

        //when
        app()->bind(LogoutAction::class, fn() => new LogoutAction(
            new UserRepository(),
        ));
        (app()->make(LogoutAction::class)($request));

        //then
        $this->expectException(IllegalUserException::class);
        $this->expectExceptionMessage('認証済みユーザー情報が取得できませんでした。');
        $this->userRepository->getAuthenticatedUser();
    }
}
