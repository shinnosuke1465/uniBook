<?php

declare(strict_types=1);

namespace Feature\Platform\UseCases\AuthenticateToken;

use App\Exceptions\IllegalUserException;
use App\Exceptions\InvalidValueException;
use App\Platform\Infrastructures\User\UserRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Presentations\AuthenticateToken\Requests\CreateTokenRequest;
use App\Platform\UseCases\Authenticate\CreateTokenAction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Feature\Api\ApiPreLoginTrait;
use Throwable;

class AuthenticateTokenActionTest extends TestCase
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
     * @throws InvalidValueException
     * @throws Throwable
     */
    public function test_認証トークンが正常に発行されること(): void
    {
        //given
        $this->prepareUserWithFacultyAndUniversity();
        $this->authenticate('test@example.com', 'password12345');
        $request = CreateTokenRequest::create(
            '',
            'POST',
            [
                'mail_address' => 'test@example.com',
                'password' => 'password12345',
            ]
        );

        //when
        app()->bind(CreateTokenAction::class, fn() => new CreateTokenAction(
            new UserRepository(),
        ));
        $response = app()->make(CreateTokenAction::class)($request);

        //then
        $this->assertNotNull($response);
        $this->assertNotEmpty($response->token);
        $this->assertIsString($response->token);
    }
}
