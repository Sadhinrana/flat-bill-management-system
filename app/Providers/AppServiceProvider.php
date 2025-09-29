<?php

namespace App\Providers;

use App\Repositories\BillRepositoryInterface;
use App\Repositories\BillCategoryRepositoryInterface;
use App\Repositories\BuildingRepositoryInterface;
use App\Repositories\FlatRepositoryInterface;
use App\Repositories\TenantRepositoryInterface;
use App\Repositories\Eloquent\EloquentBillRepository;
use App\Repositories\Eloquent\EloquentBillCategoryRepository;
use App\Repositories\Eloquent\EloquentBuildingRepository;
use App\Repositories\Eloquent\EloquentFlatRepository;
use App\Repositories\Eloquent\EloquentTenantRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BillRepositoryInterface::class, EloquentBillRepository::class);
        $this->app->bind(BuildingRepositoryInterface::class, EloquentBuildingRepository::class);
        $this->app->bind(FlatRepositoryInterface::class, EloquentFlatRepository::class);
        $this->app->bind(TenantRepositoryInterface::class, EloquentTenantRepository::class);
        $this->app->bind(BillCategoryRepositoryInterface::class, EloquentBillCategoryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
