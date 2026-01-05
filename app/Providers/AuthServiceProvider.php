<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gates
        Gate::define('view-library', fn($user) => in_array($user->role, ['student','teacher','librarian']));
        Gate::define('upload-files', fn($user) => $user->role === 'teacher');
        Gate::define('manage-users', fn($user) => $user->role === 'librarian');
        Gate::define('borrow-book', fn($user) => $user->role === 'student');
        Gate::define('return-book', fn($user) => $user->role === 'librarian');

        Gate::define('manage-books', fn($user) => $user->role === 'librarian');

        Gate::define('borrow-books', fn ($user) => in_array($user->role, ['student', 'teacher']));
        Gate::define('manage-borrows', fn ($user) => $user->role === 'librarian');

        // Learning Files
        Gate::define('upload-files', fn($user) => in_array($user->role, ['teacher', 'librarian']));
        Gate::define('delete-learning-files', fn($user) => $user->role === 'librarian');
    }
}
