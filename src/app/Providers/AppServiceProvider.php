<?php

namespace App\Providers;

use App\Models\Faculty;
use App\Platform\Domains\Deal\DealRepositoryInterface;
use App\Platform\Domains\DealEvent\DealEventRepositoryInterface;
use App\Platform\Infrastructures\Deal\DealRepository;
use App\Platform\Infrastructures\DealEvent\DealEventRepository;
use App\Platform\Domains\Faculty\FacultyRepositoryInterface;
use App\Platform\Domains\Image\ImageRepositoryInterface;
use App\Platform\Domains\Textbook\TextbookRepositoryInterface;
use App\Platform\Domains\University\UniversityRepositoryInterface;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\Infrastructures\Faculty\FacultyRepository;
use App\Platform\Infrastructures\Image\ImageRepository;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\Infrastructures\University\UniversityRepository;
use App\Platform\Infrastructures\User\UserRepository;
use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use Illuminate\Support\ServiceProvider;
use App\Services\App\AppLogger;
use App\Packages\Infrastructures\Shared\Transaction\Transaction;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Logger
        $this->app->singleton('AppLog', function () {
            return new AppLogger();
        });

        // UserRepositoryInterfaceのバインド
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        // TransactionInterfaceのバインド
        $this->app->bind(
            TransactionInterface::class,
            Transaction::class // 実装クラス名に合わせて修正
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
            ImageRepositoryInterface::class,
            ImageRepository::class
        );

        $this->app->bind(
            TextbookRepositoryInterface::class,
            TextbookRepository::class
        );

        $this->app->bind(
            DealEventRepositoryInterface::class,
            DealEventRepository::class
        );

        $this->app->bind(
            DealRepositoryInterface::class,
            DealRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
