<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Brand' => 'App\Policies\BrandPolicy',
        'App\Category' => 'App\Policies\CategoryPolicy',
        'App\Campaign' => 'App\Policies\CampaignPolicy',
        'App\Comment' => 'App\Policies\CommentPolicy',
        'App\Order' => 'App\Policies\OrderPolicy',
        'App\ProductAttribute' => 'App\Policies\ProductAttributePolicy',
        'App\Product' => 'App\Policies\ProductPolicy',
        'App\ProductType' => 'App\Policies\ProductTypePolicy',
        'App\Rate' => 'App\Policies\RatePolicy',
        'App\Role' => 'App\Policies\RolePolicy',
        'App\User' => 'App\Policies\UserPolicy',
        'App\DiscountCode' => 'App\Policies\DiscountCodePolicy'
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('storage.store', 'App\Policies\StoragePolicy@store');
        Gate::define('storage.destroy', 'App\Policies\StoragePolicy@destroy');
    }
}
