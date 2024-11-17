<?php

namespace App\Providers;

 use App\Models\Base;
 use App\Models\Order;
 use App\Models\Place;
 use App\Models\Tariff;
 use App\Policies\BasePolicy;
 use App\Policies\OrderPolicy;
 use App\Policies\PlacePolicy;
 use App\Policies\TariffPolicy;
 use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Option;
use App\Policies\OptionPolicy;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::policy(Option::class, OptionPolicy::class);
        Gate::policy(Tariff::class, TariffPolicy::class);
        Gate::policy(Place::class, PlacePolicy::class);
        Gate::policy(Base::class, BasePolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
    }
}
