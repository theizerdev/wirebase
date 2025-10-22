<?php

namespace App\Providers;

use App\Models\NivelEducativo;
use App\Policies\NivelEducativoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        NivelEducativo::class => NivelEducativoPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
