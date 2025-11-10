<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::except([
        //     'submit'
        // ]);
        require_once app_path('Helpers/helpers.php');
        Auth::userResolver(function ($user) {
            if ($user) {
                // Eager load dengan query yang optimal
                $user->load([
                    'masterEmployee' => function($query) {
                        $query->select('employee_id', 'company_id');
                    },
                    'mainCompany' => function($query) {
                        $query->select('employee_id', 'company_id');
                    }
                ]);
            }
            return $user;
        });
    }
}
