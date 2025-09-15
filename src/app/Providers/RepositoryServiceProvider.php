<?php

declare(strict_types=1);

namespace App\Providers;

use App\Platform\Domains\Faculty\FacultyRepositoryInterface;
use App\Platform\Domains\Image\ImageRepositoryInterface;
use App\Platform\Domains\University\UniversityRepositoryInterface;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Image\ImageRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UniversityRepositoryInterface::class,
            UniversityRepository::class,
        );

        $this->app->bind(
            FacultyRepositoryInterface::class,
            FacultyRepository::class,
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class,
        );

        $this->app->bind(
            ImageRepositoryInterface::class,
            ImageRepository::class,
        );
    }
}
