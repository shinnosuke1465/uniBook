<?php

declare(strict_types=1);

namespace Feature\Platform\UseCases\User;

use App\Packages\Infrastructures\Shared\Transaction\Transaction;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Infrastructures\User\UserRepository;
use App\Platform\Presentations\User\Requests\CreateUserRequest;
use App\Platform\UseCases\Authenticate\CreateTokenAction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User as UserDB;
use App\Platform\UseCases\User\CreateUserAction;
use Tests\Unit\Platform\Domains\Faculty\TestFacultyFactory;
use Tests\Unit\Platform\Domains\University\TestUniversityFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\University\UniversityRepository;

class CreateUserActionTest extends TestCase
{
    use DatabaseTransactions;

    private UserRepository $userRepository;

    private UniversityRepository $universityRepository;

    private FacultyRepository $facultyRepository;

    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserDB::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->userRepository = new UserRepository();
        $this->facultyRepository = new FacultyRepository();
        $this->universityRepository = new UniversityRepository();
    }

    /**
     * @throws Throwable
     * @throws InvalidValueException
     * @throws UseCaseException
     * @throws Exception
     * @throws IllegalUserException
     */
    public function test_ユーザーが正常に作成されること(): void
    {
        // given
        $university = TestUniversityFactory::create(
            id: new UniversityId('00000000-0000-0000-0000-000000000000')
        );
        $this->universityRepository->insert($university);

        $faculty = TestFacultyFactory::create(
            id: new FacultyId('00000000-0000-0000-0000-000000000000'),
            universityId: new UniversityId('00000000-0000-0000-0000-000000000000'),
        );
        $this->facultyRepository->insert($faculty);

        $request = CreateUserRequest::create(
            '',
            'POST',
            [
                'name' => 'Test User',
                'password' => 'password12345',
                'post_code' => '1234567',
                'address' => 'テスト県テスト市テスト町1-2-3',
                'mail_address' => 'test@example.com',
                'image_id' => null,
                'university_id' => '00000000-0000-0000-0000-000000000000',
                'faculty_id' => '00000000-0000-0000-0000-000000000000',
            ]
        );

        // when
        app()->bind(CreateUserAction::class, fn() => new CreateUserAction(
            new UserRepository(),
            new Transaction(),
        ));
        $token = app()->make(CreateUserAction::class)($request);

        $response = $this->userRepository->findByMailAddress(new MailAddress(new String255('test@example.com')));

        // then
        $this->assertEquals('Test User', $response->name->name);
        $this->assertNotNull($token);
        $this->assertNotEmpty($token->token);
        $this->assertIsString($token->token);
    }
}
