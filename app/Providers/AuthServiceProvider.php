<?php

namespace App\Providers;

use App\Models\SEGURIDAD_ERP\AuthCode;
use App\Models\SEGURIDAD_ERP\Client;
use App\Models\SEGURIDAD_ERP\PersonalAccessClient;
use App\Models\SEGURIDAD_ERP\Token;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $model = config()->get('auth.providers.users.model');

        Auth::provider('igh', function ($app, array $config) use ($model) {
            return new IghUserProvider($model);
        });

        Passport::routes();

        Passport::useAuthCodeModel(AuthCode::class);
        Passport::useClientModel(Client::class);
        Passport::usePersonalAccessClientModel(PersonalAccessClient::class);
        Passport::useTokenModel(Token::class);
    }
}
