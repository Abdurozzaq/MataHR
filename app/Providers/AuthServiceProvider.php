<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\EmergencyContact;
use App\Policies\EmergencyContactPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        EmergencyContact::class => EmergencyContactPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
