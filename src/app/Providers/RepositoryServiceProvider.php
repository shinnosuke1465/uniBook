<?php

declare(strict_types=1);

namespace App\Providers;

use App\Packages\Infrastructures\Shared\Transaction\Transaction;
use App\Platform\Domains\Comment\CommentRepositoryInterface;
use App\Platform\Domains\Deal\DealRepositoryInterface;
use App\Platform\Domains\DealEvent\DealEventRepositoryInterface;
use App\Platform\Domains\Faculty\FacultyRepositoryInterface;
use App\Platform\Domains\Image\ImageRepositoryInterface;
use App\Platform\Domains\Image\ImageStorageServiceInterface;
use App\Platform\Domains\Like\LikeRepositoryInterface;
use App\Platform\Domains\Textbook\TextbookRepositoryInterface;
use App\Platform\Domains\University\UniversityRepositoryInterface;
use App\Platform\Domains\User\AuthenticateToken\AuthenticateTokenRepositoryInterface;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\Infrastructures\Comment\CommentRepository;
use App\Platform\Infrastructures\Deal\DealRepository;
use App\Platform\Infrastructures\DealEvent\DealEventRepository;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Image\ImageRepository;
use App\Platform\Infrastructures\Image\LocalImageStorageService;
use App\Platform\Infrastructures\Image\S3ImageStorageService;
use App\Platform\Infrastructures\Like\LikeRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use Illuminate\Support\ServiceProvider;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TransactionInterface::class,
            Transaction::class
        );

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

        // 環境別にImageStorageServiceを切り替え
        if ($this->app->environment('production')) {
            $this->app->bind(
                ImageStorageServiceInterface::class,
                S3ImageStorageService::class
            );
        } else {
            $this->app->bind(
                ImageStorageServiceInterface::class,
                LocalImageStorageService::class
            );
        }

        $this->app->bind(
            TextbookRepositoryInterface::class,
            TextbookRepository::class,
        );

        $this->app->bind(
            DealRepositoryInterface::class,
            DealRepository::class,
        );

        $this->app->bind(
            DealEventRepositoryInterface::class,
            DealEventRepository::class,
        );

        $this->app->bind(
            CommentRepositoryInterface::class,
            CommentRepository::class,
        );

        $this->app->bind(
            LikeRepositoryInterface::class,
            LikeRepository::class,
        );
    }
}
