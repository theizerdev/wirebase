<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Contracts\Repositories\UserRepositoryInterface;
use App\Infrastructure\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind repository interfaces to implementations
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        // Additional repository bindings can be added here
        // $this->app->bind(
        //     StudentRepositoryInterface::class,
        //     StudentRepository::class
        // );

        // $this->app->bind(
        //     EmpresaRepositoryInterface::class,
        //     EmpresaRepository::class
        // );

        // $this->app->bind(
        //     SucursalRepositoryInterface::class,
        //     SucursalRepository::class
        // );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Additional boot logic if needed
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            UserRepositoryInterface::class,
            // Add other repository interfaces here
        ];
    }
}
