<?php

namespace App\Providers;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

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
        Model::preventLazyLoading(! $this->app->isProduction());

        View::composer('layouts.app', function (ViewInstance $view): void {
            $profile = $this->app->bound(Profile::class) ? $this->app->make(Profile::class) : null;

            $view->with([
                'currentProfile' => $profile,
                'watchlistIds' => $profile?->watchlistContents()->pluck('contents.id')->all() ?? [],
            ]);
        });
    }
}
